<?php

namespace App\Http\Controllers;

use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Str;

class ResourceController extends Controller
{
    /**
     * Fetches immutable resources.
     *
     * @param  string  $type
     * @return AnonymousResourceCollection
     */
    public function index(string $type) : AnonymousResourceCollection
    {
        $class = Str::of($type)->camel()->ucfirst()->prepend('App\\Models\\');

        return JsonResource::collection(call_user_func($class . '::paginate'));
    }
}
