<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Artefact extends Model
{
    use HasFactory;

    public const TYPES = ['swagger', 'postman', 'repo', 'docs', 'runbook', 'dashboard', 'other'];

    protected $fillable = [
        'system_id',
        'module_id',
        'endpoint_id',
        'type',
        'title',
        'url',
        'description',
        'metadata',
        'created_by',
        'updated_by',
    ];

    protected function casts(): array
    {
        return [
            'metadata' => 'array',
        ];
    }

    public function system(): BelongsTo
    {
        return $this->belongsTo(System::class);
    }

    public function module(): BelongsTo
    {
        return $this->belongsTo(Module::class);
    }

    public function endpoint(): BelongsTo
    {
        return $this->belongsTo(Endpoint::class);
    }
}
