<?php

namespace App\Http\Controllers;

use App\Models\Backup;
use App\Models\Condominio;
use App\Services\BackupService;
use Illuminate\Http\RedirectResponse;
use Throwable;

class BackupController extends Controller
{
    public function __construct(private readonly BackupService $backupService)
    {
    }

    public function index(Condominio $condominio)
    {
        $this->authorize('viewAny', [Backup::class, $condominio]);

        $backups = Backup::query()
            ->where('condominio_id', $condominio->id)
            ->orderByDesc('created_at')
            ->limit(50)
            ->get();

        return view('backups.index', compact('condominio', 'backups'));
    }

    public function store(Condominio $condominio): RedirectResponse
    {
        $this->authorize('create', [Backup::class, $condominio]);

        try {
            $this->backupService->generateCondominioBackup($condominio, auth()->user());
        } catch (Throwable $exception) {
            report($exception);

            return back()->with('error', 'Nao foi possivel gerar o backup agora. Tente novamente em instantes.');
        }

        return back()->with('success', 'Backup gerado com sucesso.');
    }

    public function download(Condominio $condominio, Backup $backup)
    {
        $this->assertSameCondominio($condominio, $backup);
        $this->authorize('view', $backup);

        return $this->backupService->download($backup);
    }

    public function destroy(Condominio $condominio, Backup $backup): RedirectResponse
    {
        $this->assertSameCondominio($condominio, $backup);
        $this->authorize('delete', $backup);
        $this->backupService->delete($backup);

        return back()->with('success', 'Backup removido.');
    }

    private function assertSameCondominio(Condominio $condominio, Backup $backup): void
    {
        if ((string) $backup->condominio_id !== (string) $condominio->id) {
            abort(404);
        }
    }
}
