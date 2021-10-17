<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    protected $primaryKey = 'commentid';

    public function object() 
    {
        return $this->hasOne(Poi::class, 'id', 'backlink')->select('id', 'name');
    }
    
}
