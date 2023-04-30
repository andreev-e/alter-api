<?php

namespace App\Http\Controllers;

use App\Http\Resources\LocationResource;
use App\Http\Resources\TagResourceCollection;
use App\Models\Tag;
use App\Http\Resources\TagResource;
use Cache;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class TagController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        return Cache::remember('tags', 60 * 60 * 24, function() {
            return TagResourceCollection::collection(Tag::query()
                ->orderBy('COUNT', 'DESC')->get());
        });
    }

    public function show(Tag $tag): TagResource
    {
        return Cache::remember('tag:' . $tag->url, 60 * 60 * 24, function() use ($tag) {
            return new TagResource($tag);
        });
    }
}
