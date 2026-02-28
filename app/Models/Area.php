<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Area extends Model
{
    use HasUuids;
    use SoftDeletes;

    protected $keyType = 'string';

    public $incrementing = false;

    protected $fillable = [
        'condominio_id',
        'bloco_id',
        'pavimento_id',
        'tipo',
        'codigo',
        'nome',
        'descricao',
        'ativa',
    ];

    protected function casts(): array
    {
        return [
            'ativa' => 'boolean',
        ];
    }

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

    public function vistoriaItens(): HasMany
    {
        return $this->hasMany(VistoriaItem::class);
    }

    public function vistorias(): HasMany
    {
        return $this->hasMany(Vistoria::class);
    }
}
