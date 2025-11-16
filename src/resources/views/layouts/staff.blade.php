<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>coachtech 勤怠管理</title>
    <link rel="stylesheet" href="{{ asset('css/sanitize.css') }}" />
    <link rel="stylesheet" href="{{ asset('css/layouts/staff.css') }}" />
    @yield('css')
</head>
<body>
    <header class="header">
        <div class="header__inner">
            <div class="logo_img">
                <a href="/attendance" class="logo__link">
                    <img src="{{ asset('storage/CoachTech_White 1.png') }}" alt="ロゴ">
                </a>
            </div>
            <nav class="header__content">
                <ul class="header__nav">
                    <li><a href="{{ route('staff.attendance') }}" class="clock__in">勤怠</a></li>
                    <li><a href="{{ route('attendance.list') }}" class="clock__in__list">勤怠一覧</a></li>
                    <li><a href="{{ route('application.list') }}" class="application__list">申請</a></li>
                    <li>
                        <form action="{{ route('staff.logout') }}" method="post">
                            @csrf
                            <button class="logout" type="submit">ログアウト</button>
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