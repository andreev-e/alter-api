<?php

namespace App\Http\Resources;

use App\Models\Poi;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Str;

/* @mixin Poi */
class PoiResource extends PoiResourceCollection
{
    public function toArray($request): array
    {
        return array_merge(
            parent::toArray($request),
            [
                'description' => strip_tags(htmlspecialchars_decode($this->description)),
                'route' => htmlspecialchars_decode($this->route),
                'route_o' => htmlspecialchars_decode($this->route_o),
                'nearest' => PoiResourceCollection::collection($this->nearest),
                'tags' => TagResource::collection($this->whenLoaded('tags')),
                'locations' => TagResource::collection($this->whenLoaded('locations')),
                'author' => new UserResource($this->whenLoaded('user')),
                'addon' => $this->addon,
            ]);
    }
}
