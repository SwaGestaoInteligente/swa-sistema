<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class OcorrenciaFuncionario extends Model
{
    use HasUuids;
    use SoftDeletes;

    protected $table = 'ocorrencias_funcionarios';

    protected $keyType = 'string';

    public $incrementing = false;

    protected $fillable = [
        'condominio_id',
        'protocolo',
        'ocorrido_em',
        'funcionario_id',
        'funcionario_nome',
        'cargo',
        'tipo',
        'relato_detalhado',
        'testemunha_nome',
        'testemunha_contato',
        'medida_aplicada',
        'status',
        'reincidencia_nivel',
        'registrado_por',
        'encerrado_em',
        'historico_snapshot',
    ];

    protected function casts(): array
    {
        return [
            'ocorrido_em' => 'datetime',
            'encerrado_em' => 'datetime',
            'historico_snapshot' => 'array',
            'reincidencia_nivel' => 'integer',
        ];
    }

    public function condominio(): BelongsTo
    {
        return $this->belongsTo(Condominio::class);
    }

    public function funcionario(): BelongsTo
    {
        return $this->belongsTo(Usuario::class, 'funcionario_id');
    }

    public function anexos(): MorphMany
    {
        return $this->morphMany(Anexo::class, 'owner');
    }
}
