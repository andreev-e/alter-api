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
             ->orderBy('COUNT', 'DESC')->get());
    }

    public function locations(): AnonymousResourceCollection
    {
        return TagResource::collection(Tag::query()
            ->with('children.children.children')
            ->where('TYPE','!=',0)
            ->where('parent', 0)
            ->orderBy('COUNT', 'DESC')->get());
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($url)
    {
        $tag = Tag::where('url', '=', $url)->firstOrFail();
        $tag->locations = array_merge(
            $tag->getParents(),
            [['id' => $tag->id, 'name' => $tag->name, 'url' => '']] // adding last breadcrumb without link
        );

        $tag->children = $tag->getChildren();

        // dump($tag->toArray());
        // return;

        return new TagResource($tag);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
