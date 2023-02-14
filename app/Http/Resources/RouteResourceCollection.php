<?php

namespace App\Http\Resources;

use App\Models\Poi;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Str;

/* @mixin Poi */
class RouteResourceCollection extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'url' => Str::slug($this->name),
            'author' => $this->author,
            'difficilty' => $this->difficilty,
            'days' => $this->days,
            'cost' => $this->cost,
            'views' => $this->views,
            'start' => $this->START,
            'finish' => $this->FINISH,
            'thumb' => $this->getFirstMediaUrl('route-image', 'thumb'),
            'show' => $this->show,
        ];
    }
}
