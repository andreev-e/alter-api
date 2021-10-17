<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Str;

class CommentResource extends JsonResource
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'commentid' => $this->commentid,
            'name' => $this->name,
            'comment' => $this->comment,
            'time' => date('Y-m-d H:i:s', $this->time),
            'object' => $this->object,
        ];
    }
}
