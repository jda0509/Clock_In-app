<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Application;

class UpdateApplicationsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Application::where('status', 'pending')->each(function ($application) {
            $application->update([
                'new_clock_in' => '09:00',
                'new_clock_out' => '18:00',
                'reason' => 'ダミー申請理由',
            ]);
        });
    }
}
