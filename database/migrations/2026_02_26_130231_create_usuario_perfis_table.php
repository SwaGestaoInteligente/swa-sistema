<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('usuario_perfis', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->foreignUuid('usuario_id')->constrained('usuarios')->cascadeOnDelete();
            $table->foreignUuid('condominio_id')->constrained('condominios')->cascadeOnDelete();
            $table->foreignId('perfil_id')->constrained('perfis')->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['usuario_id', 'condominio_id', 'perfil_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('usuario_perfis');
    }
};
