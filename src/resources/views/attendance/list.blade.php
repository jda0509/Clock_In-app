@extends('layouts.staff')

@section('css')
<link rel="stylesheet" href="{{ asset('css/attendance/detail.css') }}">
@endsection

@section('content')

@section('content')
<div class="main_content">
    <h2 class="page_title">勤怠一覧</h2>
    <div class="month-navigation">
        <a href="{{ route('attendance.list', ['month' => $previousMonth]) }}" class="previousMonth">
            <img src="" alt="←">
            前月
        </a>
        <span class="thisMonth">
            <img src="" alt="カレンダー">{{ $targetMonth }}
        </span>
        <a href="{{ route('attendance.list', ['month' => $nextMonth ]) }}" class="nextMonth">
            翌月<img src="" alt="→">
        </a>
    </div>
    <table class="table">
        <thead>
            <tr>
                <th class="title">日付</th>
                <th class="title">出勤</th>
                <th class="title">退勤</th>
                <th class="title">休憩</th>
                <th class="title">合計</th>
                <th class="title">詳細</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($attendances as $attendance)
                @php
                    $weekdayMap = [
                        'Sun' => '日', 'Mon' => '月', 'Tue' => '火',
                        'Wed' => '水', 'Thu' => '木', 'Fri' => '金', 'Sat' => '土'
                    ];
                    $weekdayJa = $weekdayMap[$attendance->weekday] ?? '';
                @endphp
                <tr>
                    <td>{{ \Carbon\Carbon::parse($attendance->work_date)->format('m/d') }}
                        {{ $weekdayJa }}
                    </td>
                    <td>{{ $attendance->formatted_clock_in }}</td>
                    <td>{{ $attendance->formatted_clock_out }}</td>
                    <td>
                        @if ($attendance->break_duration)
                            {{ gmdate('H:i' , $attendance->break_duration * 60) }}
                        @else
                            -
                        @endif
                    </td>
                    <td>
                        @if ($attendance->work_duration)
                            {{ gmdate('H:i', $attendance->work_duration * 60) }}
                        @else
                            -
                        @endif
                    </td>
                    <td>
                        <a href="{{ route('attendances.show', ['id' => $attendance->id]) }}" class="btn">詳細</a>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>

@endsection