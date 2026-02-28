<?php

namespace App\Services;

use App\Models\Relatorio;
use App\Models\User;
use App\Models\Vistoria;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class RelatorioService
{
    public function gerarVistoria(Vistoria $vistoria, ?User $ator = null): Relatorio
    {
        $vistoria->loadMissing([
            'condominio:id,nome,codigo,cidade,uf',
            'area:id,nome,codigo,tipo',
            'itens' => fn ($query) => $query
                ->with(['area:id,nome,codigo,tipo', 'anexos'])
                ->orderBy('ordem')
                ->orderByDesc('inspecionado_em')
                ->orderByDesc('created_at'),
        ]);

        $disk = (string) config('filesystems.default', 'local');
        $safeCode = Str::slug($vistoria->codigo ?: $vistoria->id);
        $timestamp = now()->format('Ymd-His');
        $fileName = "vistoria-{$safeCode}-{$timestamp}.pdf";
        $path = "condominios/{$vistoria->condominio_id}/relatorios/vistorias/{$fileName}";

        $pdfBinary = Pdf::loadView('vistorias.pdf', [
            'vistoria' => $vistoria,
        ])->setPaper('a4')->output();

        Storage::disk($disk)->put($path, $pdfBinary);

        return Relatorio::query()->create([
            'condominio_id' => $vistoria->condominio_id,
            'type' => 'vistoria',
            'ref_id' => $vistoria->id,
            'disk' => $disk,
            'path' => $path,
            'file_name' => $fileName,
            'mime_type' => 'application/pdf',
            'size' => strlen($pdfBinary),
            'generated_by' => $ator?->id,
        ]);
    }
}
