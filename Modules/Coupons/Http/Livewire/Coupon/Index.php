<?php

namespace Modules\Coupons\Http\Livewire\Coupon;

use Modules\Coupons\Entities\Coupon;
use Livewire\Component;
use App\Traits\MasterData;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use Modules\Coupons\Http\Traits\CouponTrait;
use Modules\Roles\Http\Traits\PermissionTrait;
use Illuminate\Support\Facades\Storage;

class Index extends Component
{
    use WithPagination;
    use WithFileUploads;
    protected $paginationTheme = 'bootstrap';

    public $paging, $search;
    public $forms = [];
    public $id_edit, $is_edit,$image;

    protected $listeners =['fileUpload'=>'handleFileUpload'];

    public function handleFileUpload ($imageData){
        
        $this->image = $imageData;
    }
    public function mount()
    {
        $this->paging = 25;
        $this->forms = CouponTrait::firstForm();
        // dd($this->forms);

    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updated($key, $val)
    {
        // dd($key . ' | ' . $val);
        // dd($this->forms);
    }

    public function update_status($id)
    {
        if (!akses('change-status-user')) {
            $this->emit('pesanGagal', 'Access Denied..');
            return false;
        }

        try {
            $dt = Coupon::find($id);

            // if ($dt->is_paten == 1) {
            //     $this->emit('pesanGagal', 'Sorry, this user can not edited..');
            // } else {
            updateStatus(new Coupon, $id);

            $this->emit('pesanSukses', 'Sucess..');
            // }
        } catch (\Exception $th) {
            //throw $th;
            $pesan = MasterData::pesan_gagal($th);

            $this->emit('pesanGagal', $pesan);
        }
    }

    public function tambah_data()
    {
        $this->reset(['is_edit', 'id_edit']);
        $this->forms = CouponTrait::firstForm();
        $this->emit('modalAdd', 'show');
    }

    public function edit_data($id)
    {
        $this->is_edit = 1;
        $this->id_edit = $id;

        $this->forms = CouponTrait::find_data($id);

        $this->emit('modalAdd', 'show');
    }

    public function store()
    {
        $this->validate([
            'forms.*' => 'required'
        ]);
        try {

           
            // dd($this->forms);
            if ($this->id_edit) {
                $validasi = CouponTrait::store_validation($this->forms, $this->id_edit);
            } else {
                $validasi = CouponTrait::store_validation($this->forms);
            }
            // dd($validasi);
            if (!$validasi['success']) {
                $this->emit('pesanGagal', $validasi['message']);
            } else {
                if ($this->id_edit) {

                    CouponTrait::store_data($this->forms, $this->id_edit);
                } else {
                   
                    CouponTrait::store_data($this->forms);
                }

                $this->emit('modalAdd', 'hide');

                $this->forms = CouponTrait::firstForm();
               
                $this->emit('pesanSukses', 'Store Success..');
                $this->reset(['is_edit', 'id_edit']);
            }
        } catch (\Exception $th) {
            //throw $th;
            $pesan = MasterData::pesan_gagal($th);
            $this->emit('pesanGagal', $pesan);
        }
    }

    public function destroy($id)
    {
        try {
            CouponTrait::destroy($id);
            $this->emit('pesanSukses', 'Success..');
        } catch (\Exception $th) {
            //throw $th;
            $pesan = MasterData::pesan_gagal($th);
            $this->emit('pesanGagal', $pesan);
        }
    }

    public function render()
    {
        $q = $this->search;
        $data = Coupon::where('is_deleted',0)->filter($q)->latest()->paginate($this->paging);
        $pagings = MasterData::list_pagings();
       
        return view('coupons::livewire.coupon.index', compact(
            'data',
            'pagings',
        ))
            ->layout('layouts.main', [
                'title' => 'Manage Coupon'
            ]);
    }
}