<?php

namespace App\Http\Middleware;

use App\Providers\RouteServiceProvider;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RedirectIfAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @param  string|null  ...$guards
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next, ...$guards)
    {
        $guards = empty($guards) ? [null] : $guards;

        dd([
            'admin_guard_logged_in?' => Auth::guard('admin')->check(),
            'staff_guard_logged_in?' => Auth::guard('staff')->check(),
        ]);

        foreach ($guards as $guard) {
            if (Auth::guard($guard)->check()) {
                logger('RedirectIfAuthenticated fired', ['guard' => $guard]);

                switch ($guard) {
                    case 'admin':
                        return redirect('/admin/attendance');
                    case 'staff':
                        return redirect('/attendance');
                    default:
                        return redirect('/login');
                }
            }
        }

        return $next($request);
    }
}
