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
            'login' => $this->user?->username,
            'comment' => $this->comment,
            'time' => $this->time,
            'backlink' => $this->backlink,
            'object_name' => $this->object->name,
        ];
    }
}
