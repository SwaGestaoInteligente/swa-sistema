<?php

namespace App\Http\Controllers;

use App\Models\Area;
use App\Models\Condominio;
use App\Models\Vistoria;
use App\Models\VistoriaItem;
use App\Services\AnexoService;
use App\Services\VistoriaService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;
use Illuminate\View\View;

class VistoriaItemController extends Controller
{
    public function __construct(
        private readonly AnexoService $anexoService,
        private readonly VistoriaService $vistoriaService
    ) {
    }

    public function create(Condominio $condominio, Vistoria $vistoria): View
    {
        $this->assertSameCondominio($condominio, $vistoria->condominio_id);
        $this->ensureItemsEditable($vistoria);

        return view('vistorias.itens.create', [
            'condominio' => $condominio,
            'vistoria' => $vistoria,
            'item' => new VistoriaItem([
                'categoria' => 'extintor',
                'status' => 'ok',
                'criticidade' => 'baixa',
                'inspecionado_em' => now()->format('Y-m-d\TH:i'),
                'obrigatorio_foto_se_nao_ok' => true,
            ]),
            'areas' => Area::query()
                ->where('condominio_id', $vistoria->condominio_id)
                ->orderBy('nome')
                ->get(['id', 'nome', 'codigo', 'tipo']),
            'categorias' => $this->categorias(),
            'statusList' => $this->statusList(),
            'criticidades' => $this->criticidades(),
            'quickObs' => $this->quickObservacoes(),
        ]);
    }

    public function store(Request $request, Condominio $condominio, Vistoria $vistoria): RedirectResponse
    {
        $this->assertSameCondominio($condominio, $vistoria->condominio_id);
        $this->ensureItemsEditable($vistoria);

        $data = $this->validatedData($request, $vistoria);
        $data['condominio_id'] = $vistoria->condominio_id;
        $data['vistoria_id'] = $vistoria->id;
        $data['ordem'] = (int) ($data['ordem'] ?? (($vistoria->itens()->max('ordem') ?? 0) + 1));

        $item = VistoriaItem::query()->create($data);

        $this->processEvidenceUploads($request, $vistoria, $item);

        if ($vistoria->status === 'rascunho') {
            $vistoria->update([
                'status' => 'em_andamento',
                'iniciada_em' => $vistoria->iniciada_em ?? now(),
            ]);
        }

        $this->vistoriaService->recalculateRisk($vistoria);

        return redirect()
            ->route('condominios.context.vistorias.show', ['condominio' => $condominio, 'vistoria' => $vistoria])
            ->with('success', 'Item da vistoria registrado com sucesso.');
    }

    public function update(Request $request, Condominio $condominio, Vistoria $vistoria, VistoriaItem $item): RedirectResponse
    {
        $this->assertSameCondominio($condominio, $vistoria->condominio_id);
        $this->ensureItemsEditable($vistoria);

        if ((string) $item->vistoria_id !== (string) $vistoria->id) {
            abort(404);
        }

        $data = $request->validate([
            'status' => ['required', Rule::in(array_keys($this->statusList()))],
            'criticidade' => ['required', Rule::in(array_keys($this->criticidades()))],
            'observacao' => ['nullable', 'string'],
            'inspecionado_em' => ['nullable', 'date'],
            'foto' => array_merge(['nullable'], $this->anexoService->allowedUploadRules()),
            'foto_comentario' => ['nullable', 'string', 'max:500'],
            'fotos' => ['nullable', 'array', 'max:6'],
            'fotos.*' => array_merge(['nullable'], $this->anexoService->allowedUploadRules()),
            'foto_comentarios' => ['nullable', 'array'],
            'foto_comentarios.*' => ['nullable', 'string', 'max:500'],
            'obrigatorio_foto_se_nao_ok' => ['nullable', 'boolean'],
        ]);

        $item->fill([
            'status' => $data['status'],
            'criticidade' => $data['criticidade'],
            'observacao' => $data['observacao'] ?? null,
            'inspecionado_em' => $data['inspecionado_em'] ?? now(),
            'obrigatorio_foto_se_nao_ok' => (bool) ($data['obrigatorio_foto_se_nao_ok'] ?? $item->obrigatorio_foto_se_nao_ok),
        ]);

        $this->processEvidenceUploads($request, $vistoria, $item);

        $statusNeedsEvidence = in_array($item->status, ['danificado', 'ausente', 'improvisado'], true);
        $hasImage = $item->anexos()->where('mime_type', 'like', 'image/%')->exists();

        if ($statusNeedsEvidence && $item->obrigatorio_foto_se_nao_ok && ! $hasImage && ! $this->hasIncomingEvidenceFiles($request)) {
            return back()->withErrors([
                'foto' => 'Para status diferente de OK, a foto é obrigatória.',
            ]);
        }

        if ($statusNeedsEvidence && blank($item->observacao)) {
            return back()->withErrors([
                'observacao' => 'Para status diferente de OK, preencha a observação.',
            ]);
        }

        $item->save();
        $this->vistoriaService->recalculateRisk($vistoria);

        return back()->with('success', 'Item atualizado com sucesso.');
    }

    public function destroy(Condominio $condominio, Vistoria $vistoria, VistoriaItem $item): RedirectResponse
    {
        $this->assertSameCondominio($condominio, $vistoria->condominio_id);
        $this->ensureItemsEditable($vistoria);

        if ((string) $item->vistoria_id !== (string) $vistoria->id) {
            abort(404);
        }

        foreach ($item->anexos as $anexo) {
            $this->anexoService->delete($anexo);
        }

        $item->delete();
        $this->vistoriaService->recalculateRisk($vistoria);

        return redirect()
            ->route('condominios.context.vistorias.show', ['condominio' => $condominio, 'vistoria' => $vistoria])
            ->with('success', 'Item removido com sucesso.');
    }

    private function validatedData(Request $request, Vistoria $vistoria): array
    {
        $validator = validator($request->all(), [
            'area_id' => ['required', 'uuid', Rule::exists('areas', 'id')],
            'item_codigo' => ['nullable', 'string', 'max:40'],
            'item_nome' => ['required', 'string', 'max:150'],
            'categoria' => ['required', Rule::in(array_keys($this->categorias()))],
            'status' => ['required', Rule::in(array_keys($this->statusList()))],
            'criticidade' => ['required', Rule::in(array_keys($this->criticidades()))],
            'obrigatorio_foto_se_nao_ok' => ['nullable', 'boolean'],
            'ordem' => ['nullable', 'integer', 'min:0'],
            'observacao' => ['nullable', 'string'],
            'inspecionado_em' => ['nullable', 'date'],
            'foto' => array_merge(['nullable'], $this->anexoService->allowedUploadRules()),
            'foto_comentario' => ['nullable', 'string', 'max:500'],
            'fotos' => ['nullable', 'array', 'max:8'],
            'fotos.*' => array_merge(['nullable'], $this->anexoService->allowedUploadRules()),
            'foto_comentarios' => ['nullable', 'array'],
            'foto_comentarios.*' => ['nullable', 'string', 'max:500'],
        ]);

        $validator->after(function (Validator $validator) use ($request, $vistoria): void {
            $area = Area::query()->find($request->input('area_id'));

            if ($area && (string) $area->condominio_id !== (string) $vistoria->condominio_id) {
                $validator->errors()->add('area_id', 'A área selecionada não pertence ao condomínio da vistoria.');
            }

            $status = $request->input('status');
            $statusNeedsEvidence = in_array($status, ['danificado', 'ausente', 'improvisado'], true);
            $fotoObrigatoria = $request->boolean('obrigatorio_foto_se_nao_ok', true);

            if ($statusNeedsEvidence && $fotoObrigatoria && ! $this->hasIncomingEvidenceFiles($request)) {
                $validator->errors()->add('foto', 'Para status diferente de OK, a foto é obrigatória.');
            }

            if ($statusNeedsEvidence && blank($request->input('observacao'))) {
                $validator->errors()->add('observacao', 'Para status diferente de OK, detalhe o problema na observação.');
            }
        });

        return $validator->validate();
    }

    private function categorias(): array
    {
        return [
            'extintor' => 'Extintor',
            'placa_saida' => 'Placa de saída',
            'luz_emergencia' => 'Luz de emergência',
            'corrimao' => 'Corrimão',
            'sinalizacao' => 'Sinalização',
            'outro' => 'Outro',
        ];
    }

    private function statusList(): array
    {
        return [
            'ok' => 'OK',
            'danificado' => 'Danificado',
            'ausente' => 'Ausente',
            'improvisado' => 'Improvisado',
        ];
    }

    private function criticidades(): array
    {
        return [
            'baixa' => 'Baixa',
            'media' => 'Média',
            'alta' => 'Alta',
            'critica' => 'Crítica',
        ];
    }

    private function quickObservacoes(): array
    {
        return [
            'Placa caída',
            'Extintor vencido',
            'Lâmpada apagada',
            'Corrimão solto',
            'Sinalização ausente',
        ];
    }

    private function ensureItemsEditable(Vistoria $vistoria): void
    {
        if (in_array($vistoria->status, ['finalizada', 'cancelada'], true)) {
            abort(403, 'A vistoria já está finalizada/cancelada e não aceita novos itens.');
        }
    }

    private function assertSameCondominio(Condominio $condominio, string $resourceCondominioId): void
    {
        if ((string) $condominio->id !== (string) $resourceCondominioId) {
            abort(404);
        }
    }

    private function processEvidenceUploads(Request $request, Vistoria $vistoria, VistoriaItem $item): void
    {
        $directory = "condominios/{$vistoria->condominio_id}/vistorias/{$vistoria->id}/itens/{$item->id}";
        $uploadedBy = auth()->id();

        if ($request->hasFile('foto')) {
            $this->anexoService->store(
                condominioId: $vistoria->condominio_id,
                owner: $item,
                file: $request->file('foto'),
                directory: $directory,
                uploadedBy: $uploadedBy,
                comentario: $request->input('foto_comentario')
            );
        }

        $files = (array) $request->file('fotos', []);
        $comments = (array) $request->input('foto_comentarios', []);

        foreach ($files as $index => $file) {
            if (! $file) {
                continue;
            }

            $this->anexoService->store(
                condominioId: $vistoria->condominio_id,
                owner: $item,
                file: $file,
                directory: $directory,
                uploadedBy: $uploadedBy,
                comentario: $comments[$index] ?? null
            );
        }

        $lastImage = $item->anexos()
            ->where('mime_type', 'like', 'image/%')
            ->latest('created_at')
            ->first();

        if ($lastImage) {
            $item->forceFill(['foto_path' => $lastImage->path])->save();
        }
    }

    private function hasIncomingEvidenceFiles(Request $request): bool
    {
        if ($request->hasFile('foto')) {
            return true;
        }

        return collect((array) $request->file('fotos', []))
            ->filter(fn ($file) => $file !== null)
            ->isNotEmpty();
    }
}
