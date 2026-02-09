<?php

use App\Models\Document;
use App\Models\Module;
use App\Models\System;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\Sanctum;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

uses(RefreshDatabase::class);

it('allows public listing by system', function (): void {
    $system = System::factory()->create();

    $this->getJson('/api/v1/documents?system_id='.$system->id)
        ->assertOk()
        ->assertJsonPath('meta.total', 0);
});

it('uploads and lists pdf documents by system', function (): void {
    Storage::fake('public');

    $permissionView = Permission::query()->create(['name' => 'document.view', 'guard_name' => 'web']);
    $permissionManage = Permission::query()->create(['name' => 'document.manage', 'guard_name' => 'web']);

    $role = Role::query()->create(['name' => 'editor', 'guard_name' => 'web']);
    $role->givePermissionTo([$permissionView, $permissionManage]);

    $user = User::factory()->create();
    $user->assignRole($role);

    Sanctum::actingAs($user);

    $system = System::factory()->create(['slug' => 'coffee-platform']);
    $module = Module::factory()->create(['system_id' => $system->id]);

    $upload = UploadedFile::fake()->create('manual.pdf', 512, 'application/pdf');

    $createResponse = $this->postJson('/api/v1/documents', [
        'system_id' => $system->id,
        'module_id' => $module->id,
        'title' => 'Manual de usuario cafeteria',
        'description' => 'Documento operativo base',
        'type' => 'manual',
        'file' => $upload,
    ]);

    $createResponse
        ->assertCreated()
        ->assertJsonPath('item.title', 'Manual de usuario cafeteria')
        ->assertJsonPath('item.type', 'manual');

    $document = Document::query()->first();

    expect($document)->not->toBeNull();
    Storage::disk('public')->assertExists($document->file_path);

    $listResponse = $this->getJson('/api/v1/documents?system_id='.$system->id);

    $listResponse
        ->assertOk()
        ->assertJsonPath('meta.total', 1)
        ->assertJsonPath('items.0.id', $document->id)
        ->assertJsonPath('items.0.system.id', $system->id);
});

it('blocks non-pdf uploads', function (): void {
    Storage::fake('public');

    $permissionManage = Permission::query()->create(['name' => 'document.manage', 'guard_name' => 'web']);
    $permissionView = Permission::query()->create(['name' => 'document.view', 'guard_name' => 'web']);
    $role = Role::query()->create(['name' => 'editor', 'guard_name' => 'web']);
    $role->givePermissionTo([$permissionManage, $permissionView]);

    $user = User::factory()->create();
    $user->assignRole($role);

    Sanctum::actingAs($user);

    $system = System::factory()->create();

    $response = $this->postJson('/api/v1/documents', [
        'system_id' => $system->id,
        'title' => 'Archivo invalido',
        'type' => 'manual',
        'file' => UploadedFile::fake()->create('not-valid.txt', 20, 'text/plain'),
    ]);

    $response->assertUnprocessable()->assertJsonValidationErrors(['file']);
});

it('serves document file for authenticated users with view permission', function (): void {
    Storage::fake('public');

    $permissionView = Permission::query()->create(['name' => 'document.view', 'guard_name' => 'web']);
    $role = Role::query()->create(['name' => 'viewer', 'guard_name' => 'web']);
    $role->givePermissionTo([$permissionView]);

    $user = User::factory()->create();
    $user->assignRole($role);

    $system = System::factory()->create();

    Storage::disk('public')->put('documents/'.$system->slug.'/manual.pdf', '%PDF-1.4 test');

    $document = Document::query()->create([
        'system_id' => $system->id,
        'title' => 'Manual PDF',
        'description' => 'Archivo de prueba',
        'type' => 'manual',
        'file_path' => 'documents/'.$system->slug.'/manual.pdf',
        'mime_type' => 'application/pdf',
        'size' => 12,
        'uploaded_by' => $user->id,
    ]);

    Sanctum::actingAs($user);

    $response = $this->get('/api/v1/documents/'.$document->id.'/file');

    $response
        ->assertOk()
        ->assertHeader('content-type', 'application/pdf')
        ->assertHeader('content-disposition', 'inline; filename="manual-pdf.pdf"');
});

it('blocks document file when unauthenticated', function (): void {
    Storage::fake('public');

    $user = User::factory()->create();
    $system = System::factory()->create();

    Storage::disk('public')->put('documents/'.$system->slug.'/private.pdf', '%PDF-1.4 test');

    $document = Document::query()->create([
        'system_id' => $system->id,
        'title' => 'Privado',
        'type' => 'manual',
        'file_path' => 'documents/'.$system->slug.'/private.pdf',
        'mime_type' => 'application/pdf',
        'size' => 12,
        'uploaded_by' => $user->id,
    ]);

    $this->getJson('/api/v1/documents/'.$document->id.'/file')->assertUnauthorized();
});

it('deletes document and physical file with manage permission', function (): void {
    Storage::fake('public');

    $permissionView = Permission::query()->create(['name' => 'document.view', 'guard_name' => 'web']);
    $permissionManage = Permission::query()->create(['name' => 'document.manage', 'guard_name' => 'web']);
    $role = Role::query()->create(['name' => 'admin', 'guard_name' => 'web']);
    $role->givePermissionTo([$permissionView, $permissionManage]);

    $user = User::factory()->create();
    $user->assignRole($role);

    $system = System::factory()->create();

    Storage::disk('public')->put('documents/'.$system->slug.'/to-delete.pdf', '%PDF-1.4 test');

    $document = Document::query()->create([
        'system_id' => $system->id,
        'title' => 'Eliminar',
        'type' => 'manual',
        'file_path' => 'documents/'.$system->slug.'/to-delete.pdf',
        'mime_type' => 'application/pdf',
        'size' => 12,
        'uploaded_by' => $user->id,
    ]);

    Sanctum::actingAs($user);

    $this->deleteJson('/api/v1/documents/'.$document->id)->assertNoContent();

    expect(Document::query()->find($document->id))->toBeNull();
    Storage::disk('public')->assertMissing('documents/'.$system->slug.'/to-delete.pdf');
});
