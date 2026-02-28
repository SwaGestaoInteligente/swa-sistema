<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('conflitos_moradores', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('condominio_id')->constrained('condominios')->cascadeOnDelete();
            $table->string('protocolo', 40);
            $table->dateTime('ocorrido_em');
            $table->foreignUuid('morador_a_id')->nullable()->constrained('usuarios')->nullOnDelete();
            $table->string('morador_a_nome', 150)->nullable();
            $table->foreignUuid('morador_b_id')->nullable()->constrained('usuarios')->nullOnDelete();
            $table->string('morador_b_nome', 150)->nullable();
            $table->string('unidade', 40)->nullable();
            $table->enum('tipo', ['barulho', 'ameaca', 'vaga', 'ofensa', 'uso_indevido_area_comum', 'outro']);
            $table->text('relato');
            $table->json('testemunhas')->nullable();
            $table->text('tentativa_mediacao')->nullable();
            $table->enum('status', ['em_analise', 'advertido', 'resolvido', 'judicial'])->default('em_analise');
            $table->string('registrado_por', 120)->nullable();
            $table->string('tratado_por', 120)->nullable();
            $table->dateTime('resolvido_em')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['condominio_id', 'protocolo']);
            $table->index(['condominio_id', 'status']);
            $table->index(['condominio_id', 'tipo']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('conflitos_moradores');
    }
};
