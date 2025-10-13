@extends('layouts.staff')

@section('css')
<link rel="stylesheet" href="{{ asset('css/attendance.css') }}">
@endsection

@section('content')

<div class="attendance__main">
    @if($status === 'before_work')
        <p class="status">勤務外</p>
        <div class="today_date">
            <h2 id="today"></h2>
            <p id="clock"></p>
        </div>
        <form method="post" action="{{ route('attendance.start') }}" >
            @csrf
            <button class="clock_in" type="submit">出勤</button>
        </form>

    @elseif ($status === 'working')
        <p class="status">出勤中</p>
        <div class="today_date">
            <h2 id="today"></h2>
            <p id="clock"></p>
        </div>
        <form action="{{ route('attendance.end') }}" method="post">
            @csrf
            <button class="clock_out" type="submit">退勤</button>
        </form>
        <form action="{{ route('attendance.break') }}" method="post">
            @csrf
            <button class="break">休憩入</button>
        </form>

    @elseif ($status === 'on_break')
        <p class="status">休憩中</p>
        <div class="today_date">
            <h2 id="today"></h2>
            <p id="clock"></p>
        </div>
        <form action="{{ route('attendance.resume') }}" method="post">
            @csrf
            <button class="break">休憩戻</button>
        </form>

    @elseif ($status === 'after_work')
        <p class="status">退勤済</p>
        <div class="today_date">
            <h2 id="today"></h2>
            <p id="clock"></p>
        </div>
        <p class="message">お疲れ様でした。</p>

    @endif

    <script>
        function updateClock(){
            const now = new Date();
            const date = now.getFullYear() + '年' +
                        String(now.getMonth() +1).padStart(2,'0') + '月' +
                        String(now.getDate()).padStart(2,'0') + '日';

            const time = String(now.getHours()).padStart(2,'0') + ":" +
                        String(now.getMinutes()).padStart(2,'0') + ":" +
                        String(now.getSeconds()).padStart(2,'0');

            document.getElementById('today').innerText = date;
            document.getElementById('clock').innerText = time;
        }
        setInterval(updateClock, 1000);
        updateClock();
    </section>
</div>

