<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Attendance;
use App\Models\WorkBreak;
use App\Models\Staff;
use Carbon\Carbon;

class ExistingStaffAttendanceSeeder extends Seeder
{
    public function run()
    {
        $staffs = Staff::all();

        foreach ($staffs as $staff) {
            for ($i = 0; $i < 90; $i++) {
                $date = Carbon::today()->subDays($i);

                $clockIn = $date->copy()->setTime(9,0);
                $clockOut = $date->copy()->setTime(17,0);

                $attendance = Attendance::create([
                    'staff_id' => $staff->id,
                    'work_date' => $date,
                    'clock_in' => $clockIn,
                    'clock_out' => $clockOut,
                    'total_work_minutes' => sprintf(
                        '%02d:%02d',
                        floor($totalMinutes / 60),
                        $totalMinutes % 60
                    ),
                ]);

                $totalMinutes = Carbon::parse($clockIn)->diffInMinutes(Carbon::parse($clockOut)) - $totalBreakMinutes;
            }
        }
    }
}
