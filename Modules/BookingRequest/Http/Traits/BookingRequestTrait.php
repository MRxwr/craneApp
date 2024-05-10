<?php
namespace Modules\BookingRequest\Http\Traits;

use Modules\BookingRequest\Entities\BookingRequest;

trait BookingRequestTrait
{
    public static function firstForm()
    {
        
        return [];
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
            'client_name' => $dt->client->name,
            'is_active' => $dt->is_active,
                  ];
    }
}
