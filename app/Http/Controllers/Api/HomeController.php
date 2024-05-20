<?php

namespace App\Http\Controllers\Api;

use Modules\Service\Entities\Service;
use Modules\Banners\Entities\Banner;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Storage;

class HomeController extends Controller
{
    public function getHome()
    {
        $services= Service::where('is_active',1)->where('is_deleted',0)
        ->select('id', 'title', 'description','image') ->get()->toArray();
        $banners= Banner::where('is_active',1)->where('is_deleted',0)
        ->select('id', 'title', 'description','image') ->get()->toArray();
        $data['message']=_lang('Get Home Data');
        $data['sevices']= $services;
        $data['banners']= $banners;
        return outputSuccess($data);  
    }
}
