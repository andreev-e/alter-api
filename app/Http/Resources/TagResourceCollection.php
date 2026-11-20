<?php

namespace App\Http\Resources;

use App\Models\Tag;
use Illuminate\Http\Resources\Json\JsonResource;

/* @mixin Tag */
class TagResourceCollection extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'count' => $this->count,
            'url' => $this->url,
            'code' => $this->code,
            'NAME_ROD_ED' => $this->name_rod_ed,
            'NAME_DAT_ED' => $this->name_dat_ed,
            'NAME_PREDLOZH_ED' => $this->name_predlozh_ed,
            'name_en' => $this->name_en,
            'NAME_ROD' => $this->name_rod,
        ];
    }
}
