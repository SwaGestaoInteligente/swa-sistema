<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vistoria_itens', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('condominio_id')->constrained('condominios')->cascadeOnDelete();
            $table->foreignUuid('vistoria_id')->constrained('vistorias')->cascadeOnDelete();
            $table->foreignUuid('area_id')->constrained('areas')->cascadeOnDelete();
            $table->string('item_codigo', 40)->nullable();
            $table->string('item_nome', 150);
            $table->enum('categoria', ['extintor', 'placa_saida', 'luz_emergencia', 'corrimao', 'sinalizacao', 'outro']);
            $table->enum('status', ['ok', 'danificado', 'ausente', 'improvisado'])->default('ok');
            $table->enum('criticidade', ['baixa', 'media', 'alta', 'critica'])->default('baixa');
            $table->string('foto_path', 255)->nullable();
            $table->text('observacao')->nullable();
            $table->dateTime('inspecionado_em')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['condominio_id', 'status']);
            $table->index(['vistoria_id', 'area_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vistoria_itens');
    }
};
