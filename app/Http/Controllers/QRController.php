<?php

namespace App\Http\Controllers;

use App\Http\Requests\QRRequest;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class QRController extends Controller
{
    public function show(QRRequest $request)
    {
        return QrCode::size(100)
            ->format('svg')
            ->generate('GEO:' . $request->lat . "," . $request->lng . "");
    }
}
