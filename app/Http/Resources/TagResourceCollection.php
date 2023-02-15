<?php

namespace App\Http\Resources;

use App\Models\Tag;
use Illuminate\Http\Resources\Json\JsonResource;

/* @mixin Tag */
class TagResourceCollection extends JsonResource
{

    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'count' => $this->COUNT,
            'url' => $this->url,
            'code' => $this->code,
            'lat' => $this->lat,
            'lng' => $this->lng,
            'zoom' => $this->scale,
            'children' => self::collection($this->whenLoaded('children')),
            'parents' => $this->getParents(),
            'NAME_ROD_ED' => $this->NAME_ROD_ED,
            'NAME_DAT_ED' => $this->NAME_DAT_ED,
            'NAME_PREDLOZH_ED' => $this->NAME_PREDLOZH_ED,
            'NAME_en' => $this->NAME_en,
        ];
    }
}
