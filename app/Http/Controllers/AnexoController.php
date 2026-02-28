<?php

namespace App\Http\Controllers;

use App\Models\Anexo;
use App\Models\Condominio;
use App\Services\AnexoService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class AnexoController extends Controller
{
    public function __construct(private readonly AnexoService $anexoService)
    {
    }

    public function download(Condominio $condominio, Anexo $anexo)
    {
        $this->assertSameCondominio($condominio, $anexo->condominio_id);
        $this->authorize('view', $anexo);

        return $this->anexoService->download($anexo);
    }

    public function signedDownload(Request $request, Condominio $condominio, Anexo $anexo)
    {
        abort_unless($request->hasValidSignature(), 403);

        $this->assertSameCondominio($condominio, $anexo->condominio_id);
        $this->authorize('view', $anexo);

        return $this->anexoService->download($anexo);
    }

    public function destroy(Condominio $condominio, Anexo $anexo): RedirectResponse
    {
        $this->assertSameCondominio($condominio, $anexo->condominio_id);
        $this->authorize('delete', $anexo);
        $this->anexoService->delete($anexo);

        return back()->with('success', 'Anexo removido com sucesso.');
    }

    private function assertSameCondominio(Condominio $condominio, string $resourceCondominioId): void
    {
        if ((string) $condominio->id !== (string) $resourceCondominioId) {
            abort(404);
        }
    }
}
