<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('backups', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('condominio_id')->nullable()->constrained('condominios')->nullOnDelete();
            $table->string('scope', 24)->default('condominio');
            $table->string('disk', 40)->default('local');
            $table->string('path', 255);
            $table->string('file_name', 180);
            $table->string('mime_type', 120)->nullable();
            $table->unsignedBigInteger('size')->default(0);
            $table->string('status', 20)->default('gerado');
            $table->text('notes')->nullable();
            $table->foreignId('generated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['condominio_id', 'created_at']);
            $table->index(['scope', 'created_at']);
            $table->index(['generated_by', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('backups');
    }
};

