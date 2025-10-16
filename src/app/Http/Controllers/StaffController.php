<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\Attendance;
use App\Models\WorkBreak;
use Illuminate\Support\Facades\Auth;

class StaffController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:staff');
    }
    public function index()
    {
        $staff = Auth::guard('staff')->user();
        $today = Carbon::today();

        $todayFormatted = $today->format('Y年m月d日');

        $attendance = Attendance::with('work_breaks')
            ->where('staff_id', $staff->id)
            ->whereDate('work_date', $today)
            ->first();

        $lastBreak = $attendance?->work_breaks?->last();

        if (!$attendance || !$attendance->clock_in) {
            $status = 'before_work';
        } elseif ($attendance->clock_out) {
            $status = 'after_work';
        } elseif ($lastBreak && !$lastBreak->break1_end) {
            $status = 'on_break';
        } else {
            $status = 'working';
        }

        $attendances = Attendance::where('staff_id', $staff->id)
            ->orderBy('work_date', 'desc')
            ->get();


        return view('staff.attendance', compact('attendances','attendance','todayFormatted', 'staff', 'status'));
    }

    public function startWork()
    {
        $staff = Auth::guard('staff')->user();

        $attendance = Attendance::firstOrCreate(
            [
                'staff_id' => $staff->id,
                'work_date' => Carbon::today(),
            ],
            [
                'clock_in' => Carbon::now(),
            ]
            );

        return redirect()->route('staff.attendance');
    }

    public function startBreak()
    {
        $staff = Auth::guard('staff')->user();

        $attendance = Attendance::where('staff_id', $staff->id)
            ->whereDate('work_date', Carbon::today())
            ->firstOrFail();

        $lastBreak = $attendance->work_breaks()->latest()->first();

        if (!$lastBreak || $lastBreak->break1_end) {
            $attendance->work_breaks()->create([
                'break1_start' => now(),
            ]);
        } elseif (!$lastBreak->break2_start) {
            $lastBreak->update([
                'break2_start' => now(),
            ]);
        }

        return redirect()->route('staff.attendance');
    }

    public function endBreak()
    {
        $staff = Auth::guard('staff')->user();

        $attendance = Attendance::where('staff_id', $staff->id)
            ->whereDate('work_date', Carbon::today())
            ->firstOrFail();

        $lastBreak = $attendance->work_breaks()->latest()->first();

        if ($lastBreak && !$lastBreak->break1_end) {
            $lastBreak->update([
                'break1_end' => now(),
            ]);
        } elseif ($lastBreak && !$lastBreak->break2_end) {
            $lastBreak->update([
                'break2_end' => now(),
            ]);
        }

        return redirect()->route('staff.attendance');
    }

    public function endWork()
    {
        $staff = Auth::guard('staff')->user();

        $attendance = Attendance::where('staff_id', $staff->id)
            ->whereDate('work_date', Carbon::today())
            ->firstOrFail();

        $clockOut = now();
        $clockIn = Carbon::parse($attendance->clock_in);

        $breaks = WorkBreak::where('attendance_id', $attendance->id)->get();

        $totalBreakMinutes = 0;
        foreach ($breaks as $break){
            if ($break->break_in && $break->break_out) {
                $in = Carbon::parse($break->break_in);
                $out = Carbon::parse($break->break_out);
                $totalBreakMinutes += $in->diffInMinutes($out);
            }
        }
        $totalMinutes = $clockIn->diffInMinutes($clockOut) - $totalBreakMinutes;

        $hours = floor($totalMinutes / 60);
        $minutes = $totalMinutes % 60;
        $formattedTotal = sprintf('%02d:%02d', $hours, $minutes);

        $attendance->update([
            'clock_out' => $clockOut,
            'total_work_minutes' => $formattedTotal,
        ]);

        return redirect()->route('staff.attendance');
    }
}
