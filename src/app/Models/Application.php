<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Application extends Model
{
    use HasFactory;

    protected $table = 'applications';

    protected $fillable = [
        'staff_id',
        'attendance_id',
        'reason',
        'status',
    ];

    public function staff()
    {
        return $this->belongsTo(Staff::class);
    }

    public function attendance()
    {
        return $this->belongsTo(Attendance::class);
    }

    public function applicationBreaks()
    {
        return $this->hasMany(ApplicationBreak::class);
    }

    public function getStatusTextAttribute()
    {
        return match($this->status){
            'pending' => '承認待ち',
            'approved' => '承認済み',
            'rejected' => '却下',
            default => '不明',
        };
    }
}
