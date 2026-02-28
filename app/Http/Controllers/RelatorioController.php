<?php

namespace App\Http\Controllers;

use App\Jobs\EnviarRelatorioVistoriaJob;
use App\Models\Condominio;
use App\Models\CondominioEmail;
use App\Models\Relatorio;
use App\Models\Vistoria;
use App\Services\RelatorioService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class RelatorioController extends Controller
{
    public function __construct(private readonly RelatorioService $relatorioService)
    {
    }

    public function index(Condominio $condominio): View
    {
        $this->authorize('view', $condominio);

        return view('condominios.relatorios', [
            'condominio' => $condominio,
            'relatorios' => Relatorio::query()
                ->where('condominio_id', $condominio->id)
                ->latest('created_at')
                ->paginate(20),
            'vistorias' => Vistoria::query()
                ->where('condominio_id', $condominio->id)
                ->orderByDesc('updated_at')
                ->limit(20)
                ->get(['id', 'codigo', 'status', 'updated_at']),
            'emails' => CondominioEmail::query()
                ->where('condominio_id', $condominio->id)
                ->where('ativo', true)
                ->orderBy('nome')
                ->get(['id', 'nome', 'email', 'tipo']),
        ]);
    }

    public function gerarVistoria(Condominio $condominio, Vistoria $vistoria)
    {
        $this->assertSameCondominio($condominio, $vistoria->condominio_id);
        $this->authorize('view', $condominio);

        $relatorio = $this->relatorioService->gerarVistoria($vistoria, auth()->user());

        return Storage::disk($relatorio->disk)->download(
            $relatorio->path,
            $relatorio->file_name
        );
    }

    public function download(Condominio $condominio, Relatorio $relatorio)
    {
        $this->assertSameCondominio($condominio, $relatorio->condominio_id);
        $this->authorize('view', $relatorio);

        return Storage::disk($relatorio->disk)->download(
            $relatorio->path,
            $relatorio->file_name
        );
    }

    public function signedDownload(Request $request, Relatorio $relatorio)
    {
        abort_unless($request->hasValidSignature(), 403);

        return Storage::disk($relatorio->disk)->download(
            $relatorio->path,
            $relatorio->file_name
        );
    }

    public function enviar(Request $request, Condominio $condominio, Relatorio $relatorio): RedirectResponse
    {
        $this->assertSameCondominio($condominio, $relatorio->condominio_id);
        $this->authorize('email', $relatorio);

        $data = $request->validate([
            'destinatarios' => ['required', 'array', 'min:1'],
            'destinatarios.*' => ['uuid', Rule::exists('condominio_emails', 'id')],
            'mensagem' => ['nullable', 'string', 'max:2000'],
            'anexar_pdf' => ['nullable', 'boolean'],
        ]);

        $destinatarios = CondominioEmail::query()
            ->where('condominio_id', $condominio->id)
            ->where('ativo', true)
            ->whereIn('id', $data['destinatarios'])
            ->get(['nome', 'email'])
            ->map(fn (CondominioEmail $email) => [
                'nome' => $email->nome,
                'email' => $email->email,
            ])
            ->values()
            ->all();

        if (empty($destinatarios)) {
            return back()->withErrors([
                'destinatarios' => 'Selecione ao menos um destinatário ativo.',
            ]);
        }

        $downloadUrl = URL::temporarySignedRoute(
            'relatorios.download.signed',
            now()->addHours(24),
            ['relatorio' => $relatorio->id]
        );

        EnviarRelatorioVistoriaJob::dispatch(
            relatorioId: $relatorio->id,
            destinatarios: $destinatarios,
            downloadUrl: $downloadUrl,
            mensagem: $data['mensagem'] ?? null,
            anexarPdf: (bool) ($data['anexar_pdf'] ?? false)
        );

        return back()->with('success', 'Envio de relatório enfileirado com sucesso.');
    }

    private function assertSameCondominio(Condominio $condominio, string $resourceCondominioId): void
    {
        if ((string) $condominio->id !== (string) $resourceCondominioId) {
            abort(404);
        }
    }
}
