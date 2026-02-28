<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('blocos', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('condominio_id')->constrained('condominios')->cascadeOnDelete();
            $table->string('codigo', 30);
            $table->string('nome', 120);
            $table->integer('ordem')->default(0);
            $table->boolean('ativo')->default(true);
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['condominio_id', 'codigo']);
            $table->index(['condominio_id', 'ativo']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('blocos');
    }
};
