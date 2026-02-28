<?php

namespace App\Providers;

use App\Models\Anexo;
use App\Models\Backup;
use App\Models\Condominio;
use App\Models\Relatorio;
use App\Models\User;
use App\Policies\AnexoPolicy;
use App\Policies\BackupPolicy;
use App\Policies\CondominioPolicy;
use App\Policies\RelatorioPolicy;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\URL;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Gate::policy(Condominio::class, CondominioPolicy::class);
        Gate::policy(Anexo::class, AnexoPolicy::class);
        Gate::policy(Relatorio::class, RelatorioPolicy::class);
        Gate::policy(Backup::class, BackupPolicy::class);

        Gate::define('condominio.hasRole', function (User $user, Condominio $condominio, array $roles): bool {
            if (! $user->canAccessCondominio((string) $condominio->id)) {
                return false;
            }

            if ($user->isPlatformAdmin()) {
                return true;
            }

            return in_array($user->roleOnCondominio((string) $condominio->id), $roles, true);
        });

        if ($this->app->environment('production') && config('app.url')) {
            URL::forceRootUrl(config('app.url'));
            URL::forceScheme('https');
        }
    }
}
