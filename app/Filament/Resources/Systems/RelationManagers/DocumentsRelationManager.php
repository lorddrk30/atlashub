<?php

namespace App\Filament\Resources\Systems\RelationManagers;

use App\Models\Document;
use App\Models\Endpoint;
use App\Models\Module;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class DocumentsRelationManager extends RelationManager
{
    protected static string $relationship = 'documents';

    protected static ?string $title = 'Documentos';

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('title')
                ->label('Titulo')
                ->required()
                ->maxLength(255),
            Textarea::make('description')
                ->label('Descripcion')
                ->rows(3)
                ->columnSpanFull(),
            Select::make('type')
                ->label('Tipo')
                ->options(array_combine(Document::TYPES, array_map('ucfirst', Document::TYPES)))
                ->required(),
            Select::make('module_id')
                ->label('Modulo (opcional)')
                ->searchable()
                ->preload()
                ->options(function (): array {
                    $systemId = $this->getOwnerRecord()->id;

                    return Module::query()
                        ->where('system_id', $systemId)
                        ->orderBy('name')
                        ->pluck('name', 'id')
                        ->all();
                })
                ->reactive()
                ->afterStateUpdated(fn ($state, callable $set) => $set('endpoint_id', null)),
            Select::make('endpoint_id')
                ->label('Endpoint (opcional)')
                ->searchable()
                ->preload()
                ->options(function (callable $get): array {
                    $moduleId = $get('module_id');

                    if (! $moduleId) {
                        return [];
                    }

                    return Endpoint::query()
                        ->where('module_id', (int) $moduleId)
                        ->orderBy('name')
                        ->get()
                        ->mapWithKeys(fn (Endpoint $endpoint): array => [
                            $endpoint->id => sprintf('%s %s', $endpoint->method, $endpoint->path),
                        ])
                        ->all();
                }),
            FileUpload::make('file')
                ->label('Archivo PDF')
                ->acceptedFileTypes(['application/pdf'])
                ->maxSize(20480)
                ->required()
                ->disk('public')
                ->visibility('public')
                ->directory(function (): string {
                    $slug = (string) ($this->getOwnerRecord()->slug ?? 'sistema');

                    return 'documents/'.Str::slug($slug);
                })
                ->preserveFilenames(),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('title')
            ->columns([
                TextColumn::make('title')->label('Titulo')->searchable()->limit(40),
                TextColumn::make('type')->label('Tipo')->badge(),
                TextColumn::make('module.name')->label('Modulo')->placeholder('-'),
                TextColumn::make('endpoint.path')->label('Endpoint')->placeholder('-')->limit(30),
                TextColumn::make('created_at')->label('Subido')->since(),
            ])
            ->headerActions([
                CreateAction::make('subirManual')
                    ->label('Subir Manual')
                    ->icon('heroicon-o-arrow-up-tray')
                    ->visible(fn (): bool => auth()->user()?->can('document.manage') ?? false)
                    ->mutateDataUsing(function (array $data): array {
                        $filePath = $data['file'] ?? null;

                        $data['system_id'] = $this->getOwnerRecord()->id;
                        $data['file_path'] = $filePath;
                        $data['mime_type'] = 'application/pdf';
                        $data['size'] = $filePath ? (Storage::disk('public')->size($filePath) ?: 0) : 0;
                        $data['uploaded_by'] = auth()->id();
                        unset($data['file']);

                        return $data;
                    }),
            ])
            ->recordActions([
                Action::make('verPdf')
                    ->label('Ver PDF')
                    ->icon('heroicon-o-eye')
                    ->visible(fn (): bool => auth()->user()?->can('document.view') ?? false)
                    ->url(fn (Document $record): string => route('documents.file', ['document' => $record->id]))
                    ->openUrlInNewTab(),
                Action::make('descargarPdf')
                    ->label('Descargar')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->visible(fn (): bool => auth()->user()?->can('document.view') ?? false)
                    ->url(fn (Document $record): string => route('documents.file', ['document' => $record->id, 'download' => 1]))
                    ->openUrlInNewTab(),
                DeleteAction::make()
                    ->visible(fn (): bool => auth()->user()?->can('document.manage') ?? false),
            ]);
    }

    public static function canViewForRecord($ownerRecord, string $pageClass): bool
    {
        $user = auth()->user();

        if (! $user) {
            return false;
        }

        return $user->can('document.view') || $user->can('document.manage');
    }
}
