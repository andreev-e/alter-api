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
            'name_en' => $this->name_en,
            'lat' => $this->lat,
            'lng' => $this->lng,
            'url' => Str::slug($this->name),
            'ytb' => $this->ytb,
            'type' => $this->type,
            'dist' => $this->dist,
            'author' => $this->author,
            'views' => $this->views,
            'views_month' => $this->views_month,
            'views_today' => $this->views_today,
            'comments' => $this->comments,
            'show' => $this->show,
            'date' => $this->date,
            'thumb' => $this->media?->first()?->getUrl('thumb'),
        ];
    }
}
