<?php

namespace Modules\BookingRequest\Http\Livewire\Bookings;

use Livewire\Component;
use App\Traits\MasterData;
use Livewire\WithPagination;
use Modules\BookingRequest\Entities\BookingRequest;
use Modules\BookingRequest\Http\Traits\BookingRequestTrait;


class Canceled extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';

    public $paging, $search;
    public $forms = [];
    public $id_edit, $is_edit,$avator ;
    public $prices ;
    public $logs ;

    public function mount()
    {
        $this->paging = 25;
        $this->forms = BookingRequestTrait::firstForm();
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
            $dt = BookingRequest::find($id);

            // if ($dt->is_paten == 1) {
            //     $this->emit('pesanGagal', 'Sorry, this user can not edited..');
            // } else {
            //updateStatus(new BookingRequest, $id);
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
        $this->forms = BookingRequestTrait::firstForm();
        $this->emit('modalAdd', 'show');
    }

    public function edit_data($id)
    {
        $this->is_edit = 1;
        $this->id_edit = $id;
        $this->forms = BookingRequestTrait::find_data($id); 
       
        $this->emit('modalAdd', 'show');
    }

    public function refund_data($id)
    {
        $this->forms = BookingRequestTrait::find_data($id); 
        $this->emit('modalRefund', 'show');
    }
    public function refund_process($id,$type)
    {
        $dt = BookingRequest::find($id);
        dd($dt);
        //$this->forms;
       
        $this->emit('pesanSukses', 'Sucess..');
    }

    public function logs_data($id)
    {
        $this->is_edit = 1;
        $this->id_edit = $id;

        $this->forms = BookingRequestTrait::find_data($id);

        $this->emit('modalLogs', 'show');
    }

    public function prices_data($id)
    {
        $this->is_edit = 1;
        $this->id_edit = $id;
        $this->forms = BookingRequestTrait::find_data($id);
        //dd($this->forms['prices']);
        $this->emit('modalPrice', 'show');
    }

    public function store()
    {
        $this->validate([
            'forms.*' => 'required'
        ]);
        try {

            // dd($this->forms);
            if ($this->id_edit) {
                $validasi = BookingRequestTrait::store_validation($this->forms, $this->id_edit);
            } else {
                $validasi = BookingRequestTrait::store_validation($this->forms);
            }
            // dd($validasi);
            if (!$validasi['success']) {
                $this->emit('pesanGagal', $validasi['message']);
            } else {
                if ($this->id_edit) {
                    BookingRequestTrait::store_data($this->forms, $this->id_edit);
                } else {
                    BookingRequestTrait::store_data($this->forms);
                }

                $this->emit('modalAdd', 'hide');
                $this->forms = BookingRequestTrait::firstForm();
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
            BookingRequestTrait::destroy($id);
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
        $data = BookingRequest::where('is_active',4)->where('is_deleted',0)->filter($q)->latest()->paginate($this->paging);
        $pagings = MasterData::list_pagings();
        return view('bookingrequest::livewire.requests.canceled', compact(
            'data',
            'pagings',
            
        ))
        ->layout('layouts.main', [
                'title' => _lang('Manage Canceled Booking Request ')
        ]);
    }
}
