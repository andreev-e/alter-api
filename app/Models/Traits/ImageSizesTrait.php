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
            if ($media->with > self::THUMB_SIZE || $media->height > self::THUMB_SIZE) {
                $this->addMediaConversion('thumb')
                    ->width(self::THUMB_SIZE)
                    ->crop('crop-center', self::THUMB_SIZE, self::THUMB_SIZE);
            } else {
                $this->addMediaConversion('thumb');
            }

            if ($media->with > self::FULL_SIZE || $media->height > self::FULL_SIZE) {
                $this->addMediaConversion('full')
                    ->fit(Manipulations::FIT_MAX, self::FULL_SIZE, self::FULL_SIZE);
            } else {
                $this->addMediaConversion('full');
            }
        }
    }
}
