<?php

namespace App\Http\Resources;

class RouteResource extends RouteResourceCollection
{
    public function toArray($request): array
    {
        return array_merge(
            parent::toArray($request),
            [
                'description' => $this->description,
                'encoded_route' => $this->encoded_route,
                'links' => $this->links,
                'route' => $this->route,
                'points' => explode('|',$this->POINTS),
                'transport_car' => $this->transport_car,
                'transport_train' => $this->transport_train,
                'transport_bus' => $this->transport_bus,
                'transport_plane' => $this->transport_plane,
                'transport_ship' => $this->transport_ship,
                'transport_bike' => $this->transport_bike,
                'transport_walk' => $this->transport_walk,
                'images' => ImageResource::collection($this->media),
            ]);
    }
}

