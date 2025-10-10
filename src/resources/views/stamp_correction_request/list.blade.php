@extends('layouts.staff')

@section('css')
<link rel="stylesheet" href="{{ asset('css/stamp_correction/list.css') }}">
@endsection

@section('content')

<div class="stamp_correction_main">
    <h2 class="page_title">勤怠詳細</h2>
    <div class="tab">
        <a href="{{ route('applications.index', ['tab' => 'pending']) }}">
            承認待ち
        </a>
        <a href="{{ route('applications.index', ['tab' => 'approved']) }}">
            承認済み
        </a>
    </div>
    <table class="stamp_correction_table">
        <thead>
            <tr>
                <th class="title">状態</th>
                <th class="title">名前</th>
                <th class="title">対象日時</th>
                <th class="title">申請理由</th>
                <th class="title">申請日時</th>
                <th class="title">詳細</th>
            </tr>
        </thead>
        <tbody>
            @if ($tab === 'pending')
                @foreach($applications as $application)
                    @if($application->status === 'pending')
                        <tr>
                            <td>承認待ち</td>
                            <td>{{ $application->staff->name }}</td>
                            <td>{{ $application->attendance->work_date }}</td>
                            <td>{{ $application->reason }}</td>
                            <td>{{ $application->created_at->format('Y-m-d H:i') }}</td>
                            <td>
                                <a href="{{ route('attendance.show' , $application->attendance_id }}">
                                    詳細
                                </a>
                            </td>
                        </tr>
                    @endif
                @endforeach
            @endif

            @if($tab === 'approved')
                @foreach($applications as $application)
                    @if($application->status === 'approved')
                        <tr>
                            <td>承認済み</td>
                            <td>{{ $application->staff->id }}</td>
                            <td>{{ $application->attendance->work_date }}</td>
                            <td>{{ $application->reason }}</td>
                            <td>{{ $application->created_at->format('Y-m-d H:i') }}</td>
                            <td>
                                <a href="{{ route('attendances.show', $application->attendance_id) }}">
                                    詳細
                                </a>
                            </td>
                        </th>
                    @endif
                @endforeach
            @endif
        </tbody>
    </table>
</div>