<?php

namespace App\Http\Controllers;

use App\Http\Requests\PoiRequest;
use App\Models\Poi;
use Illuminate\Http\Request;
use App\Http\Resources\PoiResource;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class PoiController extends Controller
{
    public function index(PoiRequest $request): AnonymousResourceCollection
    {
        $pois = Poi::query()
            ->where('show', '=', 1)->orderBy('views', 'DESC');

        if ($request->south) {
            $pois->where('lat', '>', $request->south);
        }
        if ($request->north) {
            $pois->where('lat', '<', $request->north);
        }
        if ($request->east) {
            $pois->where('lng', '<', $request->east);
        }
        if ($request->west) {
            $pois->where('lng', '>', $request->west);
        }

        if ($request->south || $request->north || $request->east || $request->west) {
            $pois->with('tags')->limit(100);
        }

        if ($request->tag) {
            $pois->join('relationship', 'poi.id', '=', 'relationship.POSTID');
            $pois->join('tags', 'relationship.TAGID', '=', 'tags.id');
            $pois->where('tags.url', '=', $request->tag);
            $pois->with('tags');
        }

        // dump($pois->limit(10)->get()->toArray());
        // return;

        return PoiResource::collection($pois->paginate());
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
     * @param  \App\Models\Poi  $poi
     * @return \Illuminate\Http\Response
     */
    public function show(Poi $poi)
    {
        return new PoiResource($poi);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Poi  $poi
     * @return \Illuminate\Http\Response
     */
    public function edit(Poi $poi)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Poi  $poi
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Poi $poi)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Poi  $poi
     * @return \Illuminate\Http\Response
     */
    public function destroy(Poi $poi)
    {
        //
    }
}
