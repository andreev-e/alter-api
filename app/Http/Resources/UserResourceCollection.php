<?php

namespace App\Http\Resources;

use App\Models\User;
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
            'images' => count($this->media) ? AvatarResource::collection($this->media) : [
                'id' => 0,
                'width' => User::THUMB_SIZE,
                'height' => User::THUMB_SIZE,
                'original' => 'https://via.placeholder.com/600',
            ],
            'checkins' => CheckinResource::collection($this->whenLoaded('checkins')),
        ];
    }
}
