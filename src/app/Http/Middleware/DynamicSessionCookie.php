<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Str;

class DynamicSessionCookie
{
    public function handle($request, Closure $next)
    {
        $sessionName = Str::slug(config('app.name', 'laravel'), '_') . '_' .
            ($request->is('admin/*') ? 'admin' : 'staff') . '_session';

        config(['session.cookie' => $sessionName]);

        return $next($request);
    }
}
