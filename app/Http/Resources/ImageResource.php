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
            'uuid' => $this->uuid,
            'original_url' => $this->original_url,
            'preview_url' => $this->preview_url,
            'created_at' => $this->created_at,
        ];
    }
}
