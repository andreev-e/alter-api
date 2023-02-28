<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserResourceCollection extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'username' => $this->username,
            'name' => $this->username,
            'firstname' => $this->firstname,
            'lastname' => $this->lastname,
            'publications' => $this->publications,
            'userlevel' => $this->userlevel,
            'thumb' => $this->getFirstMediaUrl('user-image', 'thumb'),
            'images' => AvatarResource::collection($this->media),
        ];
    }
}
