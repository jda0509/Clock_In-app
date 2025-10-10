@extends('layouts.logo')

@section('css')
<link rel="stylesheet" href="{{ asset('css/login.css') }}">
@endsection

@section('content')

<div class="login-form__content">
    <div class="login-form__heading">
        <h2>ログイン</h2>
    </div>
    <div class="login-form__main">
        <form action="{{ route('staff.login') }}" method="post">
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
                <button class="button__main" type="submit">ログイン</button>
            </div>
        </form>
    </div>
    <div class="register__link">
        <a href="/register" class="register">会員登録はこちら</a>
    </div>
</div>

@endsection