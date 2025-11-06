<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\Attendance;
use App\Models\Staff;

class AdminController extends Controller
{
    public function index()
    {
        $attendances = Attendance::with ('staff', 'work_breaks')->orderBy('work_date', 'desc')->get();
        $today = Carbon::today()->format('Y年m月d日');

        $staff = $attendances->first()?->staff;

        return view('admin.attendance.list', compact('today', 'attendances','staff'));
    }

    public function show($id)
    {
        $attendance = Attendance::with('staff', 'work_breaks', 'applications')->findOrFail($id);

        $hasPending = $attendance->applications()->where('status', 'pending')->exists();

        return view('admin.attendance', compact('attendance', 'hasPending'));
    }

    public function adminStaffList()
    {
        $staffs = Staff::orderBy('id', 'asc')->get();

        return view('admin.staff.list', compact('staffs'));
    }
}
