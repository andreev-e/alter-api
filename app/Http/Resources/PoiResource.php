<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Str;

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
            'tags' => $this->tags,
            'locations' => array_merge(
                $this->locations->toArray(),
                [['id' => $this->id, 'name' => $this->name, 'url' => null]] // adding last breadcrumb without link
            ),
            'addon' => $this->addon,
            'ytb' => $this->ytb,
            'type' => $this->type,
        ];
    }
}
