<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Str;

class TagResource extends JsonResource
{
    public $preserveKeys = true;
    public static $wrap = 'tag';

    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'count' => $this->COUNT,
//            'url' => $this->url,
//            'flag' => $this->flag,
//            'locations' => $this->locations,
//            'children' => $this->children,
        ];
    }
}
