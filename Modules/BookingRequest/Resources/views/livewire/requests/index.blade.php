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
                                                    class="bi bi-eye"></i>
                                                    {{_lang('Booking View')}}</a>
                                            @endif
                                            @if (akses('view-request'))
                                            <a class="dropdown-item has-icon" href="#"
                                                wire:click.prevent="logs_data({{ $dt->id }})"><i
                                                    class="bi bi-list"></i>
                                                    {{_lang('Booking Logs')}}</a>
                                            @endif
                                            @if (akses('view-request'))
                                            <a class="dropdown-item has-icon" href="#"
                                                wire:click.prevent="prices_data({{ $dt->id }})"><i
                                                    class="bi bi-list"></i>
                                                    {{_lang('Booking Prices')}}</a>
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
                    <h5 class="modal-title">{{ _lang('Booking Request') }}  #{{$forms['request_id']}}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    @if($forms['logs'])
                        <div class="form-group col-md-12 table-responsive">
                        <table class="table table-bordered table-md">
                                @foreach($forms['logs'] as $log)
                                    <tr> 
                                        <td>{{$log['driver']}}</td>
                                        <td>{{$log['client']}}</td>
                                        <td>{{$log['activity']}}</td>
                                        
                                    </tr>
                                @endforeach
                            </table>
                        </div>
                    @endif
                </div>
                
            </div>
        </div>
    </div>
    <div class="modal fade" tabindex="-1" role="dialog" id="modalLogs" wire:ignore.self>
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Booking Logs</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    @if($forms['logs'])
                        <div class="form-group col-md-12 table-responsive">
                        <table class="table table-bordered table-md">
                                @foreach($forms['logs'] as $log)
                                    <tr> 
                                        <td>{{$log['driver']}}</td>
                                        <td>{{$log['client']}}</td>
                                        <td>{{$log['activity']}}</td>
                                        
                                    </tr>
                                @endforeach
                            </table>
                        </div>
                    @endif
                </div>
                
            </div>
        </div>
    </div>

    <div class="modal fade" tabindex="-1" role="dialog" id="modalPrice" wire:ignore.self>
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{ _lang('Booking Price with driver') }}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    {{ $message ?? '' }}
                        @if($forms['prices'])
                        <div class="form-group col-md-12 table-responsive">
                        <table class="table table-bordered table-md">
                                @foreach($forms['prices'] as $price)
                                    <tr> 
                                        <td>{{$price['driver']}}</td>
                                        <td>{{$price['mobile']}}</td>
                                        <td>{{$price['price']}}KD</td>
                                        <td>{{($price['is_accepted']?'Yes':'No')}}</td>
                                    </tr>
                                @endforeach
                            </table>
                        </div>
                    @endif
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
                }

            })
            Livewire.on('modalPrice', aksi => {
                if (aksi == 'show') {
                    $('#modalPrice').modal('show');
                } else {
                    $('#modalPrice').modal('hide');
                }
            })
            Livewire.on('modalLogs', aksi => {
                if (aksi == 'show') {
                    $('#modalLogs').modal('show');
                } else {
                    $('#modalLogs').modal('hide');
                }
            })
        </script>
    @endsection

</div>
