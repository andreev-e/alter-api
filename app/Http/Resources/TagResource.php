<?php

namespace App\Http\Resources;

use App\Models\Tag;
use Illuminate\Http\Resources\Json\JsonResource;

/* @mixin Tag */
class TagResource extends TagResourceCollection
{

    public function toArray($request)
    {
        return array_merge(
            collect(parent::toArray($request))->except('dist')->toArray(),
            [
                'tags' => TagResourceCollection::collection($this->tags),
            ]);
    }
}
