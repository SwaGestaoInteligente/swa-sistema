<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('condominio_emails', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('condominio_id')->constrained('condominios')->cascadeOnDelete();
            $table->string('nome', 120);
            $table->string('email', 150);
            $table->enum('tipo', ['sindico', 'conselho', 'adm', 'outros'])->default('outros');
            $table->boolean('ativo')->default(true);
            $table->timestamps();

            $table->index(['condominio_id', 'ativo']);
            $table->unique(['condominio_id', 'email']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('condominio_emails');
    }
};
