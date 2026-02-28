<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('checklist_template_itens', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('template_id')->constrained('checklist_templates')->cascadeOnDelete();
            $table->string('titulo_item', 150);
            $table->enum('categoria', ['extintor', 'placa_saida', 'luz_emergencia', 'corrimao', 'sinalizacao', 'outro']);
            $table->boolean('obrigatorio_foto_se_nao_ok')->default(true);
            $table->integer('ordem')->default(0);
            $table->timestamps();

            $table->index(['template_id', 'ordem']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('checklist_template_itens');
    }
};
