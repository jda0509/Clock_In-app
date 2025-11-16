@php
    $layout = Auth::guard('admin')->check() ? 'layouts.admin' : 'layouts.staff';
@endphp

@extends($layout)

@section('content')

<div class="stamp_correction_main">
    <h2 class="page_title">勤怠詳細</h2>
    <div class="tab">
        <a href="{{ Auth::guard('admin')->check()
            ? route('admin.application.list', ['tab' => 'pending'])
            : route('applications.index', ['tab' => 'pending']) }}">
            承認待ち
        </a>
        <a href="{{ Auth::guard('admin')->check()
            ? route('admin.application.list', ['tab' => 'approved'])
            : route('applications.index', ['tab' => 'approved']) }}">
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
                                @php
                                    $user = Auth::guard('staff')->user();
                                @endphp

                                @if (Auth::guard('admin')->check())
                                    <a href="{{ route('admin.approve', ['id' => $application->attendance_id]) }}">
                                        詳細
                                    </a>
                                @elseif (Auth::guard('staff')->check())
                                    <a href="{{ route('attendances.show', ['id' => $application->attendance_id]) }}">
                                        詳細
                                    </a>
                                @endif
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
                            <td>{{ $application->staff->name }}</td>
                            <td>{{ $application->attendance->work_date }}</td>
                            <td>{{ $application->reason }}</td>
                            <td>{{ $application->created_at->format('Y-m-d H:i') }}</td>
                            <td>
                                @if (Auth::guard('admin')->check())
                                    <a href="{{ route('admin.attendance.show', ['id' => $application->attendance_id]) }}">
                                        詳細
                                    </a>
                                @elseif (Auth::guard('staff')->check())
                                    <a href="{{ route('attendances.show', ['id' => $application->attendance_id]) }}">
                                        詳細
                                    </a>
                                @endif
                            </td>
                        </tr>
                    @endif
                @endforeach
            @endif
        </tbody>
    </table>
</div>

@endsection