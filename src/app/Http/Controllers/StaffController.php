<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\Attendance;
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
        $attendances = Attendance::where('staff_id', $staff->id)
            ->orderBy('work_date', 'desc')
            ->get();

        return view('staff.attendance', compact('attendances', 'todayFormatted', 'staff'));
    }
}
