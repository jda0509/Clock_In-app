@extends('layouts.staff')

@section('css')
<link rel="stylesheet" href="{{ asset('css/attendance/list.css') }}">
@endsection

@section('content')

@section('content')
@php
    $weekdayMap = ['Sun' => '(日)', 'Mon' => '(月)', 'Tue' => '(火)',
                    'Wed' => '(水)', 'Thu' => '(木)', 'Fri' => '(金)', 'Sat' => '(土)'];
@endphp
<div class="main_content">
    <div class="page_title">
        <img src="{{ asset('storage/Line 2.png')}}" alt="">
        <h2 class="title">勤怠一覧</h2>
    </div>
    <div class="month-navigation">
        <a href="{{ route('attendance.list', ['month' => $previousMonth]) }}" class="previousMonth">
            <img src="{{ asset('storage/left.png')}}" alt="←">
            前月
        </a>
        <span class="thisMonth">
            <img src="{{ asset('storage/calendar.png') }}" alt="カレンダー">{{ $targetMonth }}
        </span>
        <a href="{{ route('attendance.list', ['month' => $nextMonth ]) }}" class="nextMonth">
            翌月<img src="{{ asset('storage/right.png') }}" alt="→">
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
            @foreach ($days as $day)
                @php
                    $date = $day['date'];
                    $attendance = $day['attendance'];
                    $weekdayJa = $weekdayMap[$date->format('D')] ?? '';
                    $in = $attendance?->formatted_clock_in ?? '';
                    $out = $attendance?->formatted_clock_out ?? '';
                    $break = is_numeric($attendance?->break_duration) && $attendance->break_duration > 0
                        ? gmdate('H:i', $attendance->break_duration * 60)
                        : '';
                    $work = is_numeric($attendance?->work_duration) && $attendance->work_duration > 0
                        ? gmdate('H:i', $attendance->work_duration * 60)
                        : '';
                        $detailId = $attendance?->id ? $attendance->id : 'new-' . $date->format('Ymd');
                @endphp
                <tr>
                    <td>{{ $date->format('m/d') }}
                        {{ $weekdayJa }}
                    </td>
                    <td>{{ $in }}</td>
                    <td>{{ $out }}</td>
                    <td>{{ $break }}</td>
                    <td>{{ $work }}</td>
                    <td>
                        <a href="{{ route('attendances.show', ['id' => $detailId]) }}" class="btn">詳細</a>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>

@endsection