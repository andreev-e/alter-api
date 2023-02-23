<?php

namespace App\Enums;
use App\Models\Poi;
use App\Models\Route;

enum Commentables: string
{
    case poi = Poi::class;
    case route = Route::class;
}

