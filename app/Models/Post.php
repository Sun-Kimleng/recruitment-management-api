<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use HasFactory;

    public $fillable = [
        'job_title',
        'posted_by',
        'job_type',
        'job_level',
        'experience',
        'salary',
        'status',
        'description',
    ];

    public function posted_by(){
        return $this->belongsTo(User::class, 'posted_by', 'id');
    }
}
