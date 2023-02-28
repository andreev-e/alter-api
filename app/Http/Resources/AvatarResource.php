<?php

namespace App\Http\Resources;

use App\Models\Comment;
use Illuminate\Http\Resources\Json\JsonResource;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

/* @mixin Media */
class AvatarResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'width' => $this->getCustomProperty('width'),
            'height' => $this->getCustomProperty('height'),
            'original' => $this->getCustomProperty('temporary_url') ? asset('storage/' . $this->getCustomProperty('temporary_url')) : $this->getUrl('thumb'),
        ];
    }
}
