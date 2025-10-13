<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Attendance extends Model
{
    use HasFactory;

    protected $table = 'attendances';

    protected $fillable = [
        'staff_id',
        'work_date',
        'clock_in',
        'clock_out',
        'total_work_minutes',
    ];

    public function staff()
    {
        return $this->belongsTo(Staff::class);
    }

    public function work_break()
    {
        return $this->hasOne(WorkBreak::class);
    }

    public function applications()
    {
        return $this->hasMany(Application::class);
    }

    public function getBreakTimeAttribute()
    {
        $break = 0;
        if ($this->workBreak) {
            $b1 = $this->diffInHours($this->workBreak->break1_start, $this->workBreak->break1_end);
            $b2 = $this->diffInHours($this->workBreak->break2_start, $this->workBreak->break2_end);
            $break = $b1 + $b2;
        }

        $breakMinutes = $break * 60;
        return sprintf('%02d:%02d', floor($breakMinutes / 60), $breakMinutes % 60);
    }

    public function getWorkHoursAttribute()
    {
        if (!$this->clock_in || !this->clock_out) return '00:00';

        $break = 0;
        if ($this->workBreak) {
            $b1 = $this->diffInHours($this->workBreak->break1_start, $this->workBreak->break1_end);
            $b2 = $this->diffInHours($this->workBreak->break2_start, $this->workBreak->break2_end);
            $break = $b1 + $b2;
        }

        $totalMinutes = Carbon::parse($this->clock_in)->diffInMinutes(Carbon::parse($this->clock_out)) - ($break * 60);
        return sprintf('%02d:%02d', floor($totalMinutes / 60), $totalMinutes % 60);
    }

    private function diffInHours($start, $end)
    {
        if (!$start || !$end) return 0;
        return Carbon::parse($start)->diffInMinutes(Carbon::parse($end)) / 60;
    }

}
