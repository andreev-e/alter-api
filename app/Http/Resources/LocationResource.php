<?php

namespace App\Http\Resources;

use App\Models\Location;

/* @mixin Location */
class LocationResource extends LocationResourceCollection
{

    public function toArray($request)
    {
        return array_merge(
            collect(parent::toArray($request))->toArray(),
            [
                'lat' => $this->lat,
                'lng' => $this->lng,
                'zoom' => $this->scale,
                'parents' => $this->getParents(),
                'children' => self::collection($this->whenLoaded('children')),
                'tags' => TagResourceCollection::collection($this->tags),
            ]);
    }
}
