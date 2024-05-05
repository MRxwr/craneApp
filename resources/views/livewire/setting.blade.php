
<div>
<form action="{{ route('settings.update', $row->id) }}" method="POST" enctype="multipart/form-data">
    @csrf
    <div class="card">
        <div class="card-body"> 
            <div class="row">
                <div class="col-lg-6 col-6"> 
                    @foreach (getActiveLanguages()  as $lang)
                        <div class="form-group row">
                            <div class="col-lg-3 col-12"> {{_lang('Site Title')}} [{{$lang->code}}]</div>
                            <div class="col-lg-9 col-12">
                                <input type="text" class="form-control" placeholder="Site Title.." name="sitetitle[{{ $lang->code }}]" value="{{$row->sitetitle[$lang->code]}}">
                            </div>
                        </div>
                        @endforeach
                        @foreach (getActiveLanguages()  as $lang)
                        <div class="form-group row">
                            <div class="col-lg-3 col-12">{{_lang('Site description')}} [{{$lang->code}}]</div>
                            <div class="col-lg-9 col-12">
                                <input type="text" class="form-control" placeholder="Site description.." name="sitedesc[{{ $lang->code }}]" value="{{$row->sitedesc[$lang->code]}}">
                            </div>
                        </div>
                        @endforeach
                        <div class="form-group row">
                            <div class="col-lg-3 col-12"> {{_lang('Site Logo')}}</div>
                            <div class="col-lg-9 col-12">
                                <input type="file" class="form-control"  name="logo">
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-lg-3 col-12">{{_lang('Site Favicon')}} </div>
                            <div class="col-lg-9 col-12">
                                <input type="file" class="form-control"  name="favicon">
                            </div>
                        </div>

                    </div>

                    <div class="col-lg-4 col-6">
                            <div class="form-group row">
                                <div class="col-lg-3 col-12">{{_lang('Admin language')}}</div>
                                <div class="col-lg-9 col-12">
                                   <select class="form-control"  name="adminlang">
                                    @foreach ($languages as $lang)
                                        <option value="{{ $lang->code }}">{{ $lang->title }}</option>
                                    @endforeach
                                   </select>
                               </div>
                            </div>
                            <div class="form-group row">
                                <div class="col-lg-3 col-12">{{_lang('Front language')}}</div>
                                <div class="col-lg-9 col-12">
                                   <select  class="form-control" name="frontlang" >
                                   @foreach (getActiveLanguages() as $lang)
                                        <option value="{{ $lang->code }}">{{ $lang->title }}</option>
                                    @endforeach
                                  </select>
                               </div>
                            </div>
                        <div class="form-group row">
                            <div class="col-lg-3 col-12"> {{_lang('Contact Number')}}</div>
                            <div class="col-lg-9 col-12">
                                <input type="text" class="form-control" placeholder="Contact " name="contact" value="{{$row->contact}}">
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-lg-3 col-12">{{_lang('Site email')}} </div>
                            <div class="col-lg-9 col-12">
                            <input type="text" class="form-control" placeholder="email" name="email" value="{{$row->email}}">
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-lg-3 col-12"> {{_lang('Address')}}</div>
                            <div class="col-lg-9 col-12">
                             <input type="text" class="form-control" placeholder="Address" name="address" value="{{$row->address}}">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-footer">
            <button type="submit" class="btn btn-primary">{{_lang('Submit')}}</button>
            <img src="{{ asset('loading-bar.gif') }}" alt="" wire:loading wire:target="update">
        </div>
    </form>   
</div>
