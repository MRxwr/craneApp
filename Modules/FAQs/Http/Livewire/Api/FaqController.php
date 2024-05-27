<?php

namespace Modules\FAQs\Http\Livewire\Api;

use Illuminate\Contracts\Support\Renderable;
use Modules\FAQs\Entities\Faq;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Storage;

class FaqController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function getFaqs()
    {
        $faqs= Faq::where('is_active',1)->where('is_deleted',0)->get()->toArray();
        $data['message']=_lang('Faqs');
        $data['faqs']= $faqs;
        return outputSuccess($data);
        
    }

   
}
