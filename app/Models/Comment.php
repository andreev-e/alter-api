<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    protected $primaryKey = 'commentid';

    protected $casts = [
        'time' => 'datetime:Y-m-d H:i:s',
    ];

}
