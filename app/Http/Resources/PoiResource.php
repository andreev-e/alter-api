<?php

namespace App\Http\Resources;

use App\Models\Poi;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Str;

/* @mixin Poi */
class PoiResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'lat' => $this->lat,
            'lng' => $this->lng,
            'url' => Str::slug($this->name),
            'description' => htmlspecialchars_decode($this->description),
            'route' => htmlspecialchars_decode($this->route),
            'route_o' => htmlspecialchars_decode($this->route_o),
            'tags' => TagResource::collection($this->whenLoaded('tags')),
            'locations' => TagResource::collection($this->whenLoaded('locations')),
            'nearest' => self::collection($this->nearest),
            'addon' => $this->addon,
            'ytb' => $this->ytb,
            'type' => $this->type,
        ];
    }
}
