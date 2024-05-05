@extends('layouts.main',[
'title'=>_lang('Add Faq')
])
@section('content')
<div>
    <div class="card">
        <div class="card-body row">

            <div class=" col-md-6 modal-content">
            <form action="{{ route('services.store') }}" method="POST" enctype="multipart/form-data">
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
                                @foreach (getActiveLanguages()  as $lang)
                                    <div class="form-group row">
                                        <div class="col-lg-3 col-12">{{_lang('Description')}} [{{$lang->code}}]</div>
                                        <div class="col-lg-9 col-12">
                                            <input type="text" class="form-control summernote" placeholder="Description.." name="description[{{ $lang->code }}]">
                                        </div>
                                    </div>
                                @endforeach
                                <div class="form-group row">
                                    <div class="col-lg-3 col-12"><img src="{{ asset('storage/' . '') }}" alt="Uploaded Image"></div>
                                    <div class="col-lg-9 col-12">
                                    
                                        <input type="file" class="form-control"  wire:model="forms.image">
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