@extends('layouts.main',[
'title'=>'Dashboard'
])
@section('content')
<div>
    <div class="card">
        <div class="card-body">

            <div class="row">
                <div class="col-md-1">
                    <div class="form-group">
                        {{-- <label>Text</label> --}}
                        <select class="form-control" wire:model="paging">
                            @foreach ($pagings as $e => $pg)
                                <option value="{{ $e }}">{{ $pg }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        {{-- <label>Text</label> --}}
                        <input type="text" class="form-control" placeholder="Search Data.." wire:model="search">
                        {{-- {{ $search }} --}}
                    </div>
                </div>

            </div>
            <div class="row">
                <img src="{{ asset('loading-bar.gif') }}" alt="" wire:loading wire:target="paging,search">
            </div>
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{_lang('Add Service')}}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">

                    <div class="card">
                        <div class="card-header">
                            <!-- {{-- <h4>Horizontal Form</h4> --}} -->
                            
                        </div>
                        <form wire:submit.prevent="store">
                            <div class="card-body">
                                {{ $message ?? '' }}
                                @foreach (getActiveLanguages()  as $lang)
                                <div class="form-group row">
                                    <div class="col-lg-3 col-12"> {{_lang('Title')}} [{{$lang->code}}]</div>
                                    <div class="col-lg-9 col-12">
                                        <input type="text" class="form-control" placeholder="Site Title.." wire:model="forms.title.{{ $lang->code }}">
                                    </div>
                                </div>
                                @endforeach
                                @foreach (getActiveLanguages()  as $lang)
                                <div class="form-group row">
                                    <div class="col-lg-3 col-12">{{_lang('Description')}} [{{$lang->code}}]</div>
                                    <div class="col-lg-9 col-12">
                                        <input type="text" class="form-control" placeholder="Description.." wire:model="forms.description.{{ $lang->code }}">
                                    </div>
                                </div>
                                @endforeach

                                <div class="form-group row">
                                    <div class="col-lg-3 col-12">{{_lang('Image')}} </div>
                                    <div class="col-lg-9 col-12">
                                    <img src="{{ asset('storage/' . $image) }}" alt="Uploaded Image">
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
    </div>
</div>
@endsection
@section('scripts')
       
@endsection