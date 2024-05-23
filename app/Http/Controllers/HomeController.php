<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use App\Traits\MasterData;
use Modules\BookingRequest\Entities\BookingRequest;
use Modules\BookingRequest\Entities\BookingLog;
use Modules\BookingRequest\Entities\BookingPrice;
use Modules\BookingRequest\Entities\BookingPayment;
use Modules\AppUser\Entities\AppUser;
use Illuminate\Routing\Controller;
use Modules\Pages\Entities\Page;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use App\Services\FCMService;

class HomeController extends Controller
{
    public function index()
    {
        return view('home');
    }
    public function Success(Request $request)
    {
        
        if($request->bsid){
            $decodedData = base64_decode($request->bsid);
            dd($decodedData);
        }
        return view('page');
    }
    public function Failed(Request $request)
    { 
        return view('page');
    }


}
