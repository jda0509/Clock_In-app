<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Attendance;
use App\Models\Application;
use App\Models\ApplicationBreak;
use App\Http\Requests\ApplicationRequest;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class ApplicationController extends Controller
{
    public function index(Request $request)
    {
        $tab = $request->query('tab', 'pending');
        $status = $tab === 'approved' ? 'approved' : 'pending' ;

        $applications = Application::with(['staff', 'attendance'])
            ->where('status','pending')
            ->get();

        if (Auth::guard('admin')->check()) {
            $applications = Application::with(['staff', 'attendance'])
                ->where('status', $status)
                ->orderByDesc('created_at')
                ->get();
        } elseif (Auth::guard('staff')->check()) {
            $staff = Auth::guard('staff')->user();
            $applications = Application::with(['staff', 'attendance'])
                ->where('staff_id', $staff->id)
                ->where('status', $status)
                ->orderByDesc('created_at')
                ->get();
        }

        return view('stamp_correction_request.list', compact('applications', 'tab'));
    }

    public function store(ApplicationRequest $request, $attendanceId)
    {
        $attendance = Attendance::find($attendanceId);

        if (!$attendance) {
            $attendance = Attendance::create([
                'staff_id' => Auth::guard('staff')->id(),
                'work_date' => $attendanceId,
                'status' => 'pending',
            ]);
        }

        $application = Application::create([
            'attendance_id' => $attendance?->id,
            'staff_id' => Auth::guard('staff')->id(),
            'reason' => $request->input('reason'),
            'new_clock_in' => $request->input('new_clock_in'),
            'new_clock_out' => $request->input('new_clock_out'),
            'status' => 'pending',
        ]);

        if ($request->filled('new_break1_start') || $request->filled('new_break1_end')) {
            ApplicationBreak::create([
                'application_id' => $application->id,
                'start_time' => $request->input('new_break1_start'),
                'end_time' => $request->input('new_break1_end'),
            ]);
        }

        if ($request->filled('new_break2_start') || $request->filled('new_break2_end')) {
            ApplicationBreak::create([
                'application_id' => $application->id,
                'start_time' => $request->input('new_break2_start'),
                'end_time' => $request->input('new_break2_end'),
            ]);
        }

        return redirect()->route('attendances.show', ['id' => $attendance?->id ?? 'new'])
                        ->with('success', '修正申請を送信しました。');
    }

    public function show($id)
    {
        $attendance = Attendance::with('staff', 'work_breaks', 'applications')->find($id);

        if (!$attendance) {
            $cleanId = str_replace('new-', '', $id);
                $dateStr = str_replace('new-', '', $id);
                $attendanceDate = Carbon::createFromFormat('Ymd', $dateStr);
                $attendance = new Attendance([
                    'id' => $id,
                    'clock_in' => null,
                    'clock_out'=> null,
                    'work_date' => $attendanceDate,
                ]);
                $attendance->setRelation('staff', Auth::guard('staff')->user());
                $attendance->exists = false;
            } else {
                $attendanceDate = $attendance->work_date;
                $attendance->exists = true;
            }

        if ($attendance->exists) {
            $latestApplication = $attendance->applications()->latest()->first();
            $hasPending = $latestApplication && $latestApplication->status === 'pending';
        } else {
            $hasPending = false;
        }

        $work_break = $attendance->exists
            ? $attendance->work_breaks()->latest()->first()
            : new \App\Models\WorkBreak([
                'break1_start' => null,
                'break1_end' => null,
                'break2_start' => null,
                'break2_end' => null,
            ]);

        return view('attendance.detail', compact('attendance', 'work_break', 'hasPending', 'attendanceDate'));

    }
    
    /*public function store2(Request $request)
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
    }*/


    public function adminUpdate(ApplicationRequest $request, $id)
    {
        $date = $request->validated();

        $attendance = Attendance::findOrFail($id);

        $attendance->update([
            'clock_in' => $request->input('new_clock_in'),
            'clock_out' => $request->input('new_clock_out'),
        ]);

        $attendance->work_breaks()->updateOrCreate(
            ['attendance_id' => $attendance->id],
            [
                'break1_start' => $request->input('new_break1_start'),
                'break1_end' => $request->input('new_break1_end'),
                'break2_start' => $request->input('new_break2_start'),
                'break2_end' => $request->input('new_break2_end'),
            ]
            );

        return redirect()
            ->route('admin.attendance.show', ['id' => $attendance->id]);
    }

    public function approveSubmit (Request $request,$id)
    {
        $application = Application::findOrFail($id);
        $application->status = 'approved';
        $application->save();

        $attendance = $application->attendance;
        if ($attendance) {
            $attendance->clock_in = $application->new_clock_in ?? $attendance->clock_in;
            $attendance->clock_out = $application->new_clock_out ?? $attendance->clock_out;
            $attendance->save();
        }

        $application->status = 'approved';
        $application->save();

        return redirect()->route('admin.application.list', ['tab' => 'pending']);
    }
}