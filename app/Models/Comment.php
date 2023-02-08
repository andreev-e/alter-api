<?php

namespace App\Models;

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


    protected $casts = [
        'approved' => 'boolean',
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
