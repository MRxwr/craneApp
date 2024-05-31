<div>

    <div class="card">
        <div class="card-header">
            
            @if (akses('create-page'))
                <div class="buttons float-right">
                        <a wire:click.prevent="tambah_data" href="#" class="btn btn-icon icon-left btn-primary"><i
                            class="bi bi-clipboard-plus"></i>
                        {{_lang('Add new')}}</a>
                </div>
            @endif
        </div>
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

            <div class="table-responsive">
                <table class="table table-bordered table-md">
                    <tbody>
                        <tr>
                            <th> {{_lang('ID')}}</th>
                            
                            <th>{{_lang('Title')}}</th>
                            <th>{{_lang('Description')}}</th>
                            <th>{{_lang('Status')}}</th>
                            <th>{{_lang('Action')}}</th>
                        </tr>
                        @foreach ($data as $e => $dt)
                            <tr>
                                <td>{{ $dt->id }}</td>
                                
                                <td>{{ $dt->title}}</td>
                                <td>{{ $dt->text }}</td>
                                
                                <td>
                                    @if (akses('edit-page'))
                                        @if ($dt->is_active == 1)
                                            <div style="cursor: pointer;"
                                                wire:click.prevent="update_status({{ $dt->id }})"
                                                class="badge badge-success">Active</div>
                                        @else
                                            <div style="cursor: pointer;"
                                                wire:click.prevent="update_status({{ $dt->id }})"
                                                class="badge badge-danger">Not Active</div>
                                        @endif
                                    @endif

                                    <img wire:loading wire:target="update_status" src="{{ asset('loading-bar.gif') }}"
                                        alt="">
                                </td>
                                <td>
                                    {{-- @if ($dt->is_paten != 1) --}}
                                    <div class="dropdown d-inline">
                                        <button class="btn btn-primary dropdown-toggle" type="button"
                                            data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                            {{_lang('Action')}}
                                        </button>
                                        <div class="dropdown-menu" x-placement="bottom-start"
                                            style="position: absolute; transform: translate3d(0px, 28px, 0px); top: 0px; left: 0px; will-change: transform;">
                                            @if (akses('edit-page'))
                                                <!-- <a class="dropdown-item has-icon" href="#"
                                                    wire:click.prevent="edit_data({{ $dt->id }})"><i
                                                        class="bi bi-pencil-square"></i>
                                                        {{_lang('Edit')}}</a> -->

                                            @endif
                                            @if (akses('delete-page'))
                                                <a class="dropdown-item has-icon"
                                                    onclick="return confirm('Confirm delete?') || event.stopImmediatePropagation()"
                                                    href="#" wire:click.prevent="destroy({{ $dt->id }})"><i
                                                        class="bi bi-trash3"></i>
                                                        {{_lang('Delete')}}</a>
                                            @endif
                                        </div>
                                    </div>
                                    {{-- @endif --}}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            {{ $data->links() }}
        </div>
    </div>

    <div class="modal fade" tabindex="-1" role="dialog" id="modalAdd" wire:ignore.self>
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{_lang('Add Notice')}}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">

                    <div class="card">
                        <div class="card-header">
                            {{-- <h4>Horizontal Form</h4> --}}
                        </div>
                        <form wire:submit.prevent="store">
                            <div class="card-body">
                                {{ $message ?? '' }}
                                
                                <div class="form-group row">
                                    <div class="col-lg-3 col-12"> {{_lang('Title')}} </div>
                                    <div class="col-lg-9 col-12">
                                        <input type="text" class="form-control" placeholder="Site Title.." wire:model="forms.title">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <div class="col-lg-3 col-12">{{_lang('text')}} </div>
                                    <div class="col-lg-9 col-12">
                                        <textarea class="form-control" placeholder="message.." wire:model="forms.text"></textarea> 
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

    @section('scripts')
        <script>
            Livewire.on('modalAdd', aksi => {

                if (aksi == 'show') {
                    $('#modalAdd').modal('show');
                } else {
                    // alert(aksi);
                    $('#modalAdd').modal('hide');
                    // $('#modalAdd').hide();
                    // $('#modalAdd').find('.close').click();
                }

            })
            window.livewire.on('fileChoosen',()=>{
                
                let inputFIeld= document.getElementById('image');
                let file = inputFIeld.files[0];
                let reader = new FileReader();
                reader.onloadend=()=>{
                    window.liveware.emit('fileUpload',reader.result);
                }
                reader.readAsDataURL(file);
                
            })
        </script>
    @endsection

</div>
