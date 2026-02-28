<?php

namespace Tests\Feature;

use App\Models\Area;
use App\Models\Bloco;
use App\Models\Condominio;
use App\Models\Pavimento;
use App\Models\User;
use App\Models\Vistoria;
use App\Models\VistoriaItem;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CondominioNavigationSmokeTest extends TestCase
{
    use RefreshDatabase;

    public function test_context_pages_open_without_500_errors(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $condominio = Condominio::query()->create([
            'codigo' => 'CND-001',
            'nome' => 'Condominio Teste',
            'timezone' => 'America/Sao_Paulo',
            'status' => 'ativo',
        ]);

        $condominio->usuariosSistema()->syncWithoutDetaching([
            $user->id => ['role' => 'admin'],
        ]);

        $bloco = Bloco::query()->create([
            'condominio_id' => $condominio->id,
            'codigo' => 'BL-A',
            'nome' => 'Bloco A',
            'ordem' => 1,
            'ativo' => true,
        ]);

        $pavimento = Pavimento::query()->create([
            'condominio_id' => $condominio->id,
            'bloco_id' => $bloco->id,
            'codigo' => 'PV-01',
            'nome' => 'Primeiro Pavimento',
            'nivel' => 1,
            'ordem' => 1,
            'ativo' => true,
        ]);

        $area = Area::query()->create([
            'condominio_id' => $condominio->id,
            'bloco_id' => $bloco->id,
            'pavimento_id' => $pavimento->id,
            'tipo' => 'externa',
            'codigo' => 'EXT-001',
            'nome' => 'Portaria Externa',
            'ativa' => true,
        ]);

        $vistoria = Vistoria::query()->create([
            'condominio_id' => $condominio->id,
            'codigo' => 'VIS-001',
            'tipo' => 'rotina',
            'status' => 'em_andamento',
            'competencia' => now()->toDateString(),
            'responsavel_nome' => 'Inspetor Teste',
            'risco_geral' => 0,
        ]);

        VistoriaItem::query()->create([
            'condominio_id' => $condominio->id,
            'vistoria_id' => $vistoria->id,
            'area_id' => $area->id,
            'item_codigo' => 'EXT-ITEM-001',
            'item_nome' => 'Extintor em bom estado',
            'categoria' => 'extintor',
            'status' => 'ok',
            'criticidade' => 'baixa',
        ]);

        $routes = [
            route('dashboard'),
            route('condominios.index'),
            route('condominios.context.dashboard', $condominio),
            route('condominios.context.blocos.index', $condominio),
            route('condominios.context.pavimentos.index', $condominio),
            route('condominios.context.unidades.index', $condominio),
            route('condominios.context.areas.index', $condominio),
            route('condominios.context.templates.index', $condominio),
            route('condominios.context.vistorias.index', $condominio),
            route('condominios.context.vistorias.wizard', $condominio),
            route('condominios.context.vistorias.create', $condominio),
            route('condominios.context.vistorias.show', ['condominio' => $condominio, 'vistoria' => $vistoria]),
            route('condominios.context.vistorias.edit', ['condominio' => $condominio, 'vistoria' => $vistoria]),
            route('condominios.context.vistorias.itens.create', ['condominio' => $condominio, 'vistoria' => $vistoria]),
            route('condominios.context.conflitos.index', $condominio),
            route('condominios.context.conflitos.create', $condominio),
            route('condominios.context.ocorrencias.index', $condominio),
            route('condominios.context.ocorrencias.create', $condominio),
            route('condominios.context.relatorios.index', $condominio),
            route('condominios.context.emails.index', $condominio),
            route('condominios.context.backups.index', $condominio),
        ];

        foreach ($routes as $url) {
            $response = $this->get($url);

            if ($response->isRedirection()) {
                $response = $this->followingRedirects()->get($url);
            }

            $response->assertOk();
        }
    }
}
