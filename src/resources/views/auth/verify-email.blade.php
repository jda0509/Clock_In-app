<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>coachtech 勤怠管理</title>
    <link rel="stylesheet" href="{{ asset('css/sanitize.css') }}" />
</head>
<body>
    <h1>メールアドレスの確認をお願いします</h1>
    <p>登録されたメールアドレス宛に確認リンクを送信しました。</p>
    <p>メールを確認し、リンクをクリックして認証を完了してください。</p>

    <form method="post" action="{{ route('verification.send') }}">
        @csrf
        <button type="submit">確認メールを再送信</button>
    </form>
</body>
</html>