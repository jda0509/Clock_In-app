<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Models\Staff;
use Illuminate\Support\Facades\Auth;
use App\Providers\RouteServiceProvider;

class LoginController extends Controller
{
    public function index()
    {
        return view('login');
    }

    public function adminIndex()
    {
        return view('admin.login');
    }

    public function create()
    {
        return view('register');
    }

    public function login(LoginRequest $request)
    {
        $credentials = $request->only('email', 'password');

        if (Auth::guard('admin')->attempt($credentials)) {
            return redirect()->intended(RouteServiceProvider::ADMIN_HOME);
        }

        $staff = Staff::where('email', $credentials['email'])->first();

        if (!$staff) {
            return back()->withErrors([
                'email' => 'ログイン情報が登録されていません。'
            ]);
        }

        if (!$staff->hasVerifiedEmail()) {
            return redirect()->route('verification.notice')
                ->withErrors(['email' => 'メール認証が完了していません。メールをご確認ください。']);
        }

        if (Auth::guard('staff')->attempt($credentials)) {
            $request->session()->regenerate();
            return redirect()->intended(RouteServiceProvider::STAFF_HOME);
        }

        return back()->withErrors([
            'email' => 'ログイン情報が登録されていません',
        ]);
    }

    public function register(RegisterRequest $request)
    {
        $staffData = $request->only(['name', 'email', 'password']);
        $staffData['password'] = \bcrypt($staffData['password']);

        $staff = Staff::create($staffData);

        Auth::guard('staff')->login($staff);

        $staff->sendEmailVerificationNotification();

        return redirect()->route('verification.notice')
            ->with('status', '確認メールを送信しました。受信箱を確認してください。');
    }

    public function send(Request $request)
    {
        $request->user()->sendEmailVerificationNotification();
        return back()->with('status', '確認メールを再送しました。');
    }


}
