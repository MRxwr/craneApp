<?php
namespace Modules\BookingRequest\Http\Traits;

use Modules\BookingRequest\Entities\BookingRequest;

trait BookingRequestTrait
{
    public static function firstForm()
    {
        
        return [
            'request_id' => '',
            'from_location' => '',
            'to_location' =>'',
            'distances' => '',
            'client_name' => '',
            'client_mobile' => '',
            'prices' => '',
            'is_active' => '',
            ];
    }
   



    public static function store_validation($data, $id_edit = null)
    {
       
        return [
            'success' => true,
            'message' => 'Success..'
        ];
        
    }

    public static function store_data($data, $id = null)
    {
        // dd($data);
        if ($id) {
            BookingRequest::find($id)->update($data);
        } else {
            BookingRequest::create($data);
        }
    }

    public static function destroy($id)
    {
       $user= BookingRequest::find($id);
       $user->is_deleted = 1;
       $user->save();
    }

    public static function find_data($id)
    {
        $dt = BookingRequest::with('prices', 'logs')->find($id);
       dd($dt->prices());
        return [
            'request_id' => $dt->request_id,
            'from_location' => $dt->from_location,
            'to_location' => $dt->to_location,
            'distances' => $dt->distances,
            'client_name' => $dt->client->name,
            'client_mobile' => $dt->client->mobile,
            'prices' => $dt->prices()->get()->toArray(),
            'is_active' => $dt->is_active,
            ];
    }
}
