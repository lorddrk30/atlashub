<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class System extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'slug', 'description'];

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
}
