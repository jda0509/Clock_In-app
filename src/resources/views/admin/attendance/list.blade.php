@extends('layouts.admin')

@section('css')
<link rel="stylesheet" href="{{ asset('css/admin/attendance/list.css') }}">
@endsection

@section('content')
<div class="main_content">
    <h2 class="page_title">{{ $currentDate->format('Y年m月d日') }}の勤怠</h2>
    <div class="month-navigation">
        <a href="{{ route('admin.attendance.list', ['date' => $prevDate]) }}" class="prevMonth">
            <img src="" alt="←">
            前日
        </a>
        <span class="thisMonth">
            <img src="" alt="カレンダー">
            {{ $currentDate->format('m月d日')}}
        </span>
        <a href="{{ route('admin.attendance.list', ['date' => $nextDate]) }}" class="nextMonth">
            翌日<img src="" alt="→">
        </a>
    </div>
    <table class="table">
        <thead>
            <tr>
                <th class="title">名前</th>
                <th class="title">出勤</th>
                <th class="title">退勤</th>
                <th class="title">休憩</th>
                <th class="title">合計</th>
                <th class="title">詳細</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($attendances as $attendance)
                <tr>
                    <td>{{ $attendance->staff->name }}</td>
                    <td>{{ $attendance->clock_in ? \Carbon\Carbon::parse($attendance->clock_in)->format('H:i') : '-' }}</td>
                    <td>{{ $attendance->clock_out ? \Carbon\Carbon::parse($attendance->clock_out)->format('H:i') : '-' }}</td>
                    <td>{{ gmdate('H:i' , $attendance->break_duration * 60) }}</td>
                    <td>
                        @if ($attendance->work_duration)
                            {{ gmdate('H:i', $attendance->work_duration * 60) }}
                        @else
                            -
                        @endif
                    </td>
                    <td>
                        <a href="{{ route('admin.attendance.show', ['id' => $attendance->id]) }}" class="btn">詳細</a>
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