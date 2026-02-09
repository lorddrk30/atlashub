<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Document extends Model
{
    use HasFactory;

    public const TYPES = ['manual', 'guia', 'procedimiento', 'diagrama', 'politica'];

    protected $fillable = [
        'system_id',
        'module_id',
        'endpoint_id',
        'title',
        'description',
        'type',
        'file_path',
        'mime_type',
        'size',
        'uploaded_by',
    ];

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

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }
}
