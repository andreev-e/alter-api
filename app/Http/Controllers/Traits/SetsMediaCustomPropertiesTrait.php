<?php

namespace App\Http\Controllers\Traits;

use App\Models\Poi;
use Auth;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

trait SetsMediaCustomPropertiesTrait
{
    private function setMediaCustomProperties(Media $media, string $localPath, $img): void
    {
        $maxDimension = max($img->width(), $img->height());
        $convRatio = $maxDimension / Poi::FULL_SIZE;
        $width = round($img->width() / $convRatio);
        $height = round($img->height() / $convRatio);

        $media->setCustomProperty('author', Auth::user()->username);
        $media->setCustomProperty('width', $width);
        $media->setCustomProperty('height', $height);
        $media->setCustomProperty('orig_width', $img->width());
        $media->setCustomProperty('orig_height', $img->height());
        $media->setCustomProperty('temporary_url', $localPath);
        $media->save();
    }
}
