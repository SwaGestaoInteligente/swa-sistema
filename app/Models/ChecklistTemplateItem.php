<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ChecklistTemplateItem extends Model
{
    use HasUuids;

    protected $table = 'checklist_template_itens';

    protected $keyType = 'string';

    public $incrementing = false;

    protected $fillable = [
        'template_id',
        'titulo_item',
        'categoria',
        'obrigatorio_foto_se_nao_ok',
        'ordem',
    ];

    protected function casts(): array
    {
        return [
            'obrigatorio_foto_se_nao_ok' => 'boolean',
            'ordem' => 'integer',
        ];
    }

    public function template(): BelongsTo
    {
        return $this->belongsTo(ChecklistTemplate::class, 'template_id');
    }
}
