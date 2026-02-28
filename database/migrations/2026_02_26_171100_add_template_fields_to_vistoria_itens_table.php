<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('vistoria_itens', function (Blueprint $table) {
            $table->boolean('obrigatorio_foto_se_nao_ok')
                ->default(true)
                ->after('criticidade');

            $table->unsignedInteger('ordem')
                ->default(0)
                ->after('obrigatorio_foto_se_nao_ok');
        });
    }

    public function down(): void
    {
        Schema::table('vistoria_itens', function (Blueprint $table) {
            $table->dropColumn([
                'obrigatorio_foto_se_nao_ok',
                'ordem',
            ]);
        });
    }
};
