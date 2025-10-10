@extends('layouts.staff')

@section('css')
<link rel="stylesheet" href="{{ asset('css/admin/attendance/list.css') }}">
@endsection

@section('content')
<div class="main_content">
    <h2 class="page_title">{{ $attendance->staff->name }}の勤怠</h2>
    <div class="month-navigation">
        <a href="" class="prevMonth">
            <img src="" alt="←">
            前月
        </a>
        <span class="thisMonth">
            <img src="" alt="カレンダー">{{}}
        </span>
        <a href="" class="nextMonth">
            翌月<img src="" alt="→">
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
                    <td>{{ $attendance->user->name }}</td>
                    <td>{{ $attendance->clock_in ? $attendance->clock_in->format('H:i') : '-' }}</td>
                    <td>{{ $attendance->clock_out ? $attendance->clock_out->format('H:i') : '-' }}</td>
                    <td>{{ $attendance->break_duration ? gmdate('H:i' , $attendance->break_duration) : '-' }}</td>
                    <td>
                        @if ($attendance->clock_in && $attendance->clock_out)
                            {{ gmdate('H:i', $attendance->work_duration) }}
                        @else
                            -
                        @endif
                    </td>
                    <td>
                        <a href="" class="btn">詳細</a>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
    <form action="{{ route('admin.attendance.export') }}" method="post" >
        @csrf
        <div class="button">
            <a href="{{ route('admin.attendance.export}}">CSV出力</a>
        </div>
    </form>
</div>