<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphTo;

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
        'type',
    ];

    public function commentable(): MorphTo
    {
        return $this->morphTo();
    }

    public function user(): HasOne
    {
        return $this->hasOne(User::class, 'username', 'name');
    }

}
