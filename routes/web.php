<?php

use App\Http\Controllers\AnexoController;
use App\Http\Controllers\AreaController;
use App\Http\Controllers\BackupController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\BlocoController;
use App\Http\Controllers\ChecklistTemplateController;
use App\Http\Controllers\CondominioController;
use App\Http\Controllers\CondominioEmailController;
use App\Http\Controllers\ConflitoMoradorController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\HelpController;
use App\Http\Controllers\OcorrenciaFuncionarioController;
use App\Http\Controllers\PavimentoController;
use App\Http\Controllers\RelatorioController;
use App\Http\Controllers\UnidadeController;
use App\Http\Controllers\VistoriaController;
use App\Http\Controllers\VistoriaItemController;
use App\Models\Anexo;
use App\Models\Area;
use App\Models\Backup;
use App\Models\Bloco;
use App\Models\ChecklistTemplate;
use App\Models\Condominio;
use App\Models\CondominioEmail;
use App\Models\ConflitoMorador;
use App\Models\OcorrenciaFuncionario;
use App\Models\Pavimento;
use App\Models\Relatorio;
use App\Models\Unidade;
use App\Models\Vistoria;
use App\Models\VistoriaItem;
use Illuminate\Support\Facades\Route;

Route::bind('condominio', fn (string $value) => Condominio::query()->whereKey($value)->firstOrFail());
Route::bind('bloco', fn (string $value) => Bloco::query()->whereKey($value)->firstOrFail());
Route::bind('pavimento', fn (string $value) => Pavimento::query()->whereKey($value)->firstOrFail());
Route::bind('unidade', fn (string $value) => Unidade::query()->whereKey($value)->firstOrFail());
Route::bind('area', fn (string $value) => Area::query()->whereKey($value)->firstOrFail());
Route::bind('template', fn (string $value) => ChecklistTemplate::query()->whereKey($value)->firstOrFail());
Route::bind('vistoria', fn (string $value) => Vistoria::query()->whereKey($value)->firstOrFail());
Route::bind('item', fn (string $value) => VistoriaItem::query()->whereKey($value)->firstOrFail());
Route::bind('conflito', fn (string $value) => ConflitoMorador::query()->whereKey($value)->firstOrFail());
Route::bind('ocorrencia', fn (string $value) => OcorrenciaFuncionario::query()->whereKey($value)->firstOrFail());
Route::bind('relatorio', fn (string $value) => Relatorio::query()->whereKey($value)->firstOrFail());
Route::bind('email', fn (string $value) => CondominioEmail::query()->whereKey($value)->firstOrFail());
Route::bind('anexo', fn (string $value) => Anexo::query()->whereKey($value)->firstOrFail());
Route::bind('backup', fn (string $value) => Backup::query()->whereKey($value)->firstOrFail());

Route::get('/', function () {
    return auth()->check()
        ? redirect()->route('dashboard')
        : redirect()->route('login');
});

Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'create'])->name('login');
    Route::post('/login', [LoginController::class, 'store'])->name('login.store');
});

Route::get('/relatorios/{relatorio}/download-assinado', [RelatorioController::class, 'signedDownload'])
    ->name('relatorios.download.signed')
    ->middleware('signed');

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', DashboardController::class)->name('dashboard');
    Route::get('/ajuda', [HelpController::class, 'index'])->name('ajuda.index');
    Route::post('/logout', [LoginController::class, 'destroy'])->name('logout');

    Route::resource('condominios', CondominioController::class)->except(['show']);
    Route::get('/condominios/{condominio}', [CondominioController::class, 'contextRedirect'])
        ->middleware('condominio.access')
        ->name('condominios.context');

    Route::name('condominios.context.')
        ->prefix('/condominios/{condominio}')
        ->middleware('condominio.access')
        ->group(function () {
            Route::get('/dashboard', [CondominioController::class, 'dashboard'])->name('dashboard');
            Route::get('/ajuda', [HelpController::class, 'context'])->name('ajuda');

            Route::prefix('/estrutura')->group(function () {
                Route::resource('blocos', BlocoController::class)
                    ->except(['show'])
                    ->names('blocos');

                Route::resource('pavimentos', PavimentoController::class)
                    ->except(['show'])
                    ->names('pavimentos');

                Route::resource('unidades', UnidadeController::class)
                    ->except(['show'])
                    ->names('unidades');

                Route::resource('areas', AreaController::class)
                    ->except(['show'])
                    ->names('areas');
            });

            Route::resource('templates', ChecklistTemplateController::class)
                ->except(['show'])
                ->names('templates');

            Route::prefix('/vistorias')->name('vistorias.')->group(function () {
                Route::get('/assistente', [VistoriaController::class, 'wizard'])->name('wizard');
                Route::get('/', [VistoriaController::class, 'index'])->name('index');
                Route::get('/create', [VistoriaController::class, 'create'])->name('create');
                Route::post('/', [VistoriaController::class, 'store'])->name('store');
                Route::get('/{vistoria}', [VistoriaController::class, 'show'])->name('show');
                Route::get('/{vistoria}/edit', [VistoriaController::class, 'edit'])->name('edit');
                Route::put('/{vistoria}', [VistoriaController::class, 'update'])->name('update');
                Route::delete('/{vistoria}', [VistoriaController::class, 'destroy'])->name('destroy');
                Route::post('/{vistoria}/finalizar', [VistoriaController::class, 'finalizar'])->name('finalizar');
                Route::post('/{vistoria}/reabrir', [VistoriaController::class, 'reabrir'])->name('reabrir');
                Route::post('/{vistoria}/aplicar-template', [VistoriaController::class, 'aplicarTemplate'])->name('aplicar-template');
                Route::get('/{vistoria}/pdf', [VistoriaController::class, 'pdf'])->name('pdf');

                Route::get('/{vistoria}/itens/create', [VistoriaItemController::class, 'create'])->name('itens.create');
                Route::post('/{vistoria}/itens', [VistoriaItemController::class, 'store'])->name('itens.store');
                Route::put('/{vistoria}/itens/{item}', [VistoriaItemController::class, 'update'])->name('itens.update');
                Route::delete('/{vistoria}/itens/{item}', [VistoriaItemController::class, 'destroy'])->name('itens.destroy');
            });

            Route::prefix('/conflitos-moradores')->name('conflitos.')->group(function () {
                Route::get('/', [ConflitoMoradorController::class, 'index'])->name('index');
                Route::get('/create', [ConflitoMoradorController::class, 'create'])->name('create');
                Route::post('/', [ConflitoMoradorController::class, 'store'])->name('store');
                Route::get('/{conflito}/edit', [ConflitoMoradorController::class, 'edit'])->name('edit');
                Route::put('/{conflito}', [ConflitoMoradorController::class, 'update'])->name('update');
                Route::delete('/{conflito}', [ConflitoMoradorController::class, 'destroy'])->name('destroy');
                Route::post('/{conflito}/status', [ConflitoMoradorController::class, 'alterarStatus'])->name('status');
            });

            Route::prefix('/ocorrencias-funcionarios')->name('ocorrencias.')->group(function () {
                Route::get('/', [OcorrenciaFuncionarioController::class, 'index'])->name('index');
                Route::get('/create', [OcorrenciaFuncionarioController::class, 'create'])->name('create');
                Route::post('/', [OcorrenciaFuncionarioController::class, 'store'])->name('store');
                Route::get('/{ocorrencia}/edit', [OcorrenciaFuncionarioController::class, 'edit'])->name('edit');
                Route::put('/{ocorrencia}', [OcorrenciaFuncionarioController::class, 'update'])->name('update');
                Route::delete('/{ocorrencia}', [OcorrenciaFuncionarioController::class, 'destroy'])->name('destroy');
                Route::post('/{ocorrencia}/status', [OcorrenciaFuncionarioController::class, 'alterarStatus'])->name('status');
            });

            Route::prefix('/relatorios')->name('relatorios.')->group(function () {
                Route::get('/', [RelatorioController::class, 'index'])->name('index');
                Route::post('/vistorias/{vistoria}/gerar', [RelatorioController::class, 'gerarVistoria'])->name('vistorias.gerar');
                Route::get('/{relatorio}/download', [RelatorioController::class, 'download'])->name('download');
                Route::post('/{relatorio}/enviar', [RelatorioController::class, 'enviar'])->name('enviar');
            });

            Route::prefix('/config/emails')->name('emails.')->group(function () {
                Route::get('/', [CondominioEmailController::class, 'index'])->name('index');
                Route::post('/', [CondominioEmailController::class, 'store'])->name('store');
                Route::put('/{email}', [CondominioEmailController::class, 'update'])->name('update');
                Route::delete('/{email}', [CondominioEmailController::class, 'destroy'])->name('destroy');
            });

            Route::prefix('/backups')->name('backups.')->group(function () {
                Route::get('/', [BackupController::class, 'index'])->name('index');
                Route::post('/', [BackupController::class, 'store'])->name('store');
                Route::get('/{backup}/download', [BackupController::class, 'download'])->name('download');
                Route::delete('/{backup}', [BackupController::class, 'destroy'])->name('destroy');
            });

            Route::prefix('/anexos')->name('anexos.')->group(function () {
                Route::get('/{anexo}/download', [AnexoController::class, 'download'])->name('download');
                Route::get('/{anexo}/download-assinado', [AnexoController::class, 'signedDownload'])
                    ->middleware('signed')
                    ->name('download.signed');
                Route::delete('/{anexo}', [AnexoController::class, 'destroy'])->name('destroy');
            });
        });
});
