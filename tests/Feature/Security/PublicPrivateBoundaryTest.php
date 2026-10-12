<?php

use App\Models\Document;
use App\Models\Endpoint;
use App\Models\Module;
use App\Models\System;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

it('keeps infrastructure and report routes behind login', function (): void {
    foreach (['/', '/reports', '/systems/1', '/endpoints/01ARZ3NDEKTSV4RRFFQ69G5FAV'] as $url) {
        $this->get($url)->assertRedirect(route('login'));
    }

    foreach (['/api/v1/search', '/api/v1/filters', '/api/v1/endpoints/01ARZ3NDEKTSV4RRFFQ69G5FAV', '/api/v1/reports/summary'] as $url) {
        $this->getJson($url)->assertUnauthorized();
    }

    $this->postJson('/api/v1/reports/generate-pdf')->assertUnauthorized();
});

it('allows the browser login session to read the protected catalog API', function (): void {
    $user = User::factory()->create(['password' => 'correct-password']);

    $this->post('/login', ['email' => $user->email, 'password' => 'correct-password'])->assertRedirect();
    $this->getJson('/api/v1/filters')->assertOk();
});

it('disables self registration while keeping login available', function (): void {
    $this->get('/register')->assertNotFound();
    $this->post('/register', [
        'name' => 'Uninvited', 'email' => 'uninvited@example.com',
        'password' => 'password', 'password_confirmation' => 'password',
    ])->assertNotFound();
    $this->assertDatabaseMissing('users', ['email' => 'uninvited@example.com']);
    $this->get('/login')->assertOk();
});

it('lists and serves only explicitly public documents to guests without private metadata', function (): void {
    Storage::fake('local');
    $system = System::factory()->create();
    $uploader = User::factory()->create(['email' => 'secret@example.test']);
    Storage::disk('local')->put('documents/public.pdf', '%PDF-public');
    Storage::disk('local')->put('documents/private.pdf', '%PDF-private');
    $public = Document::factory()->create([
        'system_id' => $system->id, 'uploaded_by' => $uploader->id,
        'is_public' => true, 'file_path' => 'documents/public.pdf',
    ]);
    $private = Document::factory()->create([
        'system_id' => $system->id, 'uploaded_by' => $uploader->id,
        'file_path' => 'documents/private.pdf',
    ]);

    expect($private->fresh()->is_public)->toBeFalse();
    $list = $this->getJson('/api/v1/documents')->assertOk()->assertJsonPath('meta.total', 1);
    $list->assertJsonPath('items.0.id', $public->id);
    $list->assertDontSee('file_path')->assertDontSee('secret@example.test');
    $this->getJson('/api/v1/documents')->assertOk()->assertJsonPath('meta.total', 1);
    $this->getJson('/api/v1/documents/'.$private->id)->assertNotFound();
    $this->getJson('/api/v1/documents/'.$private->id.'/file')->assertNotFound();
    $this->getJson('/api/v1/documents/'.$public->id)->assertOk()->assertDontSee('file_path')->assertDontSee('secret@example.test');
    $this->get('/documents/'.$public->id.'/file')->assertOk()->assertSee('%PDF-public');
    $this->get('/documents/'.$private->id.'/file')->assertNotFound();
});

it('does not accept private catalog filters from anonymous document readers', function (): void {
    $system = System::factory()->create();
    $module = Module::factory()->create(['system_id' => $system->id]);
    $endpoint = Endpoint::factory()->create(['module_id' => $module->id]);
    Document::factory()->create([
        'system_id' => $system->id,
        'module_id' => $module->id,
        'endpoint_id' => $endpoint->id,
        'is_public' => true,
    ]);

    foreach (['system_id' => $system->id, 'module_id' => $module->id, 'endpoint_id' => $endpoint->id] as $filter => $id) {
        $existing = $this->getJson('/api/v1/documents?'.$filter.'='.$id);
        $missing = $this->getJson('/api/v1/documents?'.$filter.'='.($id + 100000));

        $existing->assertUnprocessable()->assertJsonValidationErrors($filter);
        $missing->assertUnprocessable()->assertJsonValidationErrors($filter);
        expect($existing->json())->toBe($missing->json());
    }

    $this->actingAs(User::factory()->create())
        ->getJson('/api/v1/documents?system_id='.($system->id + 100000))
        ->assertUnprocessable()
        ->assertJsonValidationErrors('system_id');
});

it('allows an authorized intranet user to access private documents', function (): void {
    Storage::fake('local');
    Permission::findOrCreate('document.view', 'web');
    $user = User::factory()->create();
    $user->givePermissionTo('document.view');
    $document = Document::factory()->create(['uploaded_by' => $user->id, 'file_path' => 'documents/secret.pdf']);
    Storage::disk('local')->put($document->file_path, '%PDF-secret');

    $this->actingAs($user)->getJson('/api/v1/documents/'.$document->id)->assertOk();
    $this->actingAs($user)->get('/documents/'.$document->id.'/file')->assertOk()->assertSee('%PDF-secret');
});

it('resolves optional bearer identity for private documents and returns token-friendly file links', function (): void {
    Storage::fake('local');
    Permission::findOrCreate('document.view', 'web');
    $user = User::factory()->create();
    $user->givePermissionTo('document.view');
    $document = Document::factory()->create(['uploaded_by' => $user->id, 'file_path' => 'documents/token.pdf']);
    Storage::disk('local')->put($document->file_path, '%PDF-token');
    $headers = ['Authorization' => 'Bearer '.$user->createToken('read-documents')->plainTextToken];

    $this->withHeaders($headers)->getJson('/api/v1/documents')->assertOk()->assertJsonPath('meta.total', 1)
        ->assertJsonPath('items.0.view_url', route('api.documents.file', $document));
    $this->withHeaders($headers)->getJson('/api/v1/documents/'.$document->id)->assertOk()
        ->assertJsonPath('item.id', $document->id);
    $this->withHeaders($headers)->get('/api/v1/documents/'.$document->id.'/file')->assertOk()->assertSee('%PDF-token');
    $this->withHeader('Authorization', '')->getJson('/api/v1/documents/'.$document->id)->assertNotFound();
});

it('stores new pdfs on a non-public disk and makes visibility opt-in', function (): void {
    Storage::fake('local');
    Storage::fake('public');
    Permission::findOrCreate('document.manage', 'web');
    $user = User::factory()->create();
    $user->givePermissionTo('document.manage');
    $system = System::factory()->create();

    $this->actingAs($user)->postJson('/api/v1/documents', [
        'system_id' => $system->id, 'title' => 'Internal manual', 'type' => 'manual',
        'file' => UploadedFile::fake()->create('manual.pdf', 10, 'application/pdf'),
    ])->assertCreated();

    $document = Document::query()->sole();
    expect($document->is_public)->toBeFalse();
    Storage::disk('local')->assertExists($document->file_path);
    Storage::disk('public')->assertMissing($document->file_path);
});

it('serves pdf uploads with a trusted content type even when client mime is spoofed', function (): void {
    Storage::fake('local');
    Permission::findOrCreate('document.manage', 'web');
    Permission::findOrCreate('document.view', 'web');
    $user = User::factory()->create();
    $user->givePermissionTo(['document.manage', 'document.view']);
    $system = System::factory()->create();
    $pdf = UploadedFile::fake()->createWithContent('manual.pdf', "%PDF-1.4\n1 0 obj\n<<>>\nendobj\n%%EOF");
    $spoofed = new UploadedFile($pdf->getPathname(), 'manual.pdf', 'text/html', null, true);

    $this->actingAs($user)->postJson('/api/v1/documents', [
        'system_id' => $system->id, 'title' => 'Manual', 'type' => 'manual', 'file' => $spoofed,
    ])->assertCreated();

    $document = Document::query()->sole();
    expect($document->mime_type)->toBe('application/pdf');
    $this->get('/documents/'.$document->id.'/file')->assertOk()
        ->assertHeader('content-type', 'application/pdf')
        ->assertHeader('x-content-type-options', 'nosniff');
});

it('ignores unsafe mime metadata on legacy public files', function (): void {
    Storage::fake('local');
    $document = Document::factory()->create([
        'is_public' => true,
        'mime_type' => 'text/html',
        'file_path' => 'documents/legacy-unsafe.pdf',
    ]);
    Storage::disk('local')->put($document->file_path, '%PDF-1.4 legacy');

    $this->get('/documents/'.$document->id.'/file')->assertOk()
        ->assertHeader('content-type', 'application/pdf')
        ->assertHeader('x-content-type-options', 'nosniff');
});

it('lets an admin publish a pdf but rejects publication by an editor', function (): void {
    Storage::fake('local');
    Permission::findOrCreate('document.manage', 'web');
    $adminRole = Role::findOrCreate('admin', 'web');
    $editorRole = Role::findOrCreate('editor', 'web');
    $adminRole->givePermissionTo('document.manage');
    $editorRole->givePermissionTo('document.manage');
    $admin = User::factory()->create();
    $admin->assignRole($adminRole);
    $editor = User::factory()->create();
    $editor->assignRole($editorRole);
    $system = System::factory()->create();

    $this->actingAs($editor)->postJson('/api/v1/documents', [
        'system_id' => $system->id, 'title' => 'Not approved', 'type' => 'manual',
        'is_public' => true,
        'file' => UploadedFile::fake()->create('editor.pdf', 10, 'application/pdf'),
    ])->assertUnprocessable()->assertJsonValidationErrors('is_public');

    $this->actingAs($admin)->postJson('/api/v1/documents', [
        'system_id' => $system->id, 'title' => 'Approved', 'type' => 'manual',
        'is_public' => true,
        'file' => UploadedFile::fake()->create('admin.pdf', 10, 'application/pdf'),
    ])->assertCreated()->assertJsonPath('item.is_public', true);
});

it('moves legacy files to private storage before deleting the public copy', function (): void {
    Storage::fake('local');
    Storage::fake('public');
    $document = Document::factory()->create(['file_path' => 'documents/legacy.pdf']);
    Storage::disk('public')->put($document->file_path, '%PDF-legacy');

    $this->artisan('documents:migrate-to-private')->assertSuccessful();

    Storage::disk('local')->assertExists($document->file_path);
    Storage::disk('public')->assertMissing($document->file_path);
    expect(Storage::disk('local')->get($document->file_path))->toBe('%PDF-legacy');
});

it('retains a public legacy file when a private copy differs', function (): void {
    Storage::fake('local');
    Storage::fake('public');
    $document = Document::factory()->create(['file_path' => 'documents/conflict.pdf']);
    Storage::disk('public')->put($document->file_path, '%PDF-original');
    Storage::disk('local')->put($document->file_path, '%PDF-conflict');

    $this->artisan('documents:migrate-to-private')->assertFailed();

    Storage::disk('public')->assertExists($document->file_path);
    expect(Storage::disk('public')->get($document->file_path))->toBe('%PDF-original');
});
