@extends('layouts.admin-app')
@section('page-title')
    {{ __('ISO Systems') }}
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item">
        <a href="{{ route('home') }}">{{ __('Dashboard') }}</a>
    </li>
    <li class="breadcrumb-item" aria-current="page">{{ __('Procedures') }}</li>
@endsection

@section('content')
<div class="row">
    <div class="col-sm-12">
        <div class="card">
            <div class="card-header">
                <div class="row align-items-center g-3">
                    <div class="col">
                        <h4 class="mb-0">{{ __('Procedures') }}</h4>
                    </div>
                    <div class="col-auto">
                        <a href="{{ route('iso_dic.procedures.create') }}" class="btn btn-primary">
                            <i class="ti ti-plus me-1"></i>
                            {{ __('Create Procedure') }}
                        </a>
                    </div>
                </div>
            </div>
            
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover table-striped">
                        <thead class="thead-light">
                            <tr>
                                <th>{{ __('Procedure Name') }}</th>
                                <th>{{ __('Description') }}</th>
                                <th class="w-15">{{ __('Attachments') }}</th>
                                <th class="w-10">{{ __('Status') }}</th>
                                <th class="w-15 text-end">{{ __('Action') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($procedures as $procedure)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div>
                                                <h6 class="mb-0">{{ $procedure->procedure_name_ar }}</h6>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="text-body-secondary">{{ $procedure->description_ar }}</span>
                                    </td>
                                    <td>
                                        @if($procedure->attachments->count() > 0)
                                            <div class="d-flex flex-column gap-1">
                                                @foreach ($procedure->attachments as $attachment)
                                                    <div class="d-inline-flex align-items-center">
                                                        <a href="{{ route('iso_dic.procedures.attachments.download', $attachment->id) }}"
                                                            class="btn btn-sm btn-icon btn-light-secondary me-1">
                                                            <i class="ti ti-download"></i>
                                                        </a>
                                                        <small class="text-truncate" style="max-width: 150px;" title="{{ $attachment->original_name }}">
                                                            {{ $attachment->original_name }}
                                                        </small>
                                                    </div>
                                                @endforeach
                                            </div>
                                        @else
                                            <span class="text-body-secondary">{{ __('No attachments') }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if ($procedure->status == 1)
                                            <span class="badge bg-success-subtle text-success">{{ __('publish') }}</span>
                                        @else
                                            <span class="badge bg-danger-subtle text-danger">{{ __('unpublish') }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="d-flex justify-content-end gap-2">
                                            {!! Form::open(['method' => 'DELETE', 'route' => ['iso_dic.procedures.destroy', $procedure->id], 'class' => 'd-inline']) !!}
                                            
                                            <a href="{{ route('iso_dic.procedures.configure', $procedure->id) }}"
                                                class="btn btn-sm btn-icon btn-light-primary"
                                                data-bs-toggle="tooltip"
                                                data-bs-placement="top"
                                                title="{{ __('Configure') }}">
                                                <i class="ti ti-settings"></i>
                                            </a>

                                            <a href="{{ route('iso_dic.procedures.edit', $procedure->id) }}"
                                                class="btn btn-sm btn-icon btn-light-info"
                                                data-bs-toggle="tooltip"
                                                data-bs-placement="top"
                                                title="{{ __('Edit') }}">
                                                <i class="ti ti-edit"></i>
                                            </a>

                                            <button type="button"
                                                class="btn btn-sm btn-icon btn-light-danger confirm_dialog"
                                                data-bs-toggle="tooltip"
                                                data-bs-placement="top"
                                                title="{{ __('Delete') }}">
                                                <i class="ti ti-trash"></i>
                                            </button>

                                            <a href="{{ route('iso_dic.procedures.show', \Illuminate\Support\Facades\Crypt::encrypt($procedure->id)) }}"
                                                class="btn btn-sm btn-icon btn-light-warning"
                                                data-bs-toggle="tooltip"
                                                data-bs-placement="top"
                                                title="{{ __('Preview') }}">
                                                <i class="ti ti-eye"></i>
                                            </a>
                                            {!! Form::close() !!}
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center py-4">
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

                <div class="d-flex justify-content-end mt-4">
                    {{ $procedures->links('pagination::bootstrap-5') }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
