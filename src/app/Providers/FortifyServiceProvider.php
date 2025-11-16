<?php

namespace App\Providers;

use App\Actions\Fortify\CreateNewUser;
use App\Actions\Fortify\ResetUserPassword;
use App\Actions\Fortify\UpdateUserPassword;
use App\Actions\Fortify\UpdateUserProfileInformation;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Laravel\Fortify\Fortify;

class FortifyServiceProvider extends ServiceProvider
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
    public function boot()
    {
        Fortify::createUsersUsing(CreateNewUser::class);
        Fortify::registerView(function() {
            if(Auth::guard('staff')->check()){
                return '/attendance';
            } elseif (Auth::guard('admin')->check()){
                return '/admin/dashboard';
            }
            return '/login';
        });
        Fortify::loginView(function(){
            return view('auth.login');
        });
        Fortify::authenticateUsing(function (Request $request) {
            $credentials = $loginRequest->only('email','password');

            if (Auth::guard('admin')->attempt($credentials, $request->filled('remember'))){
                return Auth::guard('admin')->user();
            }

            if (Auth::guard('staff')->attempt($credentials, $request->filled('remember'))) {
                return Auth::guard('staff')->user();
            }

            throw ValidationException::withMessage([
                'email' => __('auth.failed'),
            ]);
        });

        RateLimiter::for('login', function (Request $request) {
            $email = (string) $request->email;
            return Limit::perMinute(10)->by($email . $request->ip());
        });

    }
}
