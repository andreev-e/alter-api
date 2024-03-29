<?php

namespace App\Http\Resources;

use App\Models\Tag;

/* @mixin Tag */
class TagResource extends TagResourceCollection
{
    public function toArray($request)
    {
        return array_merge(
            collect(parent::toArray($request))->toArray(),
            [
                'lat' => $this->lat,
                'lng' => $this->lng,
                'zoom' => $this->scale,
            ]);
    }
}
