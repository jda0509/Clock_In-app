<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Http\Models\Staff;
use Illuminate\Support\Facades\Auth;

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

    public function store(RegisterRequest $request)
    {
        $staffData = $request->only(['name','email','password']);
        $staffData['password'] = bcrypt($staffData['password']);

        $staff = Staff::create($staffData);

        $staff->sendEmailVerificationNotification();

        return redirect()->route('staff.verification.notice');
    }

    public function login(LoginRequest $request)
    {
        $credentials = $request->only('email', 'password');

        if (Auth::guard('admin')->attempt($credentials)) {
            return redirect()->route('admin.attendance');
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
            return redirect()->route('staff.attendance');
        }

        return back()->withErrors([
            'email' => 'ログイン情報が登録されていません',
        ]);
    }

    public function register(RegisterRequest $request)
    {
        $staffData = $request->only(['name', 'email', 'password']);
        $staffData['password'] = bcrypt($staffData['password']);

        $staff = Staff::create([
            'name' => ['name'],
            'email' => ['email'],
            'password' => bcrypt['password'],
        ]);

        $staff->sendEmailVerificationNotification();

        return redirect()->route('verification.notice')
            ->with('status', '確認メールを送信しました。受信箱を確認してください。');
    }


}
