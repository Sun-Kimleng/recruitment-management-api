<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Apply extends Model
{
    use HasFactory;

    public $table = 'applies';

    public $fillable = [
        'post_id',
        'candidate_id',
        'status',
    ];
}
