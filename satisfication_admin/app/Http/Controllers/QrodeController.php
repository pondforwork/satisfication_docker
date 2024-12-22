<?php

namespace App\Http\Controllers;

use SimpleSoftwareIO\QrCode\Facades\QrCode;

use Illuminate\Http\Request;

class QrodeController extends Controller
{
    public function generate(Request $request)
    {
        $counterName = $request->input('counterName');
        $counterId = $request->input('counterId');
        $baseUrl = env('APP_URL'); 
        $fullUrl = $baseUrl .'checkin/'.  $counterId;
        $qrCode = QrCode::size(300)->generate($fullUrl);
        return view('qrcode.qrcode', compact('qrCode', 'counterName'));
    }

}
