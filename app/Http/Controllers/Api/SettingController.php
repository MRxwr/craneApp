<?php

namespace App\Http\Controllers\Api;

use Modules\Service\Entities\Service;
use Modules\Banners\Entities\Banner;
use Modules\BookingRequest\Entities\BookingRequest;
use Modules\BookingRequest\Entities\BookingLog;
use Modules\BookingRequest\Entities\BookingPrice;
use Modules\BookingRequest\Entities\DriverPosition;
use Modules\AppUser\Entities\AppUser;
use Modules\AppUser\Entities\AppUserActivity;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Storage;

class SettingController extends Controller
{
    public function getSetting(Request $request)
    {
        $data = array();
        try {
            $setting = Setting::where('id',1)->first();
            if($request->input('action')=='version'){
                if($request->input('type')=='list'){
                    $data['versions']['ios']=$setting->ios_version;
                    $data['versions']['android']=$setting->android_version;
                    return outputSuccess($data); 
                }else if($request->input('type')=='update'){

                    if($request->input('ios')){
                        $setting->ios_version = $request->input('ios');
                    }
                    if($request->input('android')){
                        $setting->android_version = $request->input('android');
                    }
                    // if($request->input('ios')){
                    //     $setting->ios_app_link = $request->input('ios_link');
                    // }
                    // if($request->input('ios')){
                    //     $setting->android_app_link = $request->input('android_link');
                    // }
                    $setting->save();
                    
                    $data['versions']['ios']=$setting->ios_version;
                    $data['versions']['android']=$setting->android_version;
                    return outputSuccess($data); 
                }else{
                    $data['versions']['ios']=$setting->ios_version;
                    $data['versions']['android']=$setting->android_version;
                    return outputSuccess($data); 
                }
        }else if($request->input('action')=='link'){
            if($request->input('type')=='list'){
                $data['links']['ios']=$setting->ios_app_link;
                $data['links']['android']=$setting->android_app_link;
                return outputSuccess($data); 

            }else if($request->input('type')=='update'){

                if($request->input('ios_link')){
                    $setting->ios_app_link = $request->input('ios_link');
                }
                if($request->input('android_link')){
                    $setting->android_app_link = $request->input('android_link');
                }
                $setting->save();
                $data['links']['ios']=$setting->ios_app_link;
                $data['links']['android']=$setting->android_app_link;
                return outputSuccess($data); 
            }else{
                $data['links']['ios']=$setting->ios_app_link;
                $data['links']['android']=$setting->android_app_link;
                return outputSuccess($data); 
            }
        }
        } catch (\Exception $e) {
            $data['message']=_lang('Api error');
            $data['errors'] = [
                'message' => $e->getMessage(),
                'code' => $e->getCode(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ];
            return outputError($data); 
        }
        
    }
}