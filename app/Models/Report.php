<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
    use HasFactory;

    protected $fillable = [
        'interview_date',
        'interview_status',
        'interview_name',
        'interview_position',
        'description',
    ];
}
