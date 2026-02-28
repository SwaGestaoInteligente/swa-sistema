<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Bloco extends Model
{
    use HasUuids;
    use SoftDeletes;

    protected $keyType = 'string';

    public $incrementing = false;

    protected $fillable = [
        'condominio_id',
        'codigo',
        'nome',
        'descricao',
        'ordem',
        'ativo',
    ];

    protected function casts(): array
    {
        return [
            'ativo' => 'boolean',
        ];
    }

    public function condominio(): BelongsTo
    {
        return $this->belongsTo(Condominio::class);
    }

    public function pavimentos(): HasMany
    {
        return $this->hasMany(Pavimento::class);
    }

    public function areas(): HasMany
    {
        return $this->hasMany(Area::class);
    }

    public function unidades(): HasMany
    {
        return $this->hasMany(Unidade::class);
    }
}
