<?php

namespace App\Http\Middleware;

use Closure;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;

class RoleMiddleware
{
    public function handle($request, Closure $next, ...$roles)
    {
        try {
            $user = JWTAuth::parseToken()->authenticate();
            if (!in_array($user->role, $roles)) {
                return response()->json(['error' => 'Acceso denegado. Rol: ' . $user->role . ', Requerido: ' . implode('|', $roles)], 403);
            }
        } catch (JWTException $e) {
            return response()->json(['error' => 'Token inválido'], 401);
        }

        return $next($request);
    }
}
