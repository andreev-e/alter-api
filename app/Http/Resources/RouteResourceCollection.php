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
            'description' => $this->description,
            'url' => Str::slug($this->name),
            'author' => $this->author,
            'encoded_route' => $this->encoded_route,
            'difficilty' => $this->difficilty,
            'links' => $this->links,
            'route' => $this->route,
            'days' => $this->days,
            'cost' => $this->cost,
            'transport_car' => $this->transport_car,
            'transport_train' => $this->transport_train,
            'transport_bus' => $this->transport_bus,
            'transport_plane' => $this->transport_plane,
            'transport_ship' => $this->transport_ship,
            'transport_bike' => $this->transport_bike,
            'transport_walk' => $this->transport_walk,
            'views' => $this->views,
            'start' => $this->START,
            'finish' => $this->FINISH,
            'points' => explode('|',$this->POINTS),
        ];
    }
}
