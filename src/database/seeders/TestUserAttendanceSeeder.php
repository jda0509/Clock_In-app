<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Staff;
use App\Models\Attendance;
use App\Models\WorkBreak;
use App\Models\Application;
use Carbon\Carbon;

class TestUserAttendanceSeeder extends Seeder
{

    public function run()
    {
        $testUser = Staff::updateOrCreate(
            ['email' => 'aaa@gmail.com'],
            [
                'name' => 'テストユーザー',
                'password' => bcrypt('test1234'),
                'email_verified_at' => now(),
            ]
        );

        Auth::guard('staff')->login($testUser, true);

        $periodStart = Carbon::today()->subMonths(3);
        $periodEnd = Carbon::today();

        for ($date = $periodStart; $date <= $periodEnd; $date->addDay()) {
            if (in_array($date->dayOfWeek, [0,6])) {
                continue;
            }

            $exists = Attendance::where('staff_id', $testUser->id)
                ->whereDate('work_date', $date->format('Y-m-d'))
                ->exists();

            if ($exists) {
                continue;
            }

            $clockIn = $date->copy()->setHour(9)->setMinute(rand(0,15));
            $clockOut = $date->copy()->setHour(17)->setMinute(rand(0,15));

            $attendance = Attendance::firstOrCreate([
                'staff_id' => $testUser->id,
                'work_date' => $date->format('Y-m-d'),
                'clock_in' => $clockIn,
                'clock_out' => $clockOut,
            ]);

            if ($attendance->work_breaks()->count() === 0) {
                $breakCount = rand(1,2);
                for ($i = 1; $i <= $breakCount; $i++) {
                    $breakStart = $clockIn->copy()->addHours(2 * $i)->addMinutes(rand(0,10));
                    $breakEnd = $breakStart->copy()->addMinutes(rand(39,60));

                    WorkBreak::create([
                        'attendance_id' => $attendance->id,
                        "break{$i}_start" => $breakStart,
                        "break{$i}_end" => $breakEnd,
                    ]);
                }
            }

            $existingApplications = $attendance->applications()->count();
            $applicationCount = rand(0,2) - $existingApplications;
            for ($j = 0; $j < $applicationCount; $j++) {
                Application::create([
                    'staff_id' => $testUser->id,
                    'attendance_id' => $attendance->id,
                    'status' => ['pending', 'approved', 'rejected'][rand(0,2)],
                    'reason' => 'ダミー申請理由',
                ]);
            }
        }
    }
}
