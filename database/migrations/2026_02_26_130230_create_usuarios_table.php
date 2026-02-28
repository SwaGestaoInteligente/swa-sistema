<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('usuarios', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('condominio_id')->constrained('condominios');
            $table->string('nome', 150);
            $table->string('email', 150);
            $table->string('telefone', 20)->nullable();
            $table->string('password');
            $table->enum('tipo', ['morador', 'funcionario', 'gestor', 'sistema']);
            $table->boolean('ativo')->default(true);
            $table->timestamp('ultimo_login_at')->nullable();
            $table->boolean('force_password_change')->default(false);
            $table->rememberToken();
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['condominio_id', 'email']);
            $table->index(['condominio_id', 'tipo']);
            $table->index(['condominio_id', 'ativo']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('usuarios');
    }
};
