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
            'logs' => '',
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
        //dd($dt->prices);
        $prices=[];
        if($dt->prices){
            foreach($dt->prices as $price){
                $prices[$price->id]['driver'] = $price->driver->name;
                $prices[$price->id]['mobile'] = $price->driver->mobile;
                $prices[$price->id]['price'] =  $price->price;
                $prices[$price->id]['is_accepted'] = $price->is_accepted;
            }
        }
        //dd($prices);
        return [
            'request_id' => $dt->request_id,
            'from_location' => $dt->from_location,
            'to_location' => $dt->to_location,
            'distances' => $dt->distances,
            'client_name' => $dt->client->name,
            'client_mobile' => $dt->client->mobile,
            'prices' => $prices,
            'logs' => $dt->logs,
            'is_active' => $dt->is_active,
            ];
    }
}
