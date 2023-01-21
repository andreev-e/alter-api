<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Poi extends Model
{
    protected $table = 'poi';

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class, 'relationship',  'POSTID', 'TAGID')
            ->withPivot('POSTID');
    }

    public function locations()
    {
        return $this->belongsToMany(Tag::class, 'relationship', 'POSTID', 'TAGID')->where('TYPE', '<>',
            0)->orderBy('COUNT', 'desc');
    }

    public function twits()
    {
        return $this->hasMany(Comment::class, 'backlink')->where('approved', '=', 1)->orderBy('time', 'desc');
    }

}
