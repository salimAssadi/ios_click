@extends('tenant::layouts.app')

@section('page-title', __('Main Procedures'))
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('tenant.document.index') }}">{{ __('Documents') }}</a></li>
    <li class="breadcrumb-item active">{{ __('Main Procedures') }}</li>
@endsection

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">{{ __('Main Procedures') }}</h5>
                    <div>
                        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#originalModal">{{ __('Show Original') }}</button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <x-document::documents-table 
                            documentType="procedure" 
                            title="{{ __('Procedures List') }}" 
                            relatedProcess="Modules\\Document\\Entities\\IsoSystemProcedure" 
                            :categoryId="1"
                            :customColumns="$customColumns"
                        />
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="originalModal" tabindex="-1" aria-labelledby="originalModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="originalModalLabel">{{ __('Original Procedures') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body overflow-scroll" style="max-height: 80vh;">
                    <div class="table-responsive">
                        <table class="table table-hover table-striped basic-datatable">
                            <thead class="thead-light">
                                <tr>
                                    <th>{{ __('N') }}</th>
                                    <th>{{ __('Procedure Name') }}</th>
                    <th>{{ __('Description') }}</th>
                    <th class="w-10">{{ __('Status') }}</th>
                    <th class="w-15 text-end">{{ __('Action') }}</th>
                </tr>
            </thead>
            <tbody>

                @forelse ($orginal_procedures as $procedure)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>
                            <div class="d-flex align-items-center">
                                <div>
                                    <h6 class="mb-0">{{ $procedure->procedure_name_ar }}</h6>
                                </div>
                            </div>
                        </td>
                        <td>
                            <span
                                class="text-body-secondary">{{ $procedure->description_ar }}</span>
                        </td>
                        <td>
                            @if ($procedure->status == 1)
                                <span class="badge bg-success">{{ __('publish') }}</span>
                            @else
                                <span class="badge bg-danger">{{ __('unpublish') }}</span>
                            @endif
                        </td>
                        <td>
                            <div class="d-flex justify-content-end gap-2">
                                <a href="{{ route('tenant.document.procedures.configure', $procedure->id) }}"
                                    class="btn btn-sm btn-icon btn-light-primary"
                                    data-bs-toggle="tooltip" data-bs-placement="top"
                                    title="{{ __('Configure') }}">
                                    <i class="ti ti-settings"></i>
                                </a>

                              
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center py-4">
                            <div class="d-flex flex-column align-items-center">
                                <i class="ti ti-file-off text-secondary mb-2"
                                    style="font-size: 24px;"></i>
                                <p class="text-body-secondary mb-0">
                                    {{ __('No procedures found') }}
                                </p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
