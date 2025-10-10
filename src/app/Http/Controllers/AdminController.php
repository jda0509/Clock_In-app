<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;

class AdminController extends Controller
{
    public function index()
    {
        $today = Carbon::today()->format('Y年m月d日');
        $attendance = Attendance::with('staff')
            ->whereDate('work_date', Carbon::today())
            ->get();

        return view('admin.attendance.index', compact('today', 'attendances'));
    }
}
