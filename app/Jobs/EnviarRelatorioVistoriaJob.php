<?php

namespace App\Jobs;

use App\Mail\RelatorioVistoriaMail;
use App\Models\Relatorio;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Mail;

class EnviarRelatorioVistoriaJob implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public readonly string $relatorioId,
        public readonly array $destinatarios,
        public readonly string $downloadUrl,
        public readonly ?string $mensagem = null,
        public readonly bool $anexarPdf = false
    ) {
    }

    public function handle(): void
    {
        $relatorio = Relatorio::query()
            ->with('condominio')
            ->find($this->relatorioId);

        if (! $relatorio) {
            return;
        }

        foreach ($this->destinatarios as $destinatario) {
            $email = $destinatario['email'] ?? null;

            if (! $email) {
                continue;
            }

            Mail::to($email)->send(
                new RelatorioVistoriaMail(
                    relatorio: $relatorio,
                    downloadUrl: $this->downloadUrl,
                    mensagem: $this->mensagem,
                    anexarPdf: $this->anexarPdf
                )
            );
        }
    }
}
