<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Staff;
use App\Models\Attendance;
use App\Models\WorkBreak;
use App\Models\Application;
use Carbon\Carbon;

class StaffAttendanceSeeder extends Seeder
{
    public function run()
    {
        $staffs = Staff::factory()->count(5)->create();

        foreach ($staffs as $staff) {
            $periodStart = Carbon::today()->subMonths(3);
            $periodEnd = Carbon::today();

            for ($date = $periodStart; $date <= $periodEnd; $date->addDay()) {
                if (in_array($date->dayOfWeek, [0,6])) {
                    continue;
                }

                $clockIn = $date->copy()->setHour(9)->setMinute(rand(0,15));
                $clockOut = $date->copy()->setHour(17)->setMinute(rand(0,15));

                $attendance = Attendance::create([
                    'staff_id' => $staff->id,
                    'work_date' => $date->format('Y-m-d'),
                    'clock_in' => $clockIn,
                    'clock_out' => $clockOut,
                ]);

                $breakCount = rand(1,2);
                for ($i = 1; $i <= $breakCount; $i++) {
                    $breakStart = $clockIn->copy()->addHours(2 * $i)->addMinutes(rand(0,10));
                    $breakEnd = $breakStart->copy()->addMinutes(rand(39,60));

                    WorkBreak::create([
                        'attendance_id' => $attendance->id,
                        "break{$i}_start" =>$breakStart,
                        "break{$i}_end" => $breakEnd,
                    ]);
                }

                $applicationCount = rand(0,2);
                for ($j = 0; $j < $applicationCount; $j++) {
                    Application::create([
                        'attendance_id' => $attendance->id,
                        'staff_id' => $staff->id,
                        'status' => ['pending', 'approved', 'rejected'][rand(0,2)],
                        'reason' => 'ダミー申請理由',
                    ]);
                }
            }
        }
    }
}
