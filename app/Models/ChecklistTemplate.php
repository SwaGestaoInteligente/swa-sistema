<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class ChecklistTemplate extends Model
{
    use HasUuids;
    use SoftDeletes;

    protected $table = 'checklist_templates';

    protected $keyType = 'string';

    public $incrementing = false;

    protected $fillable = [
        'condominio_id',
        'nome',
        'categoria',
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

    public function itens(): HasMany
    {
        return $this->hasMany(ChecklistTemplateItem::class, 'template_id')->orderBy('ordem');
    }
}
