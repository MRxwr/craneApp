<div>

    <div class="card">
        <div class="card-header">
            {{-- <h4>Simple Table</h4> --}}
            @if (akses('create-user'))
                <div class="buttons float-right">
                    <a wire:click.prevent="tambah_data" href="#" class="btn btn-icon icon-left btn-primary"><i
                            class="bi bi-clipboard-plus"></i>
                            {{_lang('Add New')}}</a>
                </div>
            @endif
        </div>
        <div class="card-body">

            <div class="row">
                <div class="col-md-1">
                    <div class="form-group">
                       
                        <select class="form-control" wire:model="paging">
                            @foreach ($pagings as $e => $pg)
                                <option value="{{ $e }}">{{ $pg }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                       
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
                            <th>ID</th>
                            
                            <th>{{_lang('Slug')}}</th>
                             @foreach (getActiveLanguages() as $rl)
                              <th>{{ $rl->title }} [{{$rl->code}}]</th>                            
                             @endforeach
                            
                            <th>{{_lang('Action')}}</th>
                        </tr>
                        @foreach ($data as $e => $dt)
                            <tr>
                                <td>{{ $dt->id }}</td>
                                <td>{{ $dt->slug }} </td>
                                  @foreach (getActiveLanguages() as $rl)
                                    <td>{{ $dt->locales[$rl->code] }}</td>                           
                                  @endforeach
                                <td>
                                    
                                    <div class="dropdown d-inline">
                                        <button class="btn btn-primary dropdown-toggle" type="button"
                                            data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                            {{_lang('Action')}}
                                        </button>
                                        <div class="dropdown-menu" x-placement="bottom-start"
                                            style="position: absolute; transform: translate3d(0px, 28px, 0px); top: 0px; left: 0px; will-change: transform;">
                                            @if (akses('edit-user'))
                                                <a class="dropdown-item has-icon" href="#"
                                                    wire:click.prevent="edit_data({{ $dt->id }})"><i
                                                        class="bi bi-pencil-square"></i>
                                                        {{_lang('Edit')}}</a>
                                            @endif

                                            @if (akses('delete-user'))
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
                    <h5 class="modal-title">{{_lang('Add New')}}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">

                    <div class="card">
                        
                        <form wire:submit.prevent="store">
                            <div class="card-body">
                                {{ $message ?? '' }}
                                    <div class="form-group">
                                        <label for="name">{{_lang('Slug')}}(English only)</label>
                                        <input wire:model="forms.slug" type="text" class="form-control" id="slug"
                                            placeholder="Slug in english">
                                     
                                        @error('forms.slug')
                                            <span style="color: red;" class="error">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    @foreach (getActiveLanguages() as $rl)
                                               
                                                <div class="form-row">
                                                    <div class="form-group col-md-12">
                                                        <label for="inputEmail{{ $rl->code }}">{{ $rl->title }}</label>
                                                        <input wire:model="forms.locales.{{ $rl->code }}" type="text" class="form-control"
                                                            id="inputEmail{{ $rl->code }}" placeholder="{{ $rl->title }}">
                                                        
                                                    </div>
                                                </div>
                                    @endforeach
                            </div>
                            <div class="card-footer">
                                <button type="submit" class="btn btn-primary">{{_lang('Submit')}}</button>
                                <img src="{{ asset('loading-bar.gif') }}" alt="" wire:loading wire:target="store">
                            </div>
                        </form>
                    </div>

                </div>
                {{-- <div class="modal-footer">
                    <button type="button" class="btn btn-primary">Save changes</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div> --}}
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
        </script>
    @endsection

</div>
