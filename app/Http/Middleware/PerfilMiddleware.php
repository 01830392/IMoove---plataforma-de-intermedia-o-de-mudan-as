<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class PerfilMiddleware
{
    public function handle(Request $request, Closure $next, string ...$perfis): mixed
    {
        $user = $request->user();

        if (!$user || !in_array($user->perfil, $perfis)) {
            return response()->json([
                'message' => 'Acesso não autorizado para este perfil.',
            ], 403);
        }

        return $next($request);
    }
}