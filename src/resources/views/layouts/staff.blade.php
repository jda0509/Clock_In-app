<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>coachtech 勤怠管理</title>
    <link rel="stylesheet" href="{{ asset('css/sanitize.css') }}" />
    <link rel="stylesheet" href="{{ asset('css/staff.css') }}" />
    @yield('css')
</head>
<body>
    <header class="header">
        <div class="header__inner">
            <div class="logo_img">
                <a href="" class="logo__link">
                    <img src="" alt="">
                </a>
            </div>
            <nav class="header__content">
                <ul class="header__nav">
                    <li><a href="" class="clock__in">勤怠</a></li>
                    <li><a href="" class="clock__in__list">勤怠一覧</a></li>
                    <li><a href="" class="application__list">申請</a></li>
                    <li>
                        <form action="/logout" method="post">
                            @csrf
                            <button class="logout">ログアウト</button>
                        </form>
                    </li>
                </ul>
            </nav>
        </div>
    </header>
    <main>
        @yield('content')
    </main>
</body>
</html>