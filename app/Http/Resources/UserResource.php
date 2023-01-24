<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Str;

class UserResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'username' => $this->username,
            'firstname' => $this->firstname,
            'lastname' => $this->lastname,
            'publications' => $this->publications,
            'about' => $this->about,
            'homepage' => $this->homepage,
            'userlevel' => $this->userlevel,
            'regdate' => $this->regdate,
        ];
    }
}
