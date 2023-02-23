<?php

namespace App\Models\Traits;

use Illuminate\Support\Collection;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

trait ImageManualSortTrait
{
    public function getMediaAttribute(): Collection
    {
        return $this->media()->orderBy('order_column')->get();
    }

    public function sortImages(array $order)
    {
        $ids = $this->media->pluck('id')->toArray();
        foreach ($order as $index => $mediaId) {
            if (in_array($mediaId, $ids, true)) {
                Media::query()->find($mediaId)?->update(['order_column' => $index]);
            }
        }
    }

}
