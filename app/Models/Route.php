<?php

namespace App\Models;

use App\Models\Traits\ImageSizesTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Route extends Model implements HasMedia
{
    use InteractsWithMedia;
    use ImageSizesTrait {
        ImageSizesTrait::registerMediaConversions insteadof InteractsWithMedia;
    }

    public $timestamps = false;

    public function pois(): BelongsToMany
    {
        return $this->belongsToMany(Poi::class);
    }
}
