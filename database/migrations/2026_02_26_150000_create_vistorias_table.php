<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vistorias', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('condominio_id')->constrained('condominios')->cascadeOnDelete();
            $table->string('codigo', 30);
            $table->enum('tipo', ['rotina', 'extraordinaria', 'pos_ocorrencia'])->default('rotina');
            $table->enum('status', ['rascunho', 'em_andamento', 'finalizada', 'cancelada'])->default('rascunho');
            $table->date('competencia')->nullable();
            $table->dateTime('iniciada_em')->nullable();
            $table->dateTime('finalizada_em')->nullable();
            $table->string('responsavel_nome', 120)->nullable();
            $table->text('observacoes')->nullable();
            $table->unsignedSmallInteger('risco_geral')->default(0);
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['condominio_id', 'codigo']);
            $table->index(['condominio_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vistorias');
    }
};
