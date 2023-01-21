<?php

namespace App\Http\Resources;

use App\Models\Comment;
use Illuminate\Http\Resources\Json\JsonResource;

/* @mixin Comment */
class CommentResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'commentid' => $this->commentid,
            'name' => $this->name,
            'comment' => $this->comment,
            'time' => date('Y-m-d H:i:s', $this->time),
            'object_name' => $this->object->name,
        ];
    }
}
