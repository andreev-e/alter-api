<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Str;

/* @mixin \App\Models\Tag */
class TagResource extends JsonResource
{

    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'count' => $this->COUNT,
            'url' => $this->url,
            'flag' => $this->flag,
            'lat' => $this->lat,
            'lng' => $this->lng,
            'children' => self::collection($this->whenLoaded('children')),
        ];
    }
}
