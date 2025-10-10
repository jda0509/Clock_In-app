@extends('layouts.staff')

@section('css')
<link rel="stylesheet" href="{{ asset('css/attendance/detail.css') }}">
@endsection

@section('content')
@php
    $disabled = $hasPending ? 'disabled' : '';
@endphp

<div class="detail_main">
    <h2 class="page_title">勤怠詳細</h2>
    <form action="" method="post">
        @csrf
        <input type="hidden" name="attendance_id" value="{{ $attendance->id }}">
        <div class="detail_name">
            <label for="" class="name_label">名前</label>
            <div class="staff_name">{{ $attendance->staff->name }}</div>
        </div>
        <div class="detail_date">
            <label for="" class="date_label">日付</label>
            <div class="staff_date">{{ $attendance->work_date }}</div>
        </div>
        <div class="detail_clock">
            <label for="" class="clock_label">出勤・退勤</label>
            <input type="time" name="new_clock_in" value="{{ old('new_clock_in', $attendance->clock_in) }}" {{ $disabled }}>
            <span>〜</span>
            <input type="time" name="new_clock_out" value="{{ old('new_clock_out', $attendance->clock_out) }}" {{ $disabled }}>
        </div>
        <div class="detail_break">
            <label for="" class="break_label_1">休憩</label>
            <input type="time" name="new_break1_start" value="{{ old('new_break_start', $work_break->break1_start) }}" {{ $disabled }}>
            <span>〜</span>
            <input type="time" name="new_break1_end" value="{{ old('new_break_end', $work_break->break1_end }}" {{ $disabled }}>
        </div>
        <div class="detail_break2">
            <label for="" class="break_label_2">休憩２</label>
            <input type="time" name="new_break2_start" value="{{ old('new_break2_start', $work_break->break2_start }}" {{ $disabled }}>
            <input type="time" name="new_break2_end" value="{{ old('new_break2_end', $work_break->break2_end) }}" {{ $disabled }}>
        </div>
        <div class="detail_reason">
            <label for="" class="note_label">備考</label>
            <textarea class="reason_main" {{ $disabled }}>{{ optional($attendance->applications()->latest()->first())->reason }}</textarea>
        </div>

        @if(!hasPending)
            <div class="detail_button">
                <button class="button_main" type="submit">修正</button>
            </div>
        @else
            <p class="detail_message">承認待ちのため修正はできません。</p>
        @endif
    </form>
</div>

@endsection