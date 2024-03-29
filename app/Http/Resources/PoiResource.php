<?php

namespace App\Http\Resources;

use App\Models\Poi;

/* @mixin Poi */
class PoiResource extends PoiResourceCollection
{
    public function toArray($request): array
    {
        return array_merge(
            collect(parent::toArray($request))->except('dist')->toArray(),
            [
                'addon' => $this->addon,
                'description' => strip_tags(htmlspecialchars_decode($this->description)),
                'route' => htmlspecialchars_decode($this->route),
                'route_o' => htmlspecialchars_decode($this->route_o),
                'nearest' => PoiResourceCollection::collection($this->nearest),
                'tags' => TagResourceCollection::collection($this->whenLoaded('tags')),
                'checkins' => CheckinResource::collection($this->whenLoaded('checkins')),
                'routes' => RouteResourceCollection::collection($this->whenLoaded('routes')),
                'locations' => TagResourceCollection::collection($this->whenLoaded('locations')),
                'links' => $this->links,
                'copyright' => $this->copyright,
                'dominatecolor' => $this->dominatecolor,
                'images' => ImageResource::collection($this->media),
                'created_at' => $this->created_at,
                'updated_at' => $this->updated_at,
            ]);
    }
}
