<?php
namespace Modules\BookingRequest\Http\Traits;

use Modules\BookingRequest\Entities\BookingRequest;
use Modules\BookingRequest\Entities\BookingPayment;

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
            'payments' => '',
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
        dd($dt->payment);

        $dt->payments = BookingPayment::where('request_id', $id)->get();
        $prices=[];
        $payments=[];
        if($dt->payments){
            foreach($dt->payments as $payment){
                $payments[$payment->id]['driver'] = $payment->client->name;
                $payments[$payment->id]['payment_type'] = $payment->payment_type;
                $payments[$payment->id]['payment_status'] = $payment->payment_status;
                $payments[$payment->id]['amount'] =  $payment->payment_amount;
                $payments[$payment->id]['transaction_id'] = $payment->transaction_id; //$payments[$payment->id]['is_accepted'] = $payment->is_accepted;
            }
        }
        if($dt->prices){
            foreach($dt->prices as $price){
                $prices[$price->id]['driver'] = $price->driver->name;
                $prices[$price->id]['mobile'] = $price->driver->mobile;
                $prices[$price->id]['price'] =  $price->price;
                $prices[$price->id]['is_accepted'] = $price->is_accepted;
            }
        }
        $logs=[];
        if($dt->logs){
            foreach($dt->logs as $log){
                if($log){
                    if($log->driver){
                        $logs[$log->id]['driver'] = $log->driver->name;
                    }else{
                        $logs[$log->id]['driver'] =_lang('Not Assign');
                    }
                    if($log->client){
                        $logs[$log->id]['client'] = $log->client->name;
                    }else{
                        $logs[$log->id]['client'] =_lang('Not Assign');
                    }
                    $logs[$log->id]['activity'] =  $log->activity; 
                }
               
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
            'payments' => $payments,
            'logs' => $logs,
            'is_active' => $dt->is_active,
            ];
    }
}
