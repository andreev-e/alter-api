<?php

namespace App\Http\Controllers;

use App\Models\Poi;
use Illuminate\Http\Request;
use App\Http\Resources\PoiResource;

class PoiController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $nelng = (float) $request->input('nelng');
        $swlng = (float) $request->input('swlng');
        $nelat = (float) $request->input('nelat');
        $swlat = (float) $request->input('swlat');
        $tag = $request->input('tag');

        if ($nelng < 0) $nelng = 180;

        $pois = Poi::select('poi.*')->where('show', '=', 1)->orderBy('views', 'DESC');
        
        if ($nelng && $swlng && $nelat && $swlat) {
            $pois->where('lng', '<', $swlng)
                ->where('lng', '>', $nelng)
                ->where('lat', '<', $nelat)
                ->where('lat', '>', $swlat)
                ->limit(100);
            $pois->with('tags');
        }

        if ($tag) {     
            $pois->join('relationship', 'poi.id', '=', 'relationship.POSTID');
            $pois->join('tags', 'relationship.TAGID', '=', 'tags.id');
            $pois->where('tags.url', '=', $tag);
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
