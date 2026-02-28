<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('unidades', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('condominio_id')->constrained('condominios')->cascadeOnDelete();
            $table->foreignUuid('bloco_id')->constrained('blocos')->cascadeOnDelete();
            $table->foreignUuid('pavimento_id')->constrained('pavimentos')->cascadeOnDelete();
            $table->string('numero', 30);
            $table->enum('tipo', ['apto', 'sala'])->default('apto');
            $table->enum('status', ['ocupado', 'vago'])->default('ocupado');
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['condominio_id', 'numero']);
            $table->index(['condominio_id', 'bloco_id', 'pavimento_id']);
            $table->index(['condominio_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('unidades');
    }
};
