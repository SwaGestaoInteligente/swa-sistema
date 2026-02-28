<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('perfis', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('slug')->unique();
            $table->string('nome', 120);
            $table->string('descricao', 255)->nullable();
            $table->enum('escopo', ['global', 'condominio'])->default('condominio');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('perfis');
    }
};
