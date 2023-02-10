<?php

namespace App\Http\Resources;

use App\Models\Poi;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Str;

/* @mixin Poi */
class PoiResourceCollection extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'lat' => $this->lat,
            'lng' => $this->lng,
            'url' => Str::slug($this->name),
            'ytb' => $this->ytb,
            'type' => $this->type,
            'dist' => $this->dist,
            'author' => $this->author,
            'views' => $this->views,
            'show' => $this->show,
            'date' => $this->date,
            'images' => ImageResource::collection($this->media),
        ];
    }
}
