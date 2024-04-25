@extends('layouts.main',[
 'title'=>_lang('Edit Service')
])
@section('content')
<div>
    <div class="card">
        <div class="card-body row">

            <div class=" col-md-6 modal-content">
            <form action="{{ route('appuser.update', $user->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT') <!-- Use PUT method for updating -->
                            <div class="card-body">
                                {{ $message ?? '' }}
                                <div class="card-body">
                                {{ $message ?? '' }}
                                <div class="form-group">
                                    <label for="name">{{ _lang('Name') }}</label>
                                    <input name="name" type="text" class="form-control" id="name"
                                        placeholder="{{ _lang('Name') }}" value="{{$user->name}}">
                                    
                                    @error('name')
                                        <span style="color: red;" class="error">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="form-row">
                                    <div class="form-group col-md-12">
                                        <label for="inputmobile4">{{ _lang('Mobile') }}</label>
                                        <input name="mobile" type="text" class="form-control"
                                            id="inputmobile4" placeholder="{{ _lang('Mobile') }} " value="{{$user->mobile}}">
                                        @error('mobile')
                                            <span style="color: red;" class="error">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="form-row">
                                    <div class="form-group col-md-12">
                                        <label for="inputEmail4">{{ _lang('Email') }} </label>
                                        <input name="email" type="text" class="form-control"
                                            id="inputEmail4" placeholder="{{ _lang('Email') }} " value="{{$user->email}}">
                                        @error('email')
                                            <span style="color: red;" class="error">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="form-row">
                                    <div class="form-group col-md-12">
                                        <label for="inputDob4">{{ _lang('DOB') }} </label>
                                        <input name="dob" type="date" class="form-control"
                                            id="inputDob4" placeholder="{{ _lang('Date of Birth') }} " value="{{$user->dob}}">
                                        @error('dob')
                                            <span style="color: red;" class="error">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="form-row">
                                    <div class="form-group col-md-12">
                                        <label for="role_id">{{ _lang('User Type') }}</label>
                                        <select class="form-control" name="user_type">
                                                <option value="">Select Type</option>
                                                <option value="1" @if($user->user_type=='1') selected="selected" @endif >{{ _lang('Client') }}</option>
                                                <option value="2" @if($user->user_type=='2') selected="selected" @endif >{{ _lang('Driver') }}</option>
                                        </select>
                                        @error('user_type')
                                            <span style="color: red;" class="error">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <div class="col-lg-3 col-12"><img src="{{ asset($user->avator) }}" alt="Uploaded Image"></div>
                                    <div class="col-lg-9 col-12">
                                        <input type="file" class="form-control"  name="avator">
                                    </div>
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