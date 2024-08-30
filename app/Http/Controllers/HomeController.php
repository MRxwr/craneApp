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
        $data=[];
        if($request->bsid){
            $decodedData = base64_decode($request->bsid);
            $ids=explode('|',$decodedData);
            if(!empty($ids)){
                $bidid = $ids[0];
                $pid = $ids[1];
                $price = BookingPrice::find($pid);
                $dt = BookingRequest::with('prices')->find($bidid);
                $dt->status = 1;
                $dt->driver_id = $price->driver_id?$price->driver_id:0;
                $dt->save();
                $price->is_accepted = 1;
                $price->save();
                $payment=BookingPayment::where('request_id',$bidid)->first();
                $payment->driver_id = $price->driver_id?$price->driver_id:0;
                $payment->transaction_id=$request->paymentId;
                $payment->payment_status='success';
                $payment->save();
                $data['message'] =' Payment Successfully Done';
                $data['price'] = $price;
                $data['payment'] = $payment; 
                // return outputSuccess($data);
            }
        }
        return outputSuccess($data);  
        //return view('page',compact('data'));
    }
    public function Failed(Request $request)
    { 
        $data=[];
        if($request->bsid){
            $decodedData = base64_decode($request->bsid);
            $ids=explode('|',$decodedData);
            if(!empty($ids)){
                $bidid = $ids[0];
                $pid = $ids[1];
                $dt = BookingRequest::with('prices')->find($bidid);
                $price = BookingPrice::find($pid);
                //dd($price->driver_id);
                $payment=BookingPayment::where('request_id',$bidid)->first();
                $payment->driver_id = $price->driver_id?$price->driver_id:0;
                $payment->transaction_id='';
                $payment->payment_status='failed';
                $payment->save();
                $data['dt'] = $dt;
                $data['price'] = $price;
                $data['payment'] = $payment; 
                $data['message'] =' Payment failed';
                
            }
        }
        return outputSuccess($data);  
        //return view('page',compact('data'));
    }


}
