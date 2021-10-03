<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Tag extends Model
{

    protected $hidden = array('lat', 'lng', 'scale');

    protected $appends = array('url');

    public function getUrlAttribute()
    {
        return '/region/' . Str::slug($this->NAME);  
    }

}
