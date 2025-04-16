<div class="table-responsive">
    <table class="table table-hover mb-0 basic-datatable table-striped">
        <thead>
            <tr>
                <th>{{ __('no') }}</th>
                <th>{{ __('Procedure Name') }}</th>
                <th>{{ __('Description') }}</th>
                <th>{{ __('Coding') }}</th>
                <th>{{ __('Required') }}</th>
                <th>{{ __('Status') }}</th>
                <th class="text-end">{{ __('Action') }}</th>
            </tr>
        </thead>
        <tbody>
            @forelse($procedures as $procedure)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $procedure->procedure->procedure_name }}</td>
                    <td>{{ $procedure->procedure->description }}</td>
                    <td>{{ getCompanySymbol() }}{{ $procedure->procedure_coding }}</td>
                    <td>
                        @if($procedure->is_optional == 1)
                            <span class="badge bg-danger">{{ __('No') }}</span>
                        @else
                            <span class="badge bg-success">{{ __('Yes') }}</span>
                        @endif
                    </td>
                    <td>
                        @if($procedure->status == 1)
                            <span class="badge bg-success">{{ __('Published') }}</span>
                        @else
                            <span class="badge bg-danger">{{ __('Unpublished') }}</span>
                        @endif
                    </td>
                    <td>
                        <div class="d-flex justify-content-end gap-2">
                            {!! Form::open(['method' => 'DELETE', 'route' => ['iso_dic.iso_systems.procedure.delete', \Illuminate\Support\Facades\Crypt::encrypt($procedure->id)], 'class' => 'd-inline']) !!}
                            <a href="{{ route('iso_dic.iso_systems.procedure.download', \Illuminate\Support\Facades\Crypt::encrypt($procedure->id)) }}"
                                class="btn btn-sm btn-icon btn-light-secondary"
                                data-bs-toggle="tooltip"
                                title="{{ __('Download') }}">
                                <i class="ti ti-download"></i>
                            </a>
                            <a href="{{ route('iso_dic.iso_systems.procedure.preview', \Illuminate\Support\Facades\Crypt::encrypt($procedure->id)) }}"
                                class="btn btn-sm btn-icon btn-light-info"
                                data-bs-toggle="tooltip"
                                target="_blank"
                                title="{{ __('Preview') }}">
                                <i class="ti ti-eye"></i>
                            </a>
                            <button type="button"
                                class="btn btn-sm btn-icon btn-light-danger confirm_dialog"
                                data-bs-toggle="tooltip"
                                title="{{ __('Delete') }}">
                                <i class="ti ti-trash"></i>
                            </button>
                            {!! Form::close() !!}
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="text-center py-4">
                        <div class="d-flex flex-column align-items-center">
                            <i class="ti ti-file-off text-secondary mb-2" style="font-size: 24px;"></i>
                            <p class="text-body-secondary mb-0">{{ __('No procedures found') }}</p>
                        </div>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

