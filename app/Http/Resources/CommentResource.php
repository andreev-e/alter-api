<?php

namespace App\Http\Resources;

use App\Enums\Commentables;
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
            'object_id' => $this->commentable->id,
            'object_type' => Commentables::tryFrom($this->commentable_type)->name,
            'user' => new UserResourceCollection($this->whenLoaded('user')),
            'email' => $this->email,
            'comment' => $this->comment,
            'time' => $this->time,
            'approved' => $this->approved,
        ];
    }
}
