<?php

namespace App\Http\Controllers;
use App\Http\Resources\TagResourceCollection;
use App\Models\Tag;
use App\Http\Resources\TagResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class TagController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
         return TagResourceCollection::collection(Tag::query()
             ->where('TYPE','=',0)
             ->orderBy('COUNT', 'DESC')->get());
    }

    public function countries(): AnonymousResourceCollection
    {
        return TagResourceCollection::collection(Tag::query()
            ->with(['children', 'parent.parent.parent'])
            ->where('TYPE','!=',0)
            ->where('parent', 0)
            ->where('COUNT', '>', 2)
            ->orderBy('COUNT', 'DESC')->get());
    }

    public function show(Tag $tag): TagResource
    {
        return new TagResource($tag);
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
