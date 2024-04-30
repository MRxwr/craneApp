<?php
namespace Modules\AppUser\Http\Livewire\Api;

use Illuminate\Contracts\Support\Renderable;
use Modules\AppUser\Entities\AppUser;
use Modules\AppUser\Entities\OtpUser;
use Illuminate\Routing\Controller;
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
        $dob = $request->input('dob');
        $appuser =  AppUser::where('token',$token)->first();
        if ($appuser){
            $appuser->name = $name;
            $appuser->dob = $dob;
            
            if ($request->hasFile('avator')) {
                $imageName = time().'.'.$request->avator->extension();  
                $request->avator->move(public_path('avators'), $imageName);
                $appuser->avator = $imageName;
            }

            $appuser->save();
            $data['token']= $token;
            $data['user']= $appuser->toArray();
            $data['message']=_lang('Successfully Update');
            return outputSuccess($data);
        }
    }

    
}
