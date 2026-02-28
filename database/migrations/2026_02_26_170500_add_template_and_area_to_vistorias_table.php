<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('vistorias', function (Blueprint $table) {
            $table->foreignUuid('area_id')
                ->nullable()
                ->after('condominio_id')
                ->constrained('areas')
                ->nullOnDelete();

            $table->foreignUuid('checklist_template_id')
                ->nullable()
                ->after('area_id')
                ->constrained('checklist_templates')
                ->nullOnDelete();

            $table->index(['condominio_id', 'checklist_template_id']);
        });
    }

    public function down(): void
    {
        Schema::table('vistorias', function (Blueprint $table) {
            $table->dropIndex(['condominio_id', 'checklist_template_id']);
            $table->dropConstrainedForeignId('checklist_template_id');
            $table->dropConstrainedForeignId('area_id');
        });
    }
};
