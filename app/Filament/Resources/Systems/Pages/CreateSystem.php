<?php

namespace App\Filament\Resources\Systems\Pages;

use App\Filament\Resources\Systems\SystemResource;
use App\Models\System;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class CreateSystem extends CreateRecord
{
    protected static string $resource = SystemResource::class;

    protected static bool $canCreateAnother = false;

    public ?int $draftRecordId = null;

    protected bool $nameConflictNotified = false;

    public function updatedData(mixed $value, string $key): void
    {
        if ($key !== 'name' && ! $this->draftRecordId) {
            return;
        }

        if ($key === 'name' && blank((string) $value)) {
            return;
        }

        $this->persistDraft();
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('discardDraft')
                ->label('Descartar borrador')
                ->color('gray')
                ->icon('heroicon-o-trash')
                ->requiresConfirmation()
                ->modalHeading('Descartar borrador')
                ->modalDescription('Este sistema se marcara como descartado y no aparecera en el catalogo.')
                ->visible(fn (): bool => filled($this->draftRecordId))
                ->action(function (): void {
                    if (! $this->draftRecordId) {
                        return;
                    }

                    System::query()
                        ->whereKey($this->draftRecordId)
                        ->update(['status' => 'discarded']);

                    Notification::make()
                        ->title('Borrador descartado.')
                        ->success()
                        ->send();

                    $this->redirect(static::getResource()::getUrl('index'));
                }),
        ];
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['status'] = 'published';

        return $data;
    }

    protected function handleRecordCreation(array $data): Model
    {
        if (! $this->draftRecordId) {
            return static::getModel()::create($data);
        }

        $record = System::query()->findOrFail($this->draftRecordId);

        $record->fill($data);
        $record->status = 'published';
        $record->save();

        return $record;
    }

    protected function getRedirectUrl(): string
    {
        return static::getResource()::getUrl('edit', ['record' => $this->getRecord()]);
    }

    protected function getCreatedNotificationTitle(): ?string
    {
        return 'Sistema creado. Ahora puedes subir manuales en la pestana Documentos.';
    }

    private function persistDraft(): void
    {
        $name = trim((string) ($this->data['name'] ?? ''));
        $slug = trim((string) ($this->data['slug'] ?? ''));

        if (! $this->draftRecordId) {
            if (blank($name)) {
                return;
            }

            if (System::query()->where('name', $name)->exists()) {
                if (! $this->nameConflictNotified) {
                    $this->nameConflictNotified = true;

                    Notification::make()
                        ->title('Ya existe un sistema con ese nombre.')
                        ->warning()
                        ->send();
                }

                return;
            }

            $resolvedSlug = $this->resolveUniqueSlug(filled($slug) ? $slug : Str::slug($name));
            $draft = System::query()->create([
                'name' => $name,
                'slug' => $resolvedSlug,
                'status' => 'draft',
            ]);

            $this->draftRecordId = $draft->id;
            $this->data['slug'] = $resolvedSlug;

            Notification::make()
                ->title('Borrador autoguardado.')
                ->success()
                ->send();
        }

        $draft = System::query()->find($this->draftRecordId);

        if (! $draft) {
            return;
        }

        $data = $this->data;
        $data['status'] = 'draft';

        if (blank((string) ($data['name'] ?? null))) {
            $data['name'] = $draft->name;
        }

        $candidateSlug = trim((string) ($data['slug'] ?? ''));
        $data['slug'] = $this->resolveUniqueSlug(
            filled($candidateSlug) ? $candidateSlug : Str::slug((string) $data['name']),
            $draft->id
        );
        $this->data['slug'] = $data['slug'];

        $draft->fill($data);
        $draft->save();
    }

    private function resolveUniqueSlug(string $slug, ?int $ignoreId = null): string
    {
        $base = Str::slug($slug);
        if (blank($base)) {
            $base = (string) Str::ulid();
        }

        $candidate = $base;
        $suffix = 2;

        while (
            System::query()
                ->where('slug', $candidate)
                ->when($ignoreId, fn ($query) => $query->whereKeyNot($ignoreId))
                ->exists()
        ) {
            $candidate = "{$base}-{$suffix}";
            $suffix++;
        }

        return $candidate;
    }
}
