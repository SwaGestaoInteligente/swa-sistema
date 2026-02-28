<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('anexos', function (Blueprint $table) {
            $table->string('comentario', 500)->nullable()->after('size');
        });
    }

    public function down(): void
    {
        Schema::table('anexos', function (Blueprint $table) {
            $table->dropColumn('comentario');
        });
    }
};

