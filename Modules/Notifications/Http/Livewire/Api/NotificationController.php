<?php

namespace Modules\Notifications\Http\Livewire\Api;

use Illuminate\Contracts\Support\Renderable;
use Modules\Notifications\Entities\Notification;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Storage;

class NotificationController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function getNotifications()
    {
        $notifications= Notification::where('is_deleted',0)->get()->toArray();
        $data['message']=_lang('Notifications');
        $data['sevices']= $notifications;
        return outputSuccess($data);
    }

   
}
