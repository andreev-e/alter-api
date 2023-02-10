<?php

namespace App\Http\Resources;

use App\Models\Comment;
use Illuminate\Http\Resources\Json\JsonResource;

/* @mixin Comment */
class ImageResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'original' => $this->getUrl('full'),
            'preview' => $this->getUrl('thumb'),
            'created_at' => $this->created_at,
        ];
    }
}
