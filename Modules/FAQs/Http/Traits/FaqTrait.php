<?php
namespace Modules\FAQs\Http\Traits;

use Modules\FAQs\Entities\Faq;

trait FaqTrait
{
    public static function firstForm()
    {
        $a['title'] = '';
        $a['description'] = '';
       

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
                $cek = Service::where('title', $data['title'])->where('id', '!=', $id_edit)->exists();

                if ($cek) {
                    return [
                        'success' => false,
                        'message' => 'title already exist.'
                    ];
                }
            } else {
                $cek = Service::where('title', $data['title'])->exists();

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
            Faq::find($id)->update($data);
        } else {
            Faq::create($data);
        }
    }

    public static function destroy($id)
    {
        $faq= Faq::find($id);
        $faq->is_deleted = 1;
        $faq->save();
    }

    public static function find_data($id)
    {
        $dt = Faq::find($id);
        return [
            'title' => $dt->title,
            'description' => $dt->description,
        ];
    }
}
