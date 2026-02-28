<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ocorrencias_funcionarios', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('condominio_id')->constrained('condominios')->cascadeOnDelete();
            $table->string('protocolo', 40);
            $table->dateTime('ocorrido_em');
            $table->foreignUuid('funcionario_id')->nullable()->constrained('usuarios')->nullOnDelete();
            $table->string('funcionario_nome', 150)->nullable();
            $table->string('cargo', 100)->nullable();
            $table->enum('tipo', ['atraso', 'desrespeito', 'falha_operacional', 'negligencia', 'falta_grave', 'outro']);
            $table->text('relato_detalhado');
            $table->string('testemunha_nome', 150)->nullable();
            $table->string('testemunha_contato', 100)->nullable();
            $table->string('medida_aplicada', 150)->nullable();
            $table->enum('status', ['registrada', 'advertencia', 'suspensao', 'encaminhado_juridico', 'encerrada'])->default('registrada');
            $table->unsignedSmallInteger('reincidencia_nivel')->default(0);
            $table->string('registrado_por', 120)->nullable();
            $table->dateTime('encerrado_em')->nullable();
            $table->json('historico_snapshot')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['condominio_id', 'protocolo']);
            $table->index(['condominio_id', 'status']);
            $table->index(['condominio_id', 'tipo']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ocorrencias_funcionarios');
    }
};
