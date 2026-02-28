<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pavimentos', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('condominio_id')->constrained('condominios')->cascadeOnDelete();
            $table->foreignUuid('bloco_id')->constrained('blocos')->cascadeOnDelete();
            $table->string('codigo', 30);
            $table->string('nome', 120);
            $table->integer('nivel')->default(0);
            $table->integer('ordem')->default(0);
            $table->boolean('ativo')->default(true);
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['bloco_id', 'codigo']);
            $table->index(['condominio_id', 'bloco_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pavimentos');
    }
};
