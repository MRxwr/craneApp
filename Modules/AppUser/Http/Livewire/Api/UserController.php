<?php
namespace Modules\AppUser\Http\Livewire\Api;

use Illuminate\Contracts\Support\Renderable;
use Modules\AppUser\Entities\AppUser;
use Modules\AppUser\Entities\OtpUser;
use Illuminate\Routing\Controller;
use Modules\Pages\Entities\Page;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    
    public function userProfile(Request $request){
        $data = array();
        $token = $request->header('Authorization');
        // Check if validation fails
        if (!$token) {
            // If validation fails, return response with validation errors
            $data['message']=_lang('Authorization token is requred');
            $data['errors'] = ['token'=>'header Authorization token is requred'];
            return outputError($data);
        }
        $token = str_replace('Bearer ', '', $token);
        try {
            $user = AppUser::where('token',$token)->first();
            if ($user) {
                // Authentication successful
                $data['message']=_lang('Profile');
                $data['user']= $user->toArray();
                return outputSuccess($data);
                // Proceed with authenticated user logic
            } else {
                // Authentication failed
                $data['message']=_lang('Unauthorized');
                return outputError($data); 
                
            }
        } catch (\Exception $e) {
            // Log or handle the exception
            $data['message']=_lang('Authentication error');
            return outputError($data);
           
        }
    }

    public function updateProfile(Request $request){
        $data = array();
        $token = $request->header('Authorization');
        // Check if validation fails
        if (!$token) {
            // If validation fails, return response with validation errors
            $data['message']=_lang('Authorization token is requred');
            $data['errors'] = ['token'=>'header Authorization token is requred'];
            return outputError($data);
        }
        $token = str_replace('Bearer ', '', $token);
        $name = $request->input('name');
        $email = $request->input('email');
        $dob = $request->input('dob');
        $appuser =  AppUser::where('token',$token)->first();
        if ($appuser){
            $appuser->name = $name;
            $appuser->email = $email;
            $appuser->dob = $dob;

            if ($request->hasFile('avator')) {
                $imageName = time().'.'.$request->avator->extension();  
                $request->avator->move(public_path('avators'), $imageName);
                $appuser->avator = 'storage/avators/'.$imageName;
            }
            
            if ($request->hasFile('licence')) {
                $imageName = 'LNC'.time().'.'.$request->licence->extension();  
                $request->licence->move(public_path('drivers'), $imageName);
                $appuser->licence = 'storage/drivers/'.$imageName;
            }
            if ($request->hasFile('idfront')) {
                $imageName = 'IDF'.time().'.'.$request->idfront->extension();  
                $request->idfront->move(public_path('drivers'), $imageName);
                $appuser->idfront = 'storage/drivers/'.$imageName;
            }
            if ($request->hasFile('idback')) {
                $imageName = 'IDB'.time().'.'.$request->idback->extension();  
                $request->idback->move(public_path('drivers'), $imageName);
                $appuser->idback = 'storage/drivers/'.$imageName;
            }

            $appuser->save();
            $data['token']= $token;
            $data['user']= $appuser->toArray();
            $data['message']=_lang('Successfully Update');
            return outputSuccess($data);
        }
    }
    public function getProfileSetting(Request $request){
        $data = array();
        $token = $request->header('Authorization');

        // Check if validation fails
        if (!$token) {
            // If validation fails, return response with validation errors
            $data['message']=_lang('Authorization token is requred');
            $data['errors'] = ['token'=>'header Authorization token is requred'];
            return outputError($data);
        }
        $token = str_replace('Bearer ', '', $token);
        try {
            $user = AppUser::where('token',$token)->first();
            if ($user) {
                // Authentication successful
                if(!getUserMeta('wallet',$user->id)){
                    upadteUserMeta('wallet',0.00,$user->id);
                }
                if(!getUserMeta('is_notify',$user->id)){
                    upadteUserMeta('is_notify',1,$user->id);
                }
                $data['message']=_lang('Profile');
                $data['meta']['language']= $user->language;
                $data['meta']['is_notify']= getUserMeta('is_notify',$user->id);
                $data['meta']['wallet']= getUserMeta('wallet',$user->id);
                $data['about']= Page::find(1)->toArray();
                $data['terms']= Page::find(2)->toArray();
                $data['policy']= Page::find(3)->toArray();
                $data['contact']['number']= getSetting('contact');
                $data['contact']['email']= getSetting('email');
                $data['contact']['address']= getSetting('address');
                return outputSuccess($data);
                // Proceed with authenticated user logic
            } else {
                // Authentication failed
                $data['message']=_lang('Unauthorized');
                return outputError($data); 
                
            }
        } catch (\Exception $e) {
            // Log or handle the exception
            $data['message']=_lang('Authentication error');
            return outputError($data);
           
        }
    }
    public function updateProfileSetting(Request $request){
        $data = array();
        $token = $request->header('Authorization');
        // Check if validation fails
        if (!$token) {
            // If validation fails, return response with validation errors
            $data['message']=_lang('Authorization token is requred');
            $data['errors'] = ['token'=>'header Authorization token is requred'];
            return outputError($data);
        }
        $token = str_replace('Bearer ', '', $token);

        $language = $request->input('language');
        $is_notify = $request->input('is_notify');
        $appuser =  AppUser::where('token',$token)->first();
        if ($appuser){
            $appuser->language = $language;
            $appuser->save();
            if(!getUserMeta('is_notify',$user->id)){
                upadteUserMeta('is_notify',$is_notify,$user->id);
            }
            $data['message']=_lang('Profile');
            $data['meta']['language']= $user->language;
            $data['meta']['is_notify']= getUserMeta('is_notify',$user->id);
            $data['meta']['wallet']= getUserMeta('wallet',$user->id);
            $data['about']= Page::find(1)->toArray();
            $data['terms']= Page::find(2)->toArray();
            $data['policy']= Page::find(3)->toArray();
            $data['contact']['number']= getSetting('contact');
            $data['contact']['email']= getSetting('email');
            $data['contact']['address']= getSetting('address');
            return outputSuccess($data);
        }
    }
    
}
