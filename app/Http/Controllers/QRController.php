<?php

namespace App\Http\Controllers;

use App\Http\Requests\QRRequest;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class QRController extends Controller
{
    public function show(QRRequest $request)
    {
        return response(QrCode::size(200)
            ->geo($request->lat, $request->lng), 200, ['Content-type' => 'image/svg']);
    }
}
