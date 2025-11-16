@extends('layouts.admin')

@section('css')
<link rel="stylesheet" href="{{ asset('css/attendance/list.css') }}">
@endsection

@section('content')
<div class="detail_main">
    <h2 class="page_title">勤怠詳細</h2>
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
            <span>{{ $application->new_clock_in ? \Carbon\Carbon::parse($application->new_clock_in)->format('H:i') : '-' }}</span> 〜
            <span>{{ $application->new_clock_out ? \Carbon\Carbon::parse($application->new_clock_out)->format('H:i') : '-' }}</span>
        </div>
        <div class="detail_break">
            <label for="" class="break_label_1">休憩</label>
            <span>{{ $application->new_break1_start ? \Carbon\Carbon::parse($application->new_break1_start)->format('H:i') : '-' }}</span> 〜
            <span>{{ $application->new_break1_end ? \Carbon\Carbon::parse($application->new_break1_end)->format('H:i') : '-' }}</span>
        </div>
        <div class="detail_break2">
            <label for="" class="break_label_2">休憩２</label>
            <span>{{ $application->new_break2_start ? \Carbon\Carbon::parse($application->new_break2_start)->format('H:i') : '-' }}</span> 〜
            <span>{{ $application->new_break2_end ? \Carbon\Carbon::parse($application->new_break2_end)->format('H:i') : '-' }}</span>
        </div>
        <div class="detail_reason">
            <label for="" class="note_label">備考</label>
            <p>{{ $application->reason ?? '-' }}</p>
        </div>

    <form action="{{ route('admin.approve.submit', $application->id) }}" method="post">
        @csrf
        <input type="hidden" name="status" value="approved">
        <div class="detail_button">
            <button class="button_main" type="submit">承認</button>
        </div>
    </form>
</div>

@endsection