<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>coachtech 勤怠管理</title>
    <link rel="stylesheet" href="{{ asset('css/sanitize.css') }}" />
    <link rel="stylesheet" href="{{ asset('css/logo.css') }}" />
    @yield('css')
</head>
<body>
    <header class="header">
        <div class="logo_img">
            <a href="" class="logo__link">
                <img src="" alt="">
            </a>
        </div>
    </header>

    <main>
        @yield('content')
    </main>
</body>
</html>