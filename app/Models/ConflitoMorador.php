<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class ConflitoMorador extends Model
{
    use HasUuids;
    use SoftDeletes;

    protected $table = 'conflitos_moradores';

    protected $keyType = 'string';

    public $incrementing = false;

    protected $fillable = [
        'condominio_id',
        'protocolo',
        'ocorrido_em',
        'morador_a_id',
        'morador_a_nome',
        'morador_b_id',
        'morador_b_nome',
        'unidade_id',
        'unidade',
        'tipo',
        'relato',
        'testemunhas',
        'tentativa_mediacao',
        'status',
        'registrado_por',
        'tratado_por',
        'resolvido_em',
    ];

    protected function casts(): array
    {
        return [
            'ocorrido_em' => 'datetime',
            'resolvido_em' => 'datetime',
            'testemunhas' => 'array',
        ];
    }

    public function condominio(): BelongsTo
    {
        return $this->belongsTo(Condominio::class);
    }

    public function moradorA(): BelongsTo
    {
        return $this->belongsTo(Usuario::class, 'morador_a_id');
    }

    public function moradorB(): BelongsTo
    {
        return $this->belongsTo(Usuario::class, 'morador_b_id');
    }

    public function unidade(): BelongsTo
    {
        return $this->belongsTo(Unidade::class);
    }

    public function anexos(): MorphMany
    {
        return $this->morphMany(Anexo::class, 'owner');
    }
}
