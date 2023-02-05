<?php

namespace App\Http\Resources;

use App\Models\Poi;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Str;

/* @mixin Poi */
class PoiResource extends PoiResourceCollection
{
    public function toArray($request): array
    {
        return [
            parent::toArray($request),
            'nearest' => self::collection($this->nearest),
        ];
    }
}
