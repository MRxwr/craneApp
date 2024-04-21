<?php

namespace App\Http\Livewire\Locales;

use App\Models\Locale;
use App\Models\Language;
use Livewire\Component;
use App\Traits\MasterData;
use Livewire\WithPagination;
use Modules\Roles\Entities\Role;
use App\Traits\LocaleTrait;
use Modules\Roles\Http\Traits\PermissionTrait;

class Index extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';

    public $paging, $search;
    public $forms = [];
    public $id_edit, $is_edit;

    public function mount()
    {
        $this->paging = 25;
        $this->forms = LocaleTrait::firstForm();
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
        if (!akses('change-status-locale')) {
            $this->emit('pesanGagal', 'Access Denied..');
            return false;
        }

        try {
            $dt = Locale::find($id);

            // if ($dt->is_paten == 1) {
            //     $this->emit('pesanGagal', 'Sorry, this user can not edited..');
            // } else {
            updateStatus(new Locale, $id);

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
        $this->forms = LocaleTrait::firstForm();
        $this->emit('modalAdd', 'show');
    }

    public function edit_data($id)
    {
        $this->is_edit = 1;
        $this->id_edit = $id;

        $this->forms = LocaleTrait::find_data($id);

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
                $validasi = LocaleTrait::store_validation($this->forms, $this->id_edit);
            } else {
                $validasi = LocaleTrait::store_validation($this->forms);
            }
            // dd($validasi);
            if (!$validasi['success']) {
                $this->emit('pesanGagal', $validasi['message']);
            } else {
                if ($this->id_edit) {
                    LocaleTrait::store_data($this->forms, $this->id_edit);
                } else {
                    LocaleTrait::store_data($this->forms);
                }

                $this->emit('modalAdd', 'hide');

                $this->forms = LocaleTrait::firstForm();
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
            LocaleTrait::destroy($id);
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
        $data = Locale::filter($q)->latest()->paginate($this->paging);
        $pagings = MasterData::list_pagings();
        $languages = Language::where('status',1)->get();

        return view('livewire.locales.index', compact(
            'data',
            'pagings',
            'languages'
         ))
            ->layout('layouts.main', [
                'title' =>_lang('Manage Locales') 
            ]);
    }
}
