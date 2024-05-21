@extends('layouts.main',[
'title'=>_lang('Add Banner')
])
@section('content')
<div>
    <div class="card">
        <div class="card-body row">

            <div class=" col-md-6 modal-content">
            <form action="{{ route('coupons.store') }}" method="POST" enctype="multipart/form-data">
                 @csrf <!-- CSRF protection -->
                            <div class="card-body">
                                {{ $message ?? '' }}
                                @foreach (getActiveLanguages()  as $lang)
                                <div class="form-group row">
                                    <div class="col-lg-3 col-12"> {{_lang('Title')}} [{{$lang->code}}]</div>
                                    <div class="col-lg-9 col-12">
                                        <input type="text" class="form-control" placeholder="Site Title.." name="title[{{ $lang->code }}]">
                                    </div>
                                </div>
                                @endforeach
                              
                                <div class="form-group row">
                                    <div class="col-lg-3 col-12">{{_lang('Coupon Code')}}</div>
                                    <div class="col-lg-9 col-12">
                                        <input type="text" class="form-control"  name="coupon_code">
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <div class="col-lg-3 col-12">{{_lang('Coupon Value')}}</div>
                                    <div class="col-lg-9 col-12">
                                        <input type="text" class="form-control"  name="coupon_value">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <div class="col-lg-3 col-12">{{_lang('Coupon Type')}}</div>
                                    <div class="col-lg-9 col-12">
                                        <select class="form-control"  name="coupon_value">
                                          <option value="1">{{_lang('Fixed')}}</option>
                                          <option value="2">{{_lang('Percentage')}}</option>
                                        <select>
                                    </div>
                                </div>
                                
                                <div class="form-group row">
                                    <div class="col-lg-3 col-12">{{_lang('Coupon expiry date')}}</div>
                                    <div class="col-lg-9 col-12">
                                        <input type="date" class="form-control"  name="expiry_date">
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