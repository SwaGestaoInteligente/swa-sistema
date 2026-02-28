<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Renomear tabela principal
        Schema::rename('condominios', 'organizacoes');
        
        // 2. Adicionar campos de multi-org
        Schema::table('organizacoes', function (Blueprint $table) {
            $table->enum('tipo', ['condominio', 'clinica', 'empresa', 'igreja', 'pousada'])
                  ->default('condominio')
                  ->after('id');
            $table->json('modulos_ativos')->nullable()->after('tipo');
            $table->json('configuracoes')->nullable()->after('modulos_ativos');
        });
        
        // 3. Renomear foreign keys
        $tabelas = ['blocos', 'areas', 'checklist_templates', 'vistorias', 
                    'conflitos_moradores', 'ocorrencias_funcionarios', 'relatorios', 'backups'];
        
        foreach ($tabelas as $tabela) {
            if (Schema::hasColumn($tabela, 'condominio_id')) {
                Schema::table($tabela, function (Blueprint $table) {
                    $table->renameColumn('condominio_id', 'organizacao_id');
                });
            }
        }
        
        // 4. Renomear tabelas pivot
        Schema::rename('condominio_user', 'organizacao_user');
        Schema::rename('condominio_emails', 'organizacao_emails');
        
        Schema::table('organizacao_user', function (Blueprint $table) {
            $table->renameColumn('condominio_id', 'organizacao_id');
        });
        
        Schema::table('organizacao_emails', function (Blueprint $table) {
            $table->renameColumn('condominio_id', 'organizacao_id');
        });
    }

    public function down(): void
    {
        Schema::rename('organizacoes', 'condominios');
        Schema::table('condominios', function (Blueprint $table) {
            $table->dropColumn(['tipo', 'modulos_ativos', 'configuracoes']);
        });
    }
};