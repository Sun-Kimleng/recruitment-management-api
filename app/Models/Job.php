<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Job extends Model
{
    use HasFactory;

    protected $fillable = [
        'description',
        'name',
        'added_by',
     
    ];

    public function added_by(){
        $this->belongsTo(User::class, 'added_by', 'id');
    }
}
