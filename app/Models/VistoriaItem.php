<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class VistoriaItem extends Model
{
    use HasUuids;
    use SoftDeletes;

    protected $table = 'vistoria_itens';

    protected $keyType = 'string';

    public $incrementing = false;

    protected $fillable = [
        'condominio_id',
        'vistoria_id',
        'area_id',
        'item_codigo',
        'item_nome',
        'categoria',
        'status',
        'criticidade',
        'obrigatorio_foto_se_nao_ok',
        'ordem',
        'foto_path',
        'observacao',
        'inspecionado_em',
    ];

    protected function casts(): array
    {
        return [
            'inspecionado_em' => 'datetime',
            'obrigatorio_foto_se_nao_ok' => 'boolean',
            'ordem' => 'integer',
        ];
    }

    public function condominio(): BelongsTo
    {
        return $this->belongsTo(Condominio::class);
    }

    public function vistoria(): BelongsTo
    {
        return $this->belongsTo(Vistoria::class);
    }

    public function area(): BelongsTo
    {
        return $this->belongsTo(Area::class);
    }

    public function anexos(): MorphMany
    {
        return $this->morphMany(Anexo::class, 'owner');
    }

    public function fotoAnexo(): ?Anexo
    {
        return $this->anexos
            ->first(fn (Anexo $anexo) => str_starts_with((string) $anexo->mime_type, 'image/'))
            ?? $this->anexos->first();
    }

    public function getTemEvidenciaAttribute(): bool
    {
        return $this->anexos->isNotEmpty();
    }
}
