<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MigrationCom extends Model
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

    public function messages() {
        return $this->morphMany(Message::class, 'messageable');
    }

    public function user() {
        return $this->belongsTo(User::class);
    }
}
