<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Unidade extends Model
{
    use HasUuids;
    use SoftDeletes;

    protected $keyType = 'string';

    public $incrementing = false;

    protected $fillable = [
        'condominio_id',
        'bloco_id',
        'pavimento_id',
        'numero',
        'tipo',
        'status',
    ];

    public function condominio(): BelongsTo
    {
        return $this->belongsTo(Condominio::class);
    }

    public function bloco(): BelongsTo
    {
        return $this->belongsTo(Bloco::class);
    }

    public function pavimento(): BelongsTo
    {
        return $this->belongsTo(Pavimento::class);
    }

    public function conflitos(): HasMany
    {
        return $this->hasMany(ConflitoMorador::class);
    }
}
