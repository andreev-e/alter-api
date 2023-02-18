<?php

namespace App\Models\Traits;

use Spatie\Image\Manipulations;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

trait ImageSizesTrait
{
    /**
     * @throws \Spatie\Image\Exceptions\InvalidManipulation
     */
    public function registerMediaConversions(Media $media = null): void
    {
        if ($media) {
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
}
