<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AuthenticatePdv
{
    public function handle(Request $request, Closure $next): Response
    {
        $token = (string) config('pdv.sync_token');
        $providedToken = (string) $request->bearerToken();

        if (strlen($token) < 32) {
            return response()->json(['message' => 'A integração do PDV não está configurada corretamente.'], 503);
        }

        if ($providedToken === '' || ! hash_equals($token, $providedToken)) {
            return response()->json(['message' => 'Acesso não autorizado.'], 401);
        }

        return $next($request);
    }
}
