<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('condominio_user', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('condominio_id')->constrained('condominios')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->enum('role', ['admin', 'sindico', 'vistoriador', 'portaria'])->default('vistoriador');
            $table->timestamps();

            $table->unique(['condominio_id', 'user_id']);
            $table->index(['user_id', 'role']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('condominio_user');
    }
};
