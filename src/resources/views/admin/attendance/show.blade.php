@extends('layouts.admin')

@section('css')
<link rel="stylesheet" href="{{ asset('css/admin/attendance/css') }}">
@endsection

@section('content')

<div class="detail_main">
    <h2 class="page_title">勤怠詳細</h2>
    <form action="{{ route('admin.attendance.update', ['id' => $attendance->id]) }}" method="post">
        @csrf
        <input type="hidden" name="attendance_id" value="{{ $attendance->id }}">
        <div class="detail_name">
            <label for="" class="name_label">名前</label>
            <div class="staff_name">{{ $attendance->staff->name }}</div>
        </div>
        <div class="detail_date">
            <label for="" class="date_label">日付</label>
            <div class="date_year">{{ \Carbon\Carbon::parse($attendance->work_date)->format('Y') }}年</div>
            <div class="date_month-day">{{ \Carbon\Carbon::parse($attendance->work_date)->format('m月d') }}日</div>
        </div>
        <div class="detail_clock">
            <label for="" class="clock_label">出勤・退勤</label>
            <input type="time" name="new_clock_in" value="{{ old('new_clock_in', $attendance->clock_in) }}" >
            <span>〜</span>
            <input type="time" name="new_clock_out" value="{{ old('new_clock_out', $attendance->clock_out) }}" >
            @error('new_clock_in')
                {{ $message }}
            @enderror
            @error('new_clock_out')
                {{ $message }}
            @enderror
        </div>
        <div class="detail_break">
            <label for="" class="break_label_1">休憩</label>
            <input type="time" name="new_break1_start" value="{{ old('new_break1_start', optional($work_break)->break1_start ? \Carbon\Carbon::parse($work_break->break1_start)->format('H:i') : '' ) }}" >
            <span>〜</span>
            <input type="time" name="new_break1_end" value="{{ old('new_break1_end', optional($work_break)->break1_end ? \Carbon\Carbon::parse($work_break->break1_end)->format('H:i') : '' ) }}" >
            @error('new_break1_start')
                {{ $message }}
            @enderror
            @error('new_break1_end')
                {{ $message }}
            @enderror
        </div>
        <div class="detail_break2">
            <label for="" class="break_label_2">休憩２</label>
            <input type="time" name="new_break2_start"
                value="{{ old('new_break2_start', optional($work_break)->break2_start ? \Carbon\Carbon::parse($work_break->break2_start)->format('H:i') : '') }}">
            <span>〜</span>
            <input type="time" name="new_break2_end"
                value="{{ old('new_break2_end', optional($work_break)->break2_end ? \Carbon\Carbon::parse($work_break->break2_end)->format('H:i') : '') }}">
            <div class="error">
                @error('new_break2_start')
                {{ $message }}
                @enderror
            </div>
            <div class="error">
                @error('new_break2_end')
                    {{ $message }}
                @enderror
            </div>
        </div>
        <div class="detail_reason">
            <label for="" class="note_label">備考</label>
            <textarea class="reason_main" name="reason">{{ old('reason') }}</textarea>
        </div>
        <div class="error">
            @error('reason')
                {{ $message }}
            @enderror
        </div>

        <div class="detail_button">
            <button class="button_main" type="submit">修正</button>
        </div>
    </form>
</div>

@endsection