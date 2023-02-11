<?php

namespace App\Http\Controllers;
use App\Models\Tag;
use App\Http\Resources\TagResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class TagController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
         return TagResource::collection(Tag::query()
             ->where('TYPE','=',0)
             ->orderBy('name', 'DESC')->get());
    }

    public function countries(): AnonymousResourceCollection
    {
        return TagResource::collection(Tag::query()
            ->with(['children', 'parent.parent.parent'])
            ->where('TYPE','!=',0)
            ->where('parent', 0)
            ->where('COUNT', '>', 2)
            ->orderBy('COUNT', 'DESC')->get());
    }

    public function show(Tag $tag): TagResource
    {
        return new TagResource($tag->load(['children', 'parent.parent.parent']));
    }

    public function update(Request $request, $id)
    {
        //
    }

    public function destroy($id)
    {
        //
    }
}
