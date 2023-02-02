<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class ProductPermissions
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\JsonResponse
     */
    public function handle(Request $request, Closure $next)
    {
        if (auth()->user()->roles[0]->name == 'Administrador' || auth()->user()->roles[0]->name == 'Empleado'){
            return $next ($request);
        }else{
            return response()->json(['No tienes permisos para realizar esta acción'], 401);
        }
    }
}
