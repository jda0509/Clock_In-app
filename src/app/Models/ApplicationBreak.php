<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApplicationBreak extends Model
{
    use HasFactory;

    protected $table = 'application_breaks';
    protected $fillable = [
        'application_id' ,
        'new_break1_start' ,
        'new_break1_end' ,
        'new_break2_start',
        'new_break2_end',
    ];

    public function application()
    {
        return $this->belongsTo(Application::class);
    }
}

