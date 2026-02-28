<?php

namespace App\Services;

use App\Models\Area;
use App\Models\ChecklistTemplate;
use App\Models\Vistoria;
use App\Models\VistoriaItem;

class VistoriaService
{
    public function aplicarTemplate(Vistoria $vistoria, ChecklistTemplate $template, Area $area): int
    {
        $created = 0;
        $ordemBase = (int) $vistoria->itens()->max('ordem');

        foreach ($template->itens as $templateItem) {
            $exists = VistoriaItem::query()
                ->where('vistoria_id', $vistoria->id)
                ->where('item_nome', $templateItem->titulo_item)
                ->where('area_id', $area->id)
                ->exists();

            if ($exists) {
                continue;
            }

            $ordemBase++;

            VistoriaItem::query()->create([
                'condominio_id' => $vistoria->condominio_id,
                'vistoria_id' => $vistoria->id,
                'area_id' => $area->id,
                'item_codigo' => null,
                'item_nome' => $templateItem->titulo_item,
                'categoria' => $templateItem->categoria,
                'status' => 'ok',
                'criticidade' => 'baixa',
                'obrigatorio_foto_se_nao_ok' => $templateItem->obrigatorio_foto_se_nao_ok,
                'ordem' => $templateItem->ordem > 0 ? $templateItem->ordem : $ordemBase,
                'observacao' => null,
                'inspecionado_em' => null,
            ]);

            $created++;
        }

        if ($created > 0 && $vistoria->status === 'rascunho') {
            $vistoria->update([
                'status' => 'em_andamento',
                'iniciada_em' => $vistoria->iniciada_em ?? now(),
            ]);
        }

        $this->recalculateRisk($vistoria);

        return $created;
    }

    public function recalculateRisk(Vistoria $vistoria): void
    {
        $weights = [
            'baixa' => 1,
            'media' => 2,
            'alta' => 3,
            'critica' => 4,
        ];

        $relevantItems = $vistoria->itens()->get(['status', 'criticidade']);

        if ($relevantItems->isEmpty()) {
            $vistoria->update(['risco_geral' => 0]);

            return;
        }

        $score = $relevantItems->sum(function (VistoriaItem $item) use ($weights): int {
            if ($item->status === 'ok') {
                return 0;
            }

            return $weights[$item->criticidade] ?? 0;
        });

        $max = $relevantItems->count() * 4;
        $riskPercent = $max > 0 ? (int) round(($score / $max) * 100) : 0;

        $vistoria->update(['risco_geral' => $riskPercent]);
    }

    public function pendenciasEvidencia(Vistoria $vistoria): int
    {
        $vistoria->loadMissing('itens.anexos');

        return $vistoria->itens->filter(function (VistoriaItem $item): bool {
            if ($item->status === 'ok') {
                return false;
            }

            $temFoto = $item->anexos->contains(function ($anexo): bool {
                return str_starts_with((string) $anexo->mime_type, 'image/');
            });

            $obsVazia = blank($item->observacao);

            if ($item->obrigatorio_foto_se_nao_ok && ! $temFoto) {
                return true;
            }

            return $obsVazia;
        })->count();
    }
}
