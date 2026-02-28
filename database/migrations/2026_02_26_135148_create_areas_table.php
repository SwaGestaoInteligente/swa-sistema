<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('areas', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('condominio_id')->constrained('condominios')->cascadeOnDelete();
            $table->foreignUuid('bloco_id')->nullable()->constrained('blocos')->nullOnDelete();
            $table->foreignUuid('pavimento_id')->nullable()->constrained('pavimentos')->nullOnDelete();
            $table->enum('tipo', ['externa', 'comum', 'tecnica', 'seguranca']);
            $table->string('codigo', 30);
            $table->string('nome', 120);
            $table->text('descricao')->nullable();
            $table->boolean('ativa')->default(true);
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['condominio_id', 'codigo']);
            $table->index(['condominio_id', 'tipo']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('areas');
    }
};
