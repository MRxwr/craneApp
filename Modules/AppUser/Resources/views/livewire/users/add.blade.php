@extends('layouts.main',[
'title'=>'Dashboard'
])
@section('content')
<div>
    <div class="card">
        <div class="card-body row">

            <div class=" col-md-6 modal-content">
            <form action="{{ route('appuser.store') }}" method="POST" enctype="multipart/form-data">
                 @csrf <!-- CSRF protection -->
                            <div class="card-body">
                                {{ $message ?? '' }}
                                <div class="form-group">
                                    <label for="name">{{ _lang('Name') }}</label>
                                    <input wire:model="forms.name" type="text" class="form-control" id="name"
                                        placeholder="{{ _lang('Name') }}">
                                    {{-- {{ $forms['name'] }} --}}
                                    @error('forms.name')
                                        <span style="color: red;" class="error">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="form-row">
                                    <div class="form-group col-md-12">
                                        <label for="inputmobile4">{{ _lang('Mobile') }}</label>
                                        <input wire:model="forms.mobile" type="text" class="form-control"
                                            id="inputmobile4" placeholder="{{ _lang('Mobile') }} ">
                                        @error('forms.mobile')
                                            <span style="color: red;" class="error">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="form-row">
                                    <div class="form-group col-md-12">
                                        <label for="inputEmail4">{{ _lang('Email') }} </label>
                                        <input wire:model="forms.email" type="text" class="form-control"
                                            id="inputEmail4" placeholder="{{ _lang('Email') }} ">
                                        @error('forms.email')
                                            <span style="color: red;" class="error">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="form-row">
                                    <div class="form-group col-md-12">
                                        <label for="inputDob4">{{ _lang('DOB') }} </label>
                                        <input wire:model="forms.dob" type="date" class="form-control"
                                            id="inputDob4" placeholder="{{ _lang('Date of Birth') }} ">
                                        @error('forms.dob')
                                            <span style="color: red;" class="error">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="form-row">
                                    <div class="form-group col-md-12">
                                        <label for="role_id">{{ _lang('User Type') }}</label>
                                        <select class="form-control" wire:model="forms.user_type">
                                            <option value="">Select Type</option>
                                            
                                                <option value="1">{{ _lang('Client') }}</option>
                                                <option value="2">{{ _lang('Driver') }}</option>
                                        </select>
                                        @error('forms.user_type')
                                            <span style="color: red;" class="error">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <div class="col-lg-3 col-12">{{_lang('Avator')}} </div>
                                    <div class="col-lg-9 col-12">
                                        <input type="file" class="form-control"  wire:model="forms.avator">
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer">
                                <button type="submit" class="btn btn-primary">{{_lang('Submit')}}</button>
                                <img src="{{ asset('loading-bar.gif') }}" alt="" wire:loading wire:target="store">
                            </div>
                        </form>
               
            </div>
            
        </div>
    </div>
</div>
@endsection
@section('scripts')
       
@endsection