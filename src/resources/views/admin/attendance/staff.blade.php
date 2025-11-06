@extends('layouts.admin')

@section('css')
<link rel="stylesheet" href="{{ asset('css/admin/attendance/staff.css') }}">
@endsection

@section('content')
<div class="detail_main">
    <h2 class="page_title">{{ $staff->name }}さんの勤怠</h2>
    <div class="month-navigation">
        <a href="{{ route('admin.staff.monthly', ['id' => $staff->id, 'month' => $previousMonth]) }}" class="previousMonth">
            <img src="" alt="←">
            前月
        </a>
        <span class="thisMonth">
            <img src="" alt="カレンダー">{{ $targetMonth }}
        </span>
        <a href="{{ route('admin.staff.monthly', ['id' => $staff->id, 'month' => $nextMonth ]) }}" class="nextMonth">
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
                    $date = $attendance->work_date;
                    $weekdayMap = [
                        'Sun' => '(日)', 'Mon' => '(月)', 'Tue' => '(火)',
                        'Wed' => '(水)', 'Thu' => '(木)', 'Fri' => '(金)', 'Sat' => '(土)'
                    ];
                    $weekday = $attendance ? $attendance->weekday : \Carbon\Carbon::parse($date)->format('D');
                    $weekdayJa = $weekdayMap[$weekday] ?? '';
                @endphp
                <tr>
                    <td>{{ \Carbon\Carbon::parse($date)->format('m/d') }}
                        {{ $weekdayJa }}
                    </td>
                    <td>{{ $attendance->formatted_clock_in ?? '' }}</td>
                    <td>{{ $attendance->formatted_clock_out ?? ''}}</td>
                    <td>{{ $attendance->break_duration ?? ''}}</td>
                    <td>{{ $attendance->work_duration ?? ''}}</td>
                    <td>
                        @if($attendance->id)
                            <a href="{{ route('admin.attendance.show', ['id' => $attendance->id]) }}" class="btn">詳細</a>
                        @else
                            <span class="btn">詳細</span>
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
    <form action="{{ route('admin.attendance.export') }}" method="post" >
    @csrf
        <div class="button">
            <a href="{{ route('admin.attendance.export') }}">CSV出力</a>
        </div>
    </form>
</div>

@endsection