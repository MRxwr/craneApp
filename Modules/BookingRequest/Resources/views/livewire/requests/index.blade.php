<div>

    <div class="card">
        <div class="card-header">
            {{-- <h4>Simple Table</h4> --}}
            
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
                            <th>ID</th>
                            <th>{{_lang('Request ID')}}</th>
                            <th>{{_lang('Name')}}</th>
                            <th>{{_lang('Mobile')}}</th>
                            <th>{{_lang('Distance')}}</th>
                            <th>{{_lang('From')}}</th>
                            <th>{{_lang('To')}}</th>
                            <th>{{_lang('Status')}}</th>
                            <th>{{_lang('Action')}}  </th>
                        </tr>
                        @foreach ($data as $e => $dt)
                            <tr>
                                <td>{{ $dt->id }}</td>
                                <td>{{ $dt->request_id }}</td>
                                <td>{{ $dt->client->name }}</td>
                                <td>{{ $dt->client->mobile }}</td>
                                <td>{{ $dt->distances }}KM</td>
                                <td>{{ $dt->from_location }}</td>
                                <td>{{ $dt->to_location }}</td>
                                <td>
                                    @if (akses('edit-user'))
                                        @if ($dt->is_active == 1)
                                            <div style="cursor: pointer;"
                                                wire:click.prevent="update_status({{ $dt->id }})"
                                                class="badge badge-success">{{_lang('Active')}} </div>
                                        @else
                                            <div style="cursor: pointer;"
                                                wire:click.prevent="update_status({{ $dt->id }})"
                                                class="badge badge-danger">{{_lang('Not Active')}}
                                            </div>
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
                                            Action
                                        </button>
                                        <div class="dropdown-menu" x-placement="bottom-start"
                                            style="position: absolute; transform: translate3d(0px, 28px, 0px); top: 0px; left: 0px; will-change: transform;">
                                            @if (akses('view-request'))
                                            <a class="dropdown-item has-icon" href="#"
                                                wire:click.prevent="edit_data({{ $dt->id }})"><i
                                                    class="bi bi-pencil-square"></i>
                                                Show</a>
                                        @endif
                                            @if (akses('delete-user'))
                                                <a class="dropdown-item has-icon"
                                                    onclick="return confirm('Confirm delete?') || event.stopImmediatePropagation()"
                                                    href="#" wire:click.prevent="destroy({{ $dt->id }})"><i
                                                        class="bi bi-trash3"></i>
                                                    Delete</a>
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
                            
                        </div>
                        <form wire:submit.prevent="store">
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
                                    <img src="{{ asset('storage/' . $avator) }}" alt="Uploaded avator">
                                        <input type="file" class="form-control"  wire:model="forms.avator">
                                    </div>
                                </div>

                            </div>
                            <div class="card-footer">
                                <button type="submit" class="btn btn-primary">Submit</button>
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
