<?php

namespace App\Http\Resources;

use App\Models\Poi;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Str;

class UserResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'username' => $this->username,
            'name' => $this->username,
            'firstname' => $this->firstname,
            'lastname' => $this->lastname,
            'publications' => $this->publications,
            'about' => $this->about,
            'homepage' => $this->homepage,
            'userlevel' => $this->userlevel,
            'regdate' => $this->regdate,
            'lat' => $this->lat,
            'lng' => $this->lng,
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
