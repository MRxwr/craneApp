<?php
namespace Modules\Coupons\Http\Traits;

use Modules\Coupons\Entities\Coupon;

trait CouponTrait
{
    public static function firstForm()
    {
        $a['title'] = '';
        $a['coupon_code'] = '';
        $a['coupon_type'] = '';
        $a['coupon_value'] = '';
        $a['expiry_date'] = '';

        return $a;
    }

    public static function store_validation($data, $id_edit = null)
    {
        // dd($data);
        if (!$data['title']) {
            // $this->emit('pesanGagal', 'Name Required');
            return [
                'success' => false,
                'message' => 'title Required'
            ];
        } elseif (!$data['description']) {
            // $this->emit('pesanGagal', 'email Required');
            return [
                'success' => false,
                'message' => 'description Required'
            ];
        } else {

            if ($id_edit) {
                $cek = Coupon::where('title', $data['title'])->where('id', '!=', $id_edit)->exists();

                if ($cek) {
                    return [
                        'success' => false,
                        'message' => 'title already exist.'
                    ];
                }
            } else {
                $cek = Coupon::where('title', $data['title'])->exists();

                if ($cek) {
                    return [
                        'success' => false,
                        'message' => 'Maaf email sudah digunakan..'
                    ];
                }
            }

            return [
                'success' => true,
                'message' => 'Success..'
            ];
        }
    }

    public static function store_data($data, $id = null)
    {
        // dd($data);
        if ($id) {
            Coupon::find($id)->update($data);
        } else {
            Coupon::create($data);
        }
    }

    public static function destroy($id)
    {
        $page= Coupon::find($id);
        $page->is_deleted = 1;
        $page->save();
    }

    public static function find_data($id)
    {
        $dt = Coupon::find($id);

        return [
            'title' => $dt->title,
            'coupon_code' => $dt->coupon_code,
            'coupon_type' => $dt->coupon_type,
            'coupon_value' => $dt->coupon_value,
            'expiry_date' => $dt->expiry_date,
        ];
    }
}
