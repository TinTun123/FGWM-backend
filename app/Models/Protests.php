<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Protests extends Model
{
    use HasFactory;
    protected $fillable = [
        'id',
        'date',
        'user_id',
        'total_msg',
        'total_view',
        'bodyText',
        'title',
        'imgURL',
        'bodyText'

    ];
}
