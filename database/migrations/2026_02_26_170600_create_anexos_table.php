<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('anexos', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('condominio_id')->constrained('condominios')->cascadeOnDelete();
            $table->string('owner_type', 120);
            $table->uuid('owner_id');
            $table->string('disk', 40);
            $table->string('path', 255);
            $table->string('file_name', 180);
            $table->string('mime_type', 120)->nullable();
            $table->unsignedBigInteger('size')->default(0);
            $table->foreignId('uploaded_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['owner_type', 'owner_id']);
            $table->index(['condominio_id', 'owner_type', 'owner_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('anexos');
    }
};
