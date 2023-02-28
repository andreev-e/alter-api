<?php

namespace App\Http\Controllers;

use App\Models\Checkin;
use App\Models\Poi;
use Auth;
use Illuminate\Http\Response;

class CheckinController extends Controller
{
    public function toggle(Poi $poi): Response
    {
        $checkin = Checkin::query()
            ->where('poi_id', $poi->id)
            ->where('user_id', Auth::user()->id)->first();
        if ($checkin) {
            $checkin->delete();
        } else {
            Checkin::query()->create([
                'poi_id' =>  $poi->id,
                'user_id' =>  Auth::user()->id
            ]);
        }
        return response()->noContent();
    }
}
