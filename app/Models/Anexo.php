<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Anexo extends Model
{
    use HasUuids;

    protected $keyType = 'string';

    public $incrementing = false;

    protected $fillable = [
        'condominio_id',
        'owner_type',
        'owner_id',
        'disk',
        'path',
        'file_name',
        'mime_type',
        'size',
        'comentario',
        'uploaded_by',
    ];

    protected function casts(): array
    {
        return [
            'size' => 'integer',
        ];
    }

    public function owner(): MorphTo
    {
        return $this->morphTo();
    }

    public function condominio(): BelongsTo
    {
        return $this->belongsTo(Condominio::class);
    }

    public function uploadedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }
}
