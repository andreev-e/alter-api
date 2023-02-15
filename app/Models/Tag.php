<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class Tag extends Model
{
    protected $hidden = array('lat', 'lng', 'scale');

    protected $fillable = [
        'url',
        'name',
        'TYPE',
        'parent',
        'lat',
        'lng',
        'scale',
    ];

    protected $casts = [
        'lat' => 'float',
        'lng' => 'float',
    ];

    public function pois()
    {
        return $this->belongsToMany(Poi::class, 'relationship', 'TAGID', 'POSTID');
    }
}
