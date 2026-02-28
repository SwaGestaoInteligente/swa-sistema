<?php

namespace App\Services;

use App\Models\Backup;
use App\Models\Condominio;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use RuntimeException;
use ZipArchive;

class BackupService
{
    public function generateCondominioBackup(Condominio $condominio, ?User $generatedBy = null): Backup
    {
        $disk = config('filesystems.default', 'local');
        $timestamp = now()->format('Ymd_His');
        $safeCodigo = Str::slug((string) ($condominio->codigo ?: $condominio->nome ?: 'condominio'));
        $fileName = "backup-{$safeCodigo}-{$timestamp}.zip";
        $storagePath = "backups/condominios/{$condominio->id}/{$fileName}";
        $absolutePath = storage_path("app/{$storagePath}");

        if (! class_exists(ZipArchive::class)) {
            throw new RuntimeException('ZipArchive não disponível no servidor para geração de backup.');
        }

        $directory = dirname($absolutePath);
        if (! is_dir($directory)) {
            mkdir($directory, 0755, true);
        }

        $dataset = $this->collectCondominioDataset($condominio);

        $zip = new ZipArchive();
        $opened = $zip->open($absolutePath, ZipArchive::CREATE | ZipArchive::OVERWRITE);

        if ($opened !== true) {
            throw new RuntimeException('Não foi possível criar o arquivo de backup.');
        }

        $zip->addFromString(
            'database.json',
            json_encode($dataset, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT)
        );

        $filesManifest = $this->addFileEntries($zip, $dataset['tables']['anexos'] ?? [], 'files/anexos');
        $reportsManifest = $this->addFileEntries($zip, $dataset['tables']['relatorios'] ?? [], 'files/relatorios');

        $zip->addFromString(
            'manifest.json',
            json_encode([
                'generated_at' => now()->toIso8601String(),
                'condominio_id' => (string) $condominio->id,
                'condominio_nome' => $condominio->nome,
                'app_env' => config('app.env'),
                'files' => [
                    'anexos' => $filesManifest,
                    'relatorios' => $reportsManifest,
                ],
            ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT)
        );

        $zip->close();

        $payload = @file_get_contents($absolutePath);
        if ($payload === false) {
            throw new RuntimeException('Nao foi possivel ler o arquivo de backup gerado.');
        }

        $size = (int) (is_file($absolutePath) ? filesize($absolutePath) : 0);

        return Backup::query()->create([
            'condominio_id' => $condominio->id,
            'scope' => 'condominio',
            'disk' => $disk,
            'path' => $storagePath,
            'file_name' => $fileName,
            'mime_type' => 'application/zip',
            'size' => $size,
            'status' => 'gerado',
            'notes' => 'Snapshot de dados + manifest + arquivos vinculados (quando encontrados). Mantem copia no banco para download em ambiente multi-maquina.',
            'payload_base64' => base64_encode($payload),
            'generated_by' => $generatedBy?->id,
        ]);
    }

    public function download(Backup $backup)
    {
        if (Storage::disk($backup->disk)->exists($backup->path)) {
            return Storage::disk($backup->disk)->download($backup->path, $backup->file_name);
        }

        if (! empty($backup->payload_base64)) {
            $decoded = base64_decode($backup->payload_base64, true);

            if ($decoded === false) {
                throw new RuntimeException('Backup persistido no banco esta corrompido.');
            }

            return Response::streamDownload(function () use ($decoded): void {
                echo $decoded;
            }, $backup->file_name, [
                'Content-Type' => $backup->mime_type ?: 'application/zip',
                'Content-Length' => (string) strlen($decoded),
            ]);
        }

        throw new RuntimeException('Arquivo de backup nao esta disponivel para download.');
    }

    public function delete(Backup $backup): void
    {
        if (Storage::disk($backup->disk)->exists($backup->path)) {
            Storage::disk($backup->disk)->delete($backup->path);
        }

        $backup->delete();
    }

    private function collectCondominioDataset(Condominio $condominio): array
    {
        $templateIds = DB::table('checklist_templates')
            ->where('condominio_id', $condominio->id)
            ->pluck('id');

        $pivotRows = DB::table('condominio_user')
            ->where('condominio_id', $condominio->id)
            ->get();

        $userIds = $pivotRows->pluck('user_id')->filter()->values();

        $tables = [
            'condominios' => DB::table('condominios')->where('id', $condominio->id)->get(),
            'condominio_user' => $pivotRows,
            'users' => $this->tableWhereIn('users', 'id', $userIds),
            'usuarios' => DB::table('usuarios')->where('condominio_id', $condominio->id)->get(),
            'usuario_perfis' => DB::table('usuario_perfis')->where('condominio_id', $condominio->id)->get(),
            'perfis' => DB::table('perfis')->get(),
            'blocos' => DB::table('blocos')->where('condominio_id', $condominio->id)->get(),
            'pavimentos' => DB::table('pavimentos')->where('condominio_id', $condominio->id)->get(),
            'unidades' => DB::table('unidades')->where('condominio_id', $condominio->id)->get(),
            'areas' => DB::table('areas')->where('condominio_id', $condominio->id)->get(),
            'checklist_templates' => DB::table('checklist_templates')->where('condominio_id', $condominio->id)->get(),
            'checklist_template_itens' => $this->tableWhereIn('checklist_template_itens', 'template_id', $templateIds),
            'vistorias' => DB::table('vistorias')->where('condominio_id', $condominio->id)->get(),
            'vistoria_itens' => DB::table('vistoria_itens')->where('condominio_id', $condominio->id)->get(),
            'conflitos_moradores' => DB::table('conflitos_moradores')->where('condominio_id', $condominio->id)->get(),
            'ocorrencias_funcionarios' => DB::table('ocorrencias_funcionarios')->where('condominio_id', $condominio->id)->get(),
            'anexos' => DB::table('anexos')->where('condominio_id', $condominio->id)->get(),
            'relatorios' => DB::table('relatorios')->where('condominio_id', $condominio->id)->get(),
            'condominio_emails' => DB::table('condominio_emails')->where('condominio_id', $condominio->id)->get(),
            'backups' => DB::table('backups')->where('condominio_id', $condominio->id)->get(),
        ];

        return [
            'meta' => [
                'schema_version' => 1,
                'generated_at' => now()->toIso8601String(),
                'generated_at_human' => Carbon::now()->format('d/m/Y H:i:s'),
                'app_name' => config('app.name'),
                'app_env' => config('app.env'),
                'condominio_id' => (string) $condominio->id,
                'condominio_nome' => $condominio->nome,
                'condominio_codigo' => $condominio->codigo,
            ],
            'tables' => collect($tables)
                ->map(fn (Collection $rows) => $rows->map(fn ($row) => (array) $row)->values()->all())
                ->all(),
        ];
    }

    private function tableWhereIn(string $table, string $column, Collection $values): Collection
    {
        if ($values->isEmpty()) {
            return collect();
        }

        return DB::table($table)->whereIn($column, $values->all())->get();
    }

    /**
     * @param array<int, array<string, mixed>> $items
     * @return array<int, array<string, mixed>>
     */
    private function addFileEntries(ZipArchive $zip, array $items, string $zipFolder): array
    {
        $manifest = [];

        foreach ($items as $index => $item) {
            $disk = (string) ($item['disk'] ?? 'local');
            $path = (string) ($item['path'] ?? '');
            $fileName = (string) ($item['file_name'] ?? basename($path));

            if ($path === '' || ! Storage::disk($disk)->exists($path)) {
                $manifest[] = [
                    'path' => $path,
                    'file_name' => $fileName,
                    'status' => 'not_found',
                ];
                continue;
            }

            $stream = Storage::disk($disk)->readStream($path);
            if (! is_resource($stream)) {
                $manifest[] = [
                    'path' => $path,
                    'file_name' => $fileName,
                    'status' => 'unreadable',
                ];
                continue;
            }

            $safeName = preg_replace('/[^A-Za-z0-9\.\-_]+/', '_', $fileName) ?: "arquivo-{$index}";
            $entryName = "{$zipFolder}/{$index}-{$safeName}";
            $zip->addFromString($entryName, stream_get_contents($stream) ?: '');
            fclose($stream);

            $manifest[] = [
                'path' => $path,
                'file_name' => $fileName,
                'zip_entry' => $entryName,
                'status' => 'ok',
            ];
        }

        return $manifest;
    }
}
