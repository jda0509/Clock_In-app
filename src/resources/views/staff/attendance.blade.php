@extends('layouts.staff')

@section('css')
<link rel="stylesheet" href="{{ asset('css/attendance.css') }}">
@endsection

@section('content')

<div class="attendance__main">
    @if($status === 'before_work')
        <p class="status">勤務外</p>
        <div class="today_date">
            <p id="today"></p>
            <h2 id="clock"></h2>
        </div>
        <form method="post" action="{{ route('attendance.start') }}" >
            @csrf
            <button class="clock_in" type="submit">出勤</button>
        </form>

    @elseif ($status === 'working')
        <p class="status">出勤中</p>
        <div class="today_date">
            <p id="today"></p>
            <h2 id="clock"></h2>
        </div>
        <form action="{{ route('attendance.end') }}" method="post">
            @csrf
            <button class="clock_out" type="submit">退勤</button>
        </form>
        <form action="{{ route('attendance.break.start') }}" method="post">
            @csrf
            <button class="break" type="submit">休憩入</button>
        </form>

    @elseif ($status === 'on_break')
        <p class="status">休憩中</p>
        <div class="today_date">
            <p id="today"></p>
            <h2 id="clock"></h2>
        </div>
        <form action="{{ route('attendance.break.end') }}" method="post">
            @csrf
            <button class="break" type="submit">休憩戻</button>
        </form>

    @elseif ($status === 'after_work')
        <p class="status">退勤済</p>
        <div class="today_date">
            <p id="today"></p>
            <h2 id="clock"></h2>
        </div>
        <p class="message">お疲れ様でした。</p>

    @endif
</div>

<script>
    function updateTime(){
        const now = new Date();

        const days = ['日','月','火','水','木','金','土'];
        const dayOfWeek = days[now.getDay()];

        const dateText = now.getFullYear() + '年' +
                    String(now.getMonth() +1).padStart(2,'0') + '月' +
                    String(now.getDate()).padStart(2,'0') + '日' +
                    '(' + dayOfWeek + ')';

        const timeText = String(now.getHours()).padStart(2,'0') + ":" +
                    String(now.getMinutes()).padStart(2,'0');

        const elDate = document.getElementById('today');
        const elTime = document.getElementById('clock');

        if (elDate) elDate.textContent = dateText;
        if (elTime) elTime.textContent = timeText;

    }
    setInterval(updateTime, 1000);
    updateTime();
</script>


@endsection
