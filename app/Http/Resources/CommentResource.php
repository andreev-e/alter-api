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
            'object_name' => $this->commentable->name,
            'object_name_en' => $this->commentable->name_en,
            'object_id' => $this->commentable->id,
            'object_type' => $this->commentable::TYPE,
            'user' => new UserResourceCollection($this->whenLoaded('user')),
            'email' => $this->email,
            'comment' => $this->comment,
            'created_at' => $this->created_at,
            'approved' => $this->approved,
        ];
    }
}
