<?php
namespace Modules\BookingRequest\Http\Traits;

use Modules\BookingRequest\Entities\BookingRequest;

trait BookingRequestTrait
{
    public static function firstForm()
    {
        
        return [];
    }
    public function getRequestsPriceById($id)
    {
        $bid = BookingRequest::find(id);
        // dd($permissions);
        $role = Role::find($id);
        $data = $role->permissions;
        $data = json_decode($data);
        // dd($data);
        foreach ($data as $key => $value) {
            # code...
            $permissions[$value] = true;
        }
        // dd($permissions);
        return $permissions;
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
        $dt = BookingRequest::find($id);
       
        return [
            'request_id' => $dt->request_id,
            'from_location' => $dt->from_location,
            'from_latlong' => $dt->from_latlong,
            'distances' => $dt->distances,
            'client_name' => $dt->client->name,
            'client_mobile' => $dt->client->mobile,
            'is_active' => $dt->is_active,
            ];
    }
}
