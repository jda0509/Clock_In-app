@extends('layouts.logo')

@section('css')
<link rel="stylesheet" href="{{ asset('css/register.css') }}">
@endsection

@section('content')

<div class="register-form__content">
    <div class="register-form__heading">
        <h2>会員登録</h2>
    </div>
    <div class="register-form__main">
        <form action="" method="post">
            @csrf
            <div class="register-form__name">
                <div class="name__label">名前</div>
                <input class="register__name" type="text" name="name" value="{{ old('name') }}" />
                <div class="error">
                    @error('name')
                    {{ $message }}
                    @enderror
                </div>
            </div>
            <div class="login-form__email">
                <div class="email__label">メールアドレス</div>
                <input class="register__email" type="email" name="email" value="{{ old('email') }}" />
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
            <div class="login-form__password-confirmation">
                <div class="password__label">確認用パスワード</div>
                <input class="password-confirmation" type="password" name="password-confirmation" value="" />
                <div class="error">
                    @error('password-confirmation')
                    {{ $message }}
                    @enderror
                </div>
            </div>
            <div class="register__button">
                <button class="button__main" type="submit">登録する</button>
            </div>
        </form>
    </div>
    <div class="login__link">
        <a href="/login" class="login">ログインはこちら</a>
    </div>
</div>

@endsection