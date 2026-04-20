<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Verificar se usuário está autenticado e é admin
        if (!auth()->check() || auth()->user()->tipo_usuario !== 'admin') {
            // Se não for admin, redirecionar para login com mensagem
            return redirect('/admin/login')->with('error', 'Acesso restrito para administradores!');
        }
        
        return $next($request);
    }
}
