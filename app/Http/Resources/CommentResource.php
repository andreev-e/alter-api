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
            'user' => new UserResource($this->user),
            'email' => $this->email,
            'comment' => $this->comment,
            'time' => $this->time,
            'object_name' => $this->object->name,
        ];
    }
}
