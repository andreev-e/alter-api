<?php

namespace App\Http\Resources;

use App\Models\Comment;
use Illuminate\Http\Resources\Json\JsonResource;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

/* @mixin Media */
class ImageResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'original' => $this->getUrl('full'),
            'preview' => $this->getCustomProperty('temporary_url') ? asset('storage/' . $this->getCustomProperty('temporary_url')) : $this->getUrl('thumb'),
            'created_at' => $this->created_at,
            'width' => $this->getCustomProperty('width'),
            'height' => $this->getCustomProperty('height'),
            'copyright' => $this->getCustomProperty('author'),
        ];
    }
}
