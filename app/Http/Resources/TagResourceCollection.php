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
            'count' => $this->COUNT,
            'url' => $this->url,
            'code' => $this->code,
            'NAME_ROD_ED' => $this->NAME_ROD_ED,
            'NAME_DAT_ED' => $this->NAME_DAT_ED,
            'NAME_PREDLOZH_ED' => $this->NAME_PREDLOZH_ED,
            'name_en' => $this->name_en,
            'NAME_ROD' => $this->NAME_ROD,
        ];
    }
}
