<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('relatorios', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('condominio_id')->constrained('condominios')->cascadeOnDelete();
            $table->string('type', 40);
            $table->uuid('ref_id')->nullable();
            $table->string('disk', 40);
            $table->string('path', 255);
            $table->string('file_name', 180);
            $table->string('mime_type', 120)->nullable();
            $table->unsignedBigInteger('size')->default(0);
            $table->foreignId('generated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['condominio_id', 'type']);
            $table->index(['type', 'ref_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('relatorios');
    }
};
