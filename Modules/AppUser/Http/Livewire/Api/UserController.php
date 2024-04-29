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
    
    public function getProfile(Request $request){
        $data = array();
        $rules = [
            'token' => 'required',
            // Add more rules as needed
        ];

        // Perform validation
        $validator = Validator::make($request->all(), $rules);

        // Check if validation fails
        if ($validator->fails()) {
            // If validation fails, return response with validation errors
            $data['message']=_lang('validation error');
            $data['errors'] = $validator->errors();
            return outputError($data);
        }
        $appuser = AppUser::where('mobile', $mobileNumber)->first();
            if ($appuser){
                    $data['message']=_lang('Profile');
                    $data['user']= $appuser->toArray();
                    return outputSuccess($data);
                }else{
                $data['message']=_lang('mobile not  verified');
                return outputError($data); 
        }

    }

    
}
