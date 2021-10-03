<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Str;

class TagResource extends JsonResource
{

    public $preserveKeys = true;
    public static $wrap = 'tag';

    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id' => $this->ID,
            'name' => $this->NAME,
            'count' => $this->COUNT,
            'url' => Str::slug($this->NAME),
            'flag' => $this->flag,
        ];
    }

    public function with($request)
    {
        return ['status' => 'success'];
    }
}
