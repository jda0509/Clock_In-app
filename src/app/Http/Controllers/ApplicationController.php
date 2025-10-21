<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Attendance;
use App\Models\Application;
use App\Models\ApplicationBreak;
use Illuminate\Support\Facades\Auth;

class ApplicationController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::guard('staff')->user();
        $tab = $request->query('tab', 'pending');

        if ($user->role === 'admin') {
            $applications = Application::with(['staff', 'attendance'])
                ->latest()
                ->get();
        } else {
            $applications = Application::with(['staff', 'attendance'])
                ->where('staff_id', $user->id)
                ->orderBy('created_at', 'desc')
                ->get();
        }

        return view('stamp_correction_request.list', compact('applications', 'tab'));
    }

    public function store(Request $request, $id)
    {
        $attendance = Attendance::findOrFail($id);

        $hasPending = $attendance->application()->where('status', 'pending')->exists();
        if ($hasPending) {
            return back()->with('error', '既に承認待ちの申請があります。');
        }

        $application = Application::create([
            'attendance_id' => $attendance->id,
            'staff_id' => $attendance->staff_id,
            'reason' => $request->input('reason'),
            'new_clock_in' => $request->input('new_clock_in'),
            'new_clock_out' => $request->input('new_clock_out'),
            'status' => 'pending',
        ]);

        ApplicationBreak::create([
            'application_id' => $application->id,
            'break_number' => 1,
            'start_time' => $request->input('new_break1_start'),
            'end_time' => $request->input('new_ break2_end'),
        ]);

        ApplicationBreak::create([
            'application_id' => $application->id,
            'break_number' => 2,
            'start_time' => $request->input('new_break2_start'),
            'end_time' => $request->input('new_break2_end'),
        ]);

        return redirect()->route('stamp_correction_request.list')
                        ->with('success', '修正申請を送信しました。');
    }

    public function show($id)
    {
        $attendance = Attendance::with('staff', 'work_breaks', 'applications')->findOrFail($id);

        $hasPending = $attendance->applications()
            ->where('status', 'pending')
            ->exists();

        $work_break = $attendance->work_breaks()->latest()->first();

        return view('attendance.detail', compact('attendance', 'work_break', 'hasPending'));

    }
    
    public function store2(Request $request)
    {
        Application::create([
            'staff_id' => Auth::id(),
            'attendance_id' => $request->attendance_id,
            'reason' => $request->reason,
            'status' => 'pending',
        ]);
    }

    public function show2(Attendance $attendance)
    {
        $clockIn = $attendance->clock_in;
        $clockOut = $attendance->clock_out;

        $totalBreaks = $attendance->work_breaks->sum(function($break) {
            return $break->break_end->diffInMinutes($break->break_start);
        });

        $workTime = $clockOut->diffInMinutes($clockIn) - $totalBreaks;

        return view('attendance.show', compact('attendance', 'totalBreaks', 'workTime'));
    }

    public function approve($id)
    {
        $application = Application::with('break')->findOrFail($id);
        $attendance = Attendance::findOrFail($application->attendance_id);

        $attendance->update([
            'clock_in' => $application->new_clock_in,
            'clock_out' => $application->new_clock_out,
        ]);

        $attendanceBreak = AttendanceBreak::where('attendance_id', $attendance->id)->first();
        $applicationBreak = $application->break;

        if ($attendanceBreak && $applicationBreak) {
            $attendanceBreak->update([
                'break1_start' => $applicationBreak->new_break1_start,
                'break1_end'   => $applicationBreak->new_break1_end,
                'break2_start' => $applicationBreak->new_break2_start,
                'break2_end'   => $applicationBreak->new_break2_end,
            ]);
        }

        $application->update(['status' => 'approved']);

        return redirect()->route('application.index');
    }
}
