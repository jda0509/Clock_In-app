<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\StreamResponse;
use Carbon\Carbon;
use App\Models\Attendance;
use App\Models\WorkBreak;


class AttendanceController extends Controller
{
    public function index(Request $request, $month = null)
    {
        $staff = Auth::guard('staff')->user();

        $targetMonth = $request->input('month',now()->format('Y-m'));
        $targetMonth = str_replace('/', '-', $targetMonth);
        $targetDate = Carbon::createFromFormat('Y-m', $targetMonth)->startOfMonth();

        $previousMonth = $targetDate->copy()->subMonth()->format('Y/m');
        $nextMonth = $targetDate->copy()->addMonth()->format('Y/m');

        $attendances = Attendance::with(['work_breaks','applications'])
            ->where('staff_id', $staff->id)
            ->whereYear('work_date', $targetDate->year)
            ->whereMonth('work_date', $targetDate->month)
            ->orderBy('work_date', 'asc')
            ->get();

        $attendances->transform(function($attendance) {
            $attendance->formatted_clock_in = $attendance->clock_in ? Carbon::parse($attendance->clock_in)->format('H:i') : '-';
            $attendance->formatted_clock_out = $attendance->clock_out ? Carbon::parse($attendance->clock_out)->format('H:i') : '-';

            $totalBreakMinutes = 0;
            foreach ($attendance->work_breaks as $break) {
                if ($break->break1_start && $break->break1_end) {
                    $totalBreakMinutes += Carbon::parse($break->break1_end)->diffInMinutes(Carbon::parse($break->break1_start));
                }
                if ($break->break2_start && $break->break2_end) {
                    $totalBreakMinutes += Carbon::parse($break->break2_end)->diffInMinutes(Carbon::parse($break->break2_start));
                }
            }
            $attendance->break_duration = $totalBreakMinutes;

            if ($attendance->clock_in && $attendance->clock_out) {
                $workMinutes = Carbon::parse($attendance->clock_out)->diffInMinutes(Carbon::parse($attendance->clock_in));
                $attendance->work_duration = $workMinutes - $totalBreakMinutes;
            } else {
                $attendance->work_duration = null;
            }

            $attendance->weekday = Carbon::parse($attendance->work_date)->format('D');

            return $attendance;
        });

        return view('attendance.list', compact(
            'attendances',
            'targetMonth',
            'previousMonth',
            'nextMonth'
        ));

    }


    /*public function index()
    {
        $attendances = Attendance::with('staff', 'work_break')->get();

        foreach ($attendances as $attendance) {
            $clockIn = $attendance->clock_in;
            $clockOut = $attendance->clock_out;

            $break = 0;
            if ($attendance->workBreak) {
                $b1 = $this->diffInHours($attendance->workBreak->break1_start, $attendance->workBreak->break1_end);
                $b2 = $this->diffInHours($attendance->workBreak->break2_start, $attendance->workBreak->break2_end);
                $break = $b1 + $b2;
            }
            $breakMinutes = $break * 60;
            $attendance->break_time = sprintf('%02d:%02d', floor($breakMinutes / 60), $breakMinutes % 60);

            $totalMinutes = Carbon::parse($clockIn)->diffInMinutes(Carbon::parse($clockOut)) - ($break * 60);
            $attendance->work_hours = sprintf('%02d:%02d', floor($totalMinutes / 60), $totalMinutes % 60);

            $attendance->note = optional($attendance->applications()->latest()->first())->reason ?? '';
        }

        return view('attendance.index', compact('attendances'));
    }
    */

    /*
    public function dailyReport(Request $request)
    {
        $date = $request->input('date', today()->toDateString());

        $attendance = Attendance::with(['staff', 'breaks'])
            ->whereDate('work_date', $date)
            ->get();

        return view('admin.attendance.daily', compact('attendances', 'date'));
    }

    public function monthlyReport(Request $request)
    {
        $month = $request->input('month', now()->format('Y-m'));

        $attendances = Attendance::with(['breaks'])
            ->where('staff_id', auth()->id())
            ->whereYear('work_date', substr($month, 0, 4))
            ->whereMonth('work_date', substr($month, 5, 2))
            ->get();

        return view('staff.attendance.monthly', compact('attendances', 'month'));
    }
    */

    public function exportCsv()
    {
        $attendances = Attendance::with('workBreak', 'staff')->get();

        $response = new StreamedResponse(function() use ($attendances) {
            $handle = fopen('php://output', 'w');

            fputcsv($handle, ['日付', '出勤時間', '退勤時間', '休憩時間', '合計', '備考']);

            foreach ($attendances as $attendance) {
                $clockIn = $attendance->clock_in;
                $clockOut = $attendance->clock_out;

                $break = 0;
                if ($attendance->workBreak) {
                    $b1 = $this->diffInHours($attendance->workBreak->break1_start, $attendance->workBreak->break1_end);
                    $b2 = $this->diffInHours($attendance->workBreak->break2_start, $attendance->workBreak->break2_end);
                    $break = $b1 + $b2;
                }
                $breakMinutes = $break * 60;
                $breakTime = sprintf('%02d:%02d', floor($breakMinutes / 60), $breakMinutes % 60);

                $totalMinutes = Carbon::parse($clockIn)->diffInMinutes(Carbon::parse($clockOut)) - ($break * 60);
                $workHours = sprintf('%02d:%02d', floor($totalMinutes / 60), $totalMinutes % 60);

                $note = optional($attendance->applications()->latest()->first())->reason ?? '';

                fputcsv($handle, [
                    $attendance->staff->name,
                    $clockIn,
                    $clockOut,
                    $breakTime,
                    $workHours,
                    $note,
                ]);
            }

            fclose($handle);
        });

        $fileName = 'attendance_' . date('Ymd_His') . '.csv';
        $response->headers->set('Content-Type', 'text/csv');
        $response->headers->set('Content-Disposition', 'attachment; filename="'.$fileName.'"');

        return $response;
    }

    private function diffInHours($start, $end)
    {
        if (!$start || !$end) return 0;
        $start = Carbon::parse($start);
        $end = Carbon::parse($end);
        return $start->diffInMinutes($end) / 60;
    }

    public function adminAttendance($date = null)
    {
        $currentDate = $date ? Carbon::parse($date) : Carbon::today();

        $prevDate = $currentDate->copy()->subDay()->format('Y-m-d');
        $nextDate = $currentDate->copy()->addDay()->format('Y-m-d');

        $attendances = Attendance::with('staff', 'work_breaks')
            ->whereDate ('work_date', $currentDate)
            ->orderBy('work_date', 'desc')
            ->get();

        $attendances->transform(function($attendance){
            $attendance->formatted_clock_in = $attendance->clock_in ? Carbon::parse($attendance->clock_in)->format('H:i') : '-';
            $attendance->formatted_clock_out = $attendance->clock_out ? Carbon::parse($attendance->clock_out)->format('H:i') : '-';

            $totalBreakMinutes = 0;
            foreach ($attendance->work_breaks as $break) {
                if ($break->break1_start && $break->break1_end) {
                    $totalBreakMinutes += Carbon::parse($break->break1_end)->diffInMinutes(Carbon::parse($break->break1_start));
                }
                if ($break->break2_start && $break->break2_end) {
                    $totalBreakMinutes += Carbon::parse($break->break2_end)->diffInMinutes(Carbon::parse($break->break2_start));
                }
            }
            $attendance->break_duration = $totalBreakMinutes;

            if ($attendance->clock_in && $attendance->clock_out) {
                $workMinutes = Carbon::parse($attendance->clock_out)->diffInMinutes(Carbon::parse($attendance->clock_in));
                $attendance->work_duration = $workMinutes - $totalBreakMinutes;
            } else {
                $attendance->work_duration = null;
            }

            $attendance->weekday = Carbon::parse($attendance->work_date)->format('D');

            return $attendance;
        });

        return view('admin.attendance.list', compact('attendances', 'currentDate', 'prevDate', 'nextDate'));

    }


}
