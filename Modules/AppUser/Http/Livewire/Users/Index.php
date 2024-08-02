<?php

namespace Modules\AppUser\Http\Livewire\Users;

use Livewire\Component;
use App\Traits\MasterData;
use Livewire\WithPagination;
use Modules\AppUser\Entities\AppUser;
use Modules\AppUser\Http\Traits\AppUserTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;


class Index extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';

    public $paging, $search;
    public $forms = [];
    public $id_edit, $is_edit,$avator,$password ;

    public function mount()
    {
        $this->paging = 25;
        $this->forms = AppUserTrait::firstForm();
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
            $dt = AppUser::find($id);

            // if ($dt->is_paten == 1) {
            //     $this->emit('pesanGagal', 'Sorry, this user can not edited..');
            // } else {
            updateStatus(new AppUser, $id);

            $this->emit('pesanSukses', 'Sucess..');
            // }
        } catch (\Exception $th) {
            //throw $th;
            $pesan = MasterData::pesan_gagal($th);

            $this->emit('pesanGagal', $pesan);
        }
    }

    public function change_password($id)
    {
        $this->is_edit = 1;
        $this->id_edit = $id;

        $this->forms = AppUserTrait::find_data($id);

        $this->emit('modalChnagePassword', 'show');
    }
    public function update_password(){
        $this->validate([
            'password' => 'required'
        ]);
        try {
            if ($this->id_edit) {
                $dt = AppUser::find($this->id_edit);
                // dd($this->password);
                isset($this->password) ? $dt->password = Hash::make($this->password) : '';
               if( $dt->save()){
                 $this->emit('modalChnagePassword', 'hide');
                 $this->emit('pesanSukses', 'Sucess..');
                 $this->reset(['is_edit', 'id_edit']);
               }
            }
       } catch (\Exception $th) {
           //throw $th;
           $pesan = MasterData::pesan_gagal($th);
           $this->emit('pesanGagal', $pesan);
       }
    }

    public function tambah_data()
    {
        $this->reset(['is_edit', 'id_edit']);
        $this->forms = AppUserTrait::firstForm();
        $this->emit('modalAdd', 'show');
    }

    public function edit_data($id)
    {
        $this->is_edit = 1;
        $this->id_edit = $id;

        $this->forms = AppUserTrait::find_data($id);

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
                $validasi = AppUserTrait::store_validation($this->forms, $this->id_edit);
            } else {
                $validasi = AppUserTrait::store_validation($this->forms);
            }
            // dd($validasi);
            if (!$validasi['success']) {
                $this->emit('pesanGagal', $validasi['message']);
            } else {
                if ($this->id_edit) {
                    AppUserTrait::store_data($this->forms, $this->id_edit);
                } else {
                    AppUserTrait::store_data($this->forms);
                }

                $this->emit('modalAdd', 'hide');

                $this->forms = AppUserTrait::firstForm();
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
            AppUserTrait::destroy($id);
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
        $data = AppUser::filter($q)->latest()->paginate($this->paging);
        $pagings = MasterData::list_pagings();
        

        return view('appuser::livewire.users.index', compact(
            'data',
            'pagings',
            
        ))
        ->layout('layouts.main', [
                'title' => _lang('Manage App User')
        ]);
    }
}
