<div>
    <div class="card">
        <div class="card-header">
            {{-- <h4>Simple Table</h4> --}}
            @if (akses('create-user'))
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
                            <th>{{_lang('ID')}}</th>
                            <th>{{_lang('Role')}}</th>
                            <th>{{_lang('Name')}}</th>
                            <th>{{_lang('Email')}}</th>
                            <th>{{_lang('Status')}}</th>
                            <th>{{_lang('Action')}}</th>
                        </tr>
                        @foreach ($data as $e => $dt)
                            <tr>
                                <td>{{ $dt->id }}</td>
                                <td>{{ $dt->role->name }}</td>
                                <td>{{ $dt->name }}</td>
                                <td>{{ $dt->email }}</td>
                                <td>
                                    @if (akses('edit-user'))
                                        @if ($dt->is_active == 1)
                                            <div style="cursor: pointer;"
                                                wire:click.prevent="update_status({{ $dt->id }})"
                                                class="badge badge-success">{{_lang('Active')}}</div>
                                        @else
                                            <div style="cursor: pointer;"
                                                wire:click.prevent="update_status({{ $dt->id }})"
                                                class="badge badge-danger">{{_lang('Not Active')}}</div>
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
                                            @if (akses('edit-user'))
                                                <a class="dropdown-item has-icon" href="#"
                                                    wire:click.prevent="edit_data({{ $dt->id }})"><i
                                                        class="bi bi-pencil-square"></i>
                                                        {{_lang('Edit')}}</a>
                                            @endif
                                            @if (akses('edit-user'))
                                                <a class="dropdown-item has-icon" href="#"
                                                    wire:click.prevent="change_password({{ $dt->id }})"><i
                                                        class="bi bi-lock"></i>
                                                        {{_lang('Change Password')}}</a>
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
                    <h5 class="modal-title">Add User</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">

                    <div class="card">
                        <div class="card-header">
                            {{-- <h4>Horizontal Form</h4> --}}
                            <p style="color: red">
                                <b><i>** Password default: 12345678</i></b>
                            </p>
                        </div>
                        <form wire:submit.prevent="store">
                            <div class="card-body">
                                {{ $message ?? '' }}
                                <div class="form-group">
                                    <label for="name"> {{_lang('Name')}}</label>
                                    <input wire:model="forms.name" type="text" class="form-control" id="name"
                                        placeholder="Name">
                                    {{-- {{ $forms['name'] }} --}}
                                    @error('forms.name')
                                        <span style="color: red;" class="error">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="form-row">
                                    <div class="form-group col-md-12">
                                        <label for="inputEmail4"> {{_lang('Email')}}</label>
                                        <input wire:model="forms.email" type="text" class="form-control"
                                            id="inputEmail4" placeholder="Email">
                                        @error('forms.email')
                                            <span style="color: red;" class="error">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="form-row">
                                    <div class="form-group col-md-12">
                                        <label for="role_id"> {{_lang('Role')}}</label>
                                        <select class="form-control" wire:model="forms.role_id">
                                            <option value="">Select Role</option>
                                            @foreach ($roles as $rl)
                                                <option value="{{ $rl->id }}">{{ $rl->name }}</option>
                                            @endforeach
                                        </select>
                                        @error('forms.role_id')
                                            <span style="color: red;" class="error">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer">
                                <button type="submit" class="btn btn-primary"> {{_lang('Submit')}}</button>
                                <img src="{{ asset('loading-bar.gif') }}" alt="" wire:loading wire:target="store">
                            </div>
                        </form>
                    </div>

                </div>
                
            </div>
        </div>
    </div>

    
    <div class="modal fade" tabindex="-1" role="dialog" id="modalChnagePassword" wire:ignore.self>
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Chnage Password</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">

                    <div class="card">
                        <div class="card-header">
                            {{-- <h4>Horizontal Form</h4> --}}
                            <p style="color: red">
                                <b><i>** Password default: 12345678</i></b>
                            </p>
                        </div>
                        <form wire:submit.prevent="update_password">
                            <div class="card-body">
                                {{ $message ?? '' }}
                                <div class="form-group">
                                    <label for="name"> {{_lang('New Password')}}</label>
                                    <input wire:model="password" name="password" type="password" class="form-control" id="password"
                                        placeholder="Password">
                                    {{-- {{ $forms['name'] }} --}}
                                    @error('forms.password')
                                        <span style="color: red;" class="error">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="card-footer">
                                <button type="submit" class="btn btn-primary"> {{_lang('Submit')}}</button>
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
            Livewire.on('modalChnagePassword', aksi => {
                if (aksi == 'show') {
                    $('#modalChnagePassword').modal('show');
                } else {
                  
                    $('#modalChnagePassword').modal('hide');
                    
                }

                })
        </script>
    @endsection

</div>
