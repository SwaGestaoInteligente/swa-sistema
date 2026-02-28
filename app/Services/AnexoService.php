<?php

namespace App\Services;

use App\Models\Anexo;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AnexoService
{
    public function store(
        string $condominioId,
        Model $owner,
        UploadedFile $file,
        string $directory,
        ?int $uploadedBy = null,
        ?string $comentario = null
    ): Anexo {
        $disk = (string) config('filesystems.default', 'local');
        $extension = strtolower((string) ($file->getClientOriginalExtension() ?: $file->extension() ?: 'bin'));
        $filename = Str::uuid()->toString().'.'.$extension;
        $storedPath = trim($directory, '/').'/'.$filename;

        Storage::disk($disk)->put($storedPath, file_get_contents($file->getRealPath()));

        return $owner->anexos()->create([
            'condominio_id' => $condominioId,
            'disk' => $disk,
            'path' => $storedPath,
            'file_name' => $file->getClientOriginalName() ?: $filename,
            'mime_type' => $file->getClientMimeType(),
            'size' => (int) $file->getSize(),
            'comentario' => $comentario ? trim($comentario) : null,
            'uploaded_by' => $uploadedBy,
        ]);
    }

    public function delete(Anexo $anexo): void
    {
        $disk = $anexo->disk ?: config('filesystems.default', 'local');

        if ($anexo->path && Storage::disk($disk)->exists($anexo->path)) {
            Storage::disk($disk)->delete($anexo->path);
        }

        $anexo->delete();
    }

    public function download(Anexo $anexo, ?string $name = null): StreamedResponse
    {
        $disk = $anexo->disk ?: config('filesystems.default', 'local');

        return Storage::disk($disk)->download(
            $anexo->path,
            $name ?: $anexo->file_name
        );
    }

    public function allowedUploadRules(): array
    {
        return ['file', 'max:10240', 'mimes:jpg,jpeg,png,pdf'];
    }
}
