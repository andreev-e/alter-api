<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Image\Manipulations;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Route extends Model implements HasMedia
{
    use InteractsWithMedia;


    public $timestamps = false;
    /**
     * @throws \Spatie\Image\Exceptions\InvalidManipulation
     */
    public function registerMediaConversions(Media $media = null): void
    {
        if ($media->with > 600 || $media->height > 600) {
            $this->addMediaConversion('thumb')
                ->width(600)
                ->crop('crop-center', 600, 600);
        } else {
            $this->addMediaConversion('thumb');
        }

        if ($media->with > 1200 || $media->height > 1200) {
            $this->addMediaConversion('full')
                ->fit(Manipulations::FIT_MAX, 1200, 1200);
        } else {
            $this->addMediaConversion('full');
        }
    }
}
