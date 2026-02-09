<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class System extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'prod_server',
        'uat_server',
        'dev_server',
        'internal_url',
        'public_url',
        'responsibles',
        'user_areas',
        'repository_url',
        'gitlab_url',
        'home_preview_url',
    ];

    protected function casts(): array
    {
        return [
            'responsibles' => 'array',
            'user_areas' => 'array',
        ];
    }

    public function modules(): HasMany
    {
        return $this->hasMany(Module::class);
    }

    public function endpoints(): HasManyThrough
    {
        return $this->hasManyThrough(Endpoint::class, Module::class);
    }

    public function artefacts(): HasMany
    {
        return $this->hasMany(Artefact::class);
    }

    public function documents(): HasMany
    {
        return $this->hasMany(Document::class);
    }
}
