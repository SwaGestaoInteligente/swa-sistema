<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Vistoria extends Model
{
    use HasUuids;
    use SoftDeletes;

    protected $keyType = 'string';

    public $incrementing = false;

    protected $fillable = [
        'condominio_id',
        'area_id',
        'checklist_template_id',
        'codigo',
        'tipo',
        'status',
        'competencia',
        'iniciada_em',
        'finalizada_em',
        'responsavel_nome',
        'observacoes',
        'risco_geral',
    ];

    protected function casts(): array
    {
        return [
            'competencia' => 'date',
            'iniciada_em' => 'datetime',
            'finalizada_em' => 'datetime',
        ];
    }

    public function condominio(): BelongsTo
    {
        return $this->belongsTo(Condominio::class);
    }

    public function itens(): HasMany
    {
        return $this->hasMany(VistoriaItem::class);
    }

    public function area(): BelongsTo
    {
        return $this->belongsTo(Area::class);
    }

    public function template(): BelongsTo
    {
        return $this->belongsTo(ChecklistTemplate::class, 'checklist_template_id');
    }

    public function anexos(): MorphMany
    {
        return $this->morphMany(Anexo::class, 'owner');
    }

    public function getRiscoNivelAttribute(): string
    {
        $risco = (int) ($this->risco_geral ?? 0);

        if ($risco <= 0) {
            return 'neutro';
        }

        if ($risco <= 33) {
            return 'baixo';
        }

        if ($risco <= 66) {
            return 'medio';
        }

        return 'alto';
    }

    public function getRiscoNivelLabelAttribute(): string
    {
        return match ($this->risco_nivel) {
            'neutro' => 'Neutro',
            'baixo' => 'Baixo',
            'medio' => 'Medio',
            default => 'Alto',
        };
    }
}
