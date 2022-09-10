<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Candidate extends Model
{
    use HasFactory;

    protected $fillable= [
        'name',
        'workplace',
        'city',
        'school',
        'job_status',
        'interested_job',
        'job_level',
        'description',
        'gender',
        'birthday',
        'height',
        'weight',
        'phone',
        'email',
        'address',
        'educations',
        'skills',
        'experiences',
        'languages',
        'cv',
        
    ];

    protected $casts = [
        'education' => 'array',
        'skills' => 'array',
        'experiences' => 'array',
        'educations' => 'array',
        'languages'=>'array'
    ];

    public function users(){
        return $this->belongsTo(User::class);
    }
}
