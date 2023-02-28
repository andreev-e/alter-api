<?php

namespace App\Http\Resources;

use App\Models\Checkin;
use Illuminate\Http\Resources\Json\JsonResource;

/* @mixin Checkin */
class CheckinResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'created_at' => $this->created_at,
            'user' => new UserResourceCollection($this->user),
        ];
    }
}
