<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Endpoint extends Model
{
    use HasFactory;

    public const METHODS = ['GET', 'POST', 'PUT', 'PATCH', 'DELETE'];

    public const AUTH_TYPES = ['none', 'bearer', 'basic', 'api_key', 'oauth2', 'session', 'custom'];

    public const STATUSES = ['draft', 'published', 'archived'];

    protected $fillable = [
        'public_id',
        'module_id',
        'name',
        'method',
        'path',
        'description',
        'parameters',
        'request_example',
        'response_example',
        'authentication_type',
        'urls',
        'status',
        'created_by',
        'updated_by',
    ];

    protected function casts(): array
    {
        return [
            'parameters' => 'array',
            'request_example' => 'array',
            'response_example' => 'array',
            'urls' => 'array',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (self $endpoint): void {
            if (! filled($endpoint->public_id)) {
                $endpoint->public_id = (string) Str::ulid();
            }
        });
    }

    public function module(): BelongsTo
    {
        return $this->belongsTo(Module::class);
    }

    public function artefacts(): HasMany
    {
        return $this->hasMany(Artefact::class);
    }
}
