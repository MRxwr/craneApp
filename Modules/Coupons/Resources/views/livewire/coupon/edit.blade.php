@extends('layouts.main',[
    'title'=>_lang('Edit Banner')
])
@section('content')
<div>
    <div class="card">
        <div class="card-body row">

            <div class=" col-md-6 modal-content">
            <form action="{{ route('banners.update', $service->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT') <!-- Use PUT method for updating -->
                            <div class="card-body">
                                {{ $message ?? '' }}
                                @foreach (getActiveLanguages()  as $lang)
                                <div class="form-group row">
                                    <div class="col-lg-3 col-12"> {{_lang('Title')}} [{{$lang->code}}]</div>
                                    <div class="col-lg-9 col-12">
                                        <input type="text" class="form-control" placeholder="Site Title.." value="{{$service->title[$lang->code]}}" name="title[{{ $lang->code }}]">
                                    </div>
                                </div>
                                @endforeach
                               

                                <div class="form-group row">
                                    <div class="col-lg-3 col-12">{{_lang('Coupon Code')}}</div>
                                    <div class="col-lg-9 col-12">
                                        <input type="text" class="form-control"  name="coupon_code" value="{{$service->coupon_code}}">
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <div class="col-lg-3 col-12">{{_lang('Coupon Value')}}</div>
                                    <div class="col-lg-9 col-12">
                                        <input type="text" class="form-control"  name="coupon_value" value="{{$service->coupon_value}}">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <div class="col-lg-3 col-12">{{_lang('Coupon Type')}}</div>
                                    <div class="col-lg-9 col-12">
                                        <select class="form-control"  name="coupon_type">
                                          <option value="1" @if($service->coupon_value=="1") selected="selected" @endif >{{_lang('Fixed')}}</option>
                                          <option value="2" @if($service->coupon_value=="2") selected="selected" @endif >{{_lang('Percentage')}}</option>
                                        <select>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <div class="col-lg-3 col-12">{{_lang('Coupon expiry date')}}</div>
                                    <div class="col-lg-9 col-12">
                                        <input type="date" class="form-control"  name="expiry_date" value="{{$service->expiry_date}}">
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