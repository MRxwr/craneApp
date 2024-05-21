<?php

namespace Modules\Coupons\Http\Livewire\Api;

use Illuminate\Contracts\Support\Renderable;
use Modules\Coupons\Entities\Coupon;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Storage;

class CouponsController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function checkCoupon()
    {
        $services= Coupon::where('is_deleted',0)->get()->toArray();
        $data['message']=_lang('Sevices');
        $data['sevices']= $services;
        return outputSuccess($data);
        
    }

   
}
