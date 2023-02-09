<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class RouteComment extends Model
{
    protected $primaryKey = 'commentid';

    protected $casts = [
        'approved' => 'boolean',
    ];

    public function object(): HasOne
    {
        return $this->hasOne(Poi::class, 'id', 'backlink')->select('id', 'name');
    }

    public function user(): HasOne
    {
        return $this->hasOne(User::class, 'name', 'name');
    }
}
