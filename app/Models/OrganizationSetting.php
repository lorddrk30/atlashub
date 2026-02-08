<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrganizationSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'short_name',
        'slug',
        'tagline',
        'description',
        'logo',
        'favicon',
        'support_email',
        'primary_color',
        'secondary_color',
    ];
}

