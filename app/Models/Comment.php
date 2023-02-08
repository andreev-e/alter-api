<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    protected $primaryKey = 'commentid';

    protected $fillable = [
        'backlink',
        'name',
        'comment',
        'time',
        'approved',
        'email',
    ];

    public function object()
    {
        return $this->hasOne(Poi::class, 'id', 'backlink')->select('id', 'name');
    }

    public function user()
    {
        return $this->hasOne(User::class, 'username', 'name');
    }

}
