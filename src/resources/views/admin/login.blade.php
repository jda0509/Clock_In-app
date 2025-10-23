@extends('layouts.logo')

@section('css')
<link rel="stylesheet" href="{{ asset('css/admin/login.css') }}">
@endsection

@section('content')

<div class="login-form__content">
    <div class="login-form__heading">
        <h2>ログイン</h2>
    </div>
    <div class="login-form__main">
        <form action="{{ route('admin.login') }}" method="post" novalidate>
            @csrf
            <div class="login-form__email">
                <div class="email__label">メールアドレス</div>
                <input class="email" type="email" name="email" value="{{ old('email') }}" />
                <div class="error">
                    @error('email')
                    {{ $message }}
                    @enderror
                </div>
            </div>
            <div class="login-form__password">
                <div class="password__label">パスワード</div>
                <input class="password" type="password" name="password" value="" />
                <div class="error">
                    @error('password')
                    {{ $message }}
                    @enderror
                </div>
            </div>
            <div class="login__button">
                <button class="button__main" type="submit">管理者ログインする</button>
            </div>
        </form>
    </div>
</div>

@endsection