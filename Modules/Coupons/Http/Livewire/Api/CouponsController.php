<?php

namespace Modules\Banners\Http\Livewire\Api;

use Illuminate\Contracts\Support\Renderable;
use Modules\Pages\Entities\Page;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Storage;

class BannerController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function getPages()
    {
        $services= Service::where('is_deleted',0)->get()->toArray();
        $data['message']=_lang('Sevices');
        $data['sevices']= $services;
        return outputSuccess($data);
        
    }

   
}
