<?php
namespace Modules\BookingRequest\Http\Livewire\Api;

use Illuminate\Contracts\Support\Renderable;
use App\Traits\MasterData;
use Modules\BookingRequest\Entities\BookingRequest;
use Modules\BookingRequest\Entities\BookingLog;
use Modules\BookingRequest\Entities\BookingPrice;
use Modules\AppUser\Entities\AppUser;
use Illuminate\Routing\Controller;
use Modules\Pages\Entities\Page;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use App\Services\FCMService;

class UserBookingController extends Controller
{
    public function GetClientHome(Request $request){
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
                $data['message']=_lang('Send Crane Request');
                $bidr= new BookingRequest();
                $bidr->request_id = time();
                $bidr->client_id = $user->id;
                $bidr->from_location = $request->input('from_location');
                $bidr->to_location = $request->input('to_location');
                $bidr->from_latlong = $request->input('from_latlong');
                $bidr->to_latlong = $request->input('to_latlong');
                $bidr->service_id = $request->input('service_id');
                $bidr->distances = $request->input('distances');
                if($bidr->save()){
                    $drivers = AppUser::where('user_type', 2)->where('is_active',1)->where('is_deleted',0)->get();
                     if($drivers->count()>0){
                        foreach($drivers as $driver){
                          $price = new  BookingPrice();
                          $price->request_id =$bidr->id;
                          $price->client_id =$user->id;
                          $price->driver_id =$driver->id; 
                          if($price->save()){
                            firebaseNotification($user);
                          }
                        }
                     }
                     $activity = _lang('The Crane requested by ').$user->name;
                     AddBookingLog($bidr,$activity);
                     $data['request']= $bidr->toArray();
                    return outputSuccess($data);
                }
                
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

   
    
}
