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
                'user' => new UserResource($this->whenLoaded('user')),
                'description' => strip_tags(htmlspecialchars_decode($this->description)),
                'route' => htmlspecialchars_decode($this->route),
                'route_o' => htmlspecialchars_decode($this->route_o),
                'nearest' => PoiResourceCollection::collection($this->nearest),
                'tags' => TagResource::collection($this->whenLoaded('tags')),
                'routes' => RouteResourceCollection::collection($this->whenLoaded('routes')),
                'locations' => TagResource::collection($this->whenLoaded('locations')),
                'links' => $this->links,
                'copyright' => $this->copyright,
                'images' => ImageResource::collection($this->media),
            ]);
    }
}
