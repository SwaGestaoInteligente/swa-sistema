<?php

namespace App\Http\Middleware;

use App\Models\Condominio;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureCondominioAccess
{
    public function handle(Request $request, Closure $next): Response
    {
        $condominio = $request->route('condominio');

        if (! $condominio instanceof Condominio) {
            return $next($request);
        }

        $user = $request->user();

        if (! $user) {
            abort(401);
        }

        if (! $user->can('view', $condominio)) {
            abort(403, 'Você não possui acesso a este condomínio.');
        }

        return $next($request);
    }
}
