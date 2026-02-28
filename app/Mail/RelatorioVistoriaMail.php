<?php

namespace App\Mail;

use App\Models\Relatorio;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;

class RelatorioVistoriaMail extends Mailable
{
    use Queueable;
    use SerializesModels;

    public function __construct(
        public readonly Relatorio $relatorio,
        public readonly string $downloadUrl,
        public readonly ?string $mensagem = null,
        public readonly bool $anexarPdf = false
    ) {
    }

    public function envelope(): Envelope
    {
        $nomeCondominio = $this->relatorio->condominio?->nome ?? 'Condomínio';

        return new Envelope(
            subject: 'Relatório de vistoria - '.$nomeCondominio
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.relatorio-vistoria',
            with: [
                'relatorio' => $this->relatorio,
                'downloadUrl' => $this->downloadUrl,
                'mensagem' => $this->mensagem,
            ]
        );
    }

    public function attachments(): array
    {
        if (! $this->anexarPdf) {
            return [];
        }

        $disk = $this->relatorio->disk;

        if (! Storage::disk($disk)->exists($this->relatorio->path)) {
            return [];
        }

        return [
            Attachment::fromStorageDisk($disk, $this->relatorio->path)
                ->as($this->relatorio->file_name ?: 'relatorio-vistoria.pdf')
                ->withMime($this->relatorio->mime_type ?: 'application/pdf'),
        ];
    }
}
