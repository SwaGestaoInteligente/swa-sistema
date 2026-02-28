<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('condominios', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('codigo', 20)->unique();
            $table->string('nome', 150);
            $table->string('cnpj', 14)->nullable()->unique();
            $table->string('cep', 8)->nullable();
            $table->string('logradouro', 150)->nullable();
            $table->string('numero', 20)->nullable();
            $table->string('bairro', 100)->nullable();
            $table->string('cidade', 100)->nullable();
            $table->string('uf', 2)->nullable();
            $table->string('timezone', 60)->default('America/Sao_Paulo');
            $table->enum('status', ['ativo', 'inativo', 'suspenso'])->default('ativo');
            $table->timestamps();
            $table->softDeletes();

            $table->index('status');
            $table->index(['cidade', 'uf']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('condominios');
    }
};
