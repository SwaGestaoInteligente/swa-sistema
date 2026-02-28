<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        if (! DB::getSchemaBuilder()->hasTable('anexos') || ! DB::getSchemaBuilder()->hasTable('vistoria_itens')) {
            return;
        }

        $items = DB::table('vistoria_itens')
            ->select(['id', 'condominio_id', 'foto_path'])
            ->whereNotNull('foto_path')
            ->where('foto_path', '!=', '')
            ->get();

        foreach ($items as $item) {
            $exists = DB::table('anexos')
                ->where('owner_type', 'App\\Models\\VistoriaItem')
                ->where('owner_id', $item->id)
                ->where('path', $item->foto_path)
                ->exists();

            if ($exists) {
                continue;
            }

            DB::table('anexos')->insert([
                'id' => (string) Str::uuid(),
                'condominio_id' => $item->condominio_id,
                'owner_type' => 'App\\Models\\VistoriaItem',
                'owner_id' => $item->id,
                'disk' => config('filesystems.default', 'local'),
                'path' => $item->foto_path,
                'file_name' => basename($item->foto_path),
                'mime_type' => null,
                'size' => 0,
                'uploaded_by' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    public function down(): void
    {
        DB::table('anexos')
            ->where('owner_type', 'App\\Models\\VistoriaItem')
            ->delete();
    }
};
