{{-- @extends('tenant::layouts.app')

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
                   
                </div>
                <div class="card-body">
                    
                </div>
            </div>
        </div>
    </div>
@endsection --}}


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
                </div>
                <div class="card-body">
                    <!-- Tabs -->
                    <ul class="nav nav-tabs" id="procedureTabs" role="tablist">

                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="used-tab" data-bs-toggle="tab" data-bs-target="#used"
                                type="button" role="tab" aria-controls="used" aria-selected="true">
                                {{ __('Used Copy') }}
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link " id="original-tab" data-bs-toggle="tab" data-bs-target="#original"
                                type="button" role="tab" aria-controls="original" aria-selected="false">
                                {{ __('Original Copy') }}
                            </button>
                        </li>
                    </ul>

                    <div class="tab-content pt-3" id="procedureTabsContent">
                        <!-- Used Copy Tab -->
                        <div class="tab-pane fade show active" id="used" role="tabpanel" aria-labelledby="used-tab">
                            <div class="table-responsive">
                                <table class="table table-hover table-striped basic-datatable">
                                    <thead class="thead-light">
                                        <tr>
                                            <th>{{ __('N') }}</th>
                                            <th>{{ __('Procedure Name') }}</th>
                                            <th>{{ __('Description') }}</th>
                                            {{-- <th class="w-10">{{ __('Status') }}</th> --}}
                                            <th class="w-15 text-end">{{ __('Action') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($used_procedures as $procedure)
                                            <tr>
                                                <td>{{ $loop->iteration }}</td>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <div>
                                                            <h6 class="mb-0">{{ $procedure->procedure->procedure_name }}</h6>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <span
                                                        class="text-body-secondary">{{ $procedure->procedure->description }}</span>
                                                </td>
                                                {{-- <td>
                                                    @if ($procedure->status == 1)
                                                        <span class="badge bg-success">{{ __('publish') }}</span>
                                                    @else
                                                        <span class="badge bg-danger">{{ __('unpublish') }}</span>
                                                    @endif
                                                </td> --}}
                                                <td>
                                                    <div class="d-flex justify-content-end gap-2">
                                                        <a href="{{ route('tenant.document.procedures.configure', $procedure->id) }}"
                                                            class="btn btn-sm btn-icon btn-light-primary"
                                                            data-bs-toggle="tooltip" data-bs-placement="top"
                                                            title="{{ __('Configure') }}">
                                                            <i class="ti ti-settings"></i>
                                                        </a>

                                                        <a href="" class="btn btn-sm btn-icon btn-light-info"
                                                            data-bs-toggle="tooltip" data-bs-placement="top"
                                                            title="{{ __('Edit') }}">
                                                            <i class="ti ti-edit"></i>
                                                        </a>

                                                        <a href="" class="btn btn-sm btn-icon btn-light-warning"
                                                            data-bs-toggle="tooltip" data-bs-placement="top"
                                                            title="{{ __('Preview') }}">
                                                            <i class="ti ti-eye"></i>
                                                        </a>
                                                    </div>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="4" class="text-center py-4">
                                                    <div class="d-flex flex-column align-items-center">
                                                        <i class="ti ti-file-off text-secondary mb-2"
                                                            style="font-size: 24px;"></i>
                                                        <p class="text-body-secondary mb-0">{{ __('No procedures found') }}
                                                        </p>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>


                        </div>
                        <!-- Original Copy Tab -->
                        <div class="tab-pane fade" id="original" role="tabpanel" aria-labelledby="original-tab">
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
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
