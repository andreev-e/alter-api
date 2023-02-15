<?php

namespace App\Http\Resources;

use App\Models\Location;
use Illuminate\Http\Resources\Json\JsonResource;

/* @mixin Location */
class LocationResourceCollection extends JsonResource
{

    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'count' => $this->count,
            'url' => $this->url,
            'code' => $this->code,
            'name_rod_ed' => $this->name_rod_ed,
            'name_dat_ed' => $this->name_dat_ed,
            'name_predlozh_ed' => $this->name_predlozh_ed,
            'name_en' => $this->name_en,
            'name_rod' => $this->name_rod,
        ];
    }
}
