<?php

namespace Modules\FAQs\Http\Livewire\Api;

use Illuminate\Contracts\Support\Renderable;
use Modules\Service\Entities\Service;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Storage;

class FaqController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function getServices()
    {
        $services= Service::where('is_deleted',0)->get()->toArray();
        $data['message']=_lang('Sevices');
        $data['sevices']= $services;
        return outputSuccess($data);
        
    }

   
}
