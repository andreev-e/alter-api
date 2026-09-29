<?php

namespace App\Http\Controllers\Traits;

use App\Models\Poi;
use Auth;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

trait SetsMediaCustomPropertiesTrait
{
    private function setMediaCustomProperties(Media $media, string $localPath, $img): void
    {
        [$width, $height] = self::fullSizeDimensions($img->width(), $img->height());

        $media->setCustomProperty('author', Auth::user()->username);
        $media->setCustomProperty('width', $width);
        $media->setCustomProperty('height', $height);
        $media->setCustomProperty('orig_width', $img->width());
        $media->setCustomProperty('orig_height', $img->height());
        $media->setCustomProperty('temporary_url', $localPath);
        $media->save();
    }

    /**
     * Размеры конверсии 'full': она уменьшает только большие картинки, маленькие остаются как есть
     */
    public static function fullSizeDimensions(int $width, int $height): array
    {
        $convRatio = max(max($width, $height) / Poi::FULL_SIZE, 1);

        return [round($width / $convRatio), round($height / $convRatio)];
    }
}
