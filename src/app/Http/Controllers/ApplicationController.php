<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Models\Attendance;

class ApplicationController extends Controller
{
    public function show($id)
    {
        $attendance = Attendance::with('applications')->findOrFail($id);

        $hasPendingApplication = $attendance->applications()
            ->where('status', 'pending')
            ->exists();

        return view('attendances.show', compact('attendance', 'hasPendingApplication'));

    }
    
    public function store(Request $request)
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
