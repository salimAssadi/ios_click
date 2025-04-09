@extends('layouts.admin-app')
@section('page-title')
    {{ __('Document Details') }}
@endsection
<style>
    .nav-link {
        padding: 10px 15px;
        border: none;
        background-color: transparent;
        color: #555;
        /* Default color for inactive tabs */
        transition: background-color 0.3s, color 0.3s;
        cursor: pointer;
        border-radius: 5px;
        width: 100px;
    }

    .nav-link:hover {
        background-color: #f0f0f0;
        /* Light background on hover */
        color: #333;
        /* Darker text on hover */
    }

    .nav-link.active {
        background-color: #007bff !important;
        color: white;
        font-weight: bold;
    }
</style>
@section('breadcrumb')
    <ul class="breadcrumb mb-0">
        <li class="breadcrumb-item">
            <a href="{{ route('iso_dic.home') }}">{{ __('Dashboard') }}</a>
        </li>
        <li class="breadcrumb-item">
            <a href="#">{{ __('ISO Systems') }}</a>
        </li>
        <li class="breadcrumb-item active">
            <a href="#">{{ __('Detail') }}</a>
        </li>
    </ul>
@endsection
@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="email-body">
                            <div class="card">
                                <div class="card-header">

                                    <div class="row align-items-center g-2">
                                        <div class="col">
                                            <h5>
                                                {{ $pageTitle }}
                                            </h5>
                                        </div>
                                        <div class="col-auto">

                                            <a class="btn btn-secondary"
                                                href="{{ route('iso_dic.iso_systems.procedure.create', \Illuminate\Support\Facades\Crypt::encrypt($isoSystem->id)) }}">
                                                <i class="ti ti-circle-plus align-text-bottom"></i>
                                                {{ __('Create Procedure') }}
                                            </a>
                                            <a class="btn btn-secondary"
                                                href="{{ route('iso_dic.iso_systems.sample.create', \Illuminate\Support\Facades\Crypt::encrypt($isoSystem->id)) }}">
                                                <i class="ti ti-circle-plus align-text-bottom"></i>
                                                {{ __('Create Sample') }}
                                            </a>

                                        </div>
                                    </div>
                                </div>
                                <div class="card-body pt-4">
                                    <ul class="nav nav-tabs" id="myTab" role="tablist">

                                        {{-- purpose-tab --}}
                                        <li class="nav-item" role="presentation">
                                            <button class="nav-link active" id="procedure-tab" data-bs-toggle="tab"
                                                data-bs-target="#procedure" type="button" role="tab"
                                                aria-controls="purpose" aria-selected="true">
                                                {{ __('Procedures') }}
                                            </button>
                                        </li>

                                        {{-- scope-tab --}}
                                        <li class="nav-item" role="presentation">
                                            <button class="nav-link " id="sample-tab" data-bs-toggle="tab"
                                                data-bs-target="#sample" type="button" role="tab"
                                                aria-controls="sample" aria-selected="false">
                                                {{ __('Samples') }}
                                            </button>
                                        </li>
                                        {{-- specifcationItem-tab --}}
                                        <li class="nav-item" role="presentation" >
                                            <button class="nav-link w-100" id="specifcationItem-tab" data-bs-toggle="tab"
                                                data-bs-target="#specifcationItem" type="button" role="tab"
                                                aria-controls="specifcationItem" aria-selected="false">
                                                {{ __('ISO specification items') }}
                                            </button>
                                        </li>
                                        {{-- responsibility-tab --}}
                                        <li class="nav-item" role="presentation">
                                            <button class="nav-link " id="responsibility-tab" data-bs-toggle="tab"
                                                data-bs-target="#responsibility" type="button" role="tab"
                                                aria-controls="responsibility" aria-selected="false">
                                                {{ __('Attachments') }}
                                            </button>
                                        </li>



                                    </ul>
                                    <div class="tab-content mt-3" id="myTabContent">

                                        {{-- procedure --}}
                                        <div class="tab-pane fade show active" id="procedure" role="tabpanel"
                                            aria-labelledby="procedure-tab">

                                            <div class="dt-responsive table-responsive">
                                                <table class="table table-hover easy-datatable text-center">
                                                    <thead>
                                                        <tr>
                                                            <th class="text-center">{{ __('no') }}</th>
                                                            <th class="text-center">{{ __('Procedure Name') }}</th>
                                                            <th class="text-center">{{ __('Procedure Description') }}</th>
                                                            <th class="text-center">{{ __('Coding') }}</th>
                                                            <th class="text-center">{{ __('Required Procedure') }}</th>
                                                            <th class="text-center">{{ __('status') }}</th>
                                                            <th class="text-center">{{ __('Action') }}</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @forelse ($procedures as $procedure)
                                                            <tr>
                                                                <td>{{ $loop->iteration }}</td>
                                                                <td>
                                                                    <div class="flex-grow-1 ms-3">
                                                                        <h5 class="mb-1">
                                                                            {{ $procedure->procedure->procedure_name }}

                                                                        </h5>
                                                                    </div>
                                                                </td>



                                                                <td>
                                                                    <div class="flex-grow-1 ms-3">
                                                                        <h5 class="mb-1">
                                                                            {{ $procedure->procedure->description }}

                                                                        </h5>
                                                                    </div>
                                                                </td>

                                                                <td>
                                                                    <div class="flex-grow-1 ms-3">
                                                                        <h5 class="mb-1">
                                                                            {{ getCompanySymbol() }}{{ $procedure->procedure_coding }}

                                                                        </h5>
                                                                    </div>
                                                                </td>


                                                                <td>
                                                                    <div class="flex-grow-1 ms-3">
                                                                        @if ($procedure->is_optional == 1)
                                                                            <span
                                                                                class="d-inline badge  text-bg-danger px-4">{{ __('NO') }}</span>
                                                                        @else
                                                                            <span
                                                                                class="d-inline badge text-bg-success px-3">{{ __('Yes') }}</span>
                                                                        @endif
                                                                    </div>
                                                                </td>

                                                                <td>
                                                                    <div class="flex-grow-1 ms-3">
                                                                        @if ($procedure->status == 1)
                                                                            <span
                                                                                class="d-inline badge text-bg-success">{{ __('publish') }}</span>
                                                                        @else
                                                                            <span
                                                                                class="d-inline badge text-bg-danger">{{ __('unpublish') }}</span>
                                                                        @endif
                                                                    </div>
                                                                </td>
                                                                <td>
                                                                    <div class="cart-action align-items-center">
                                                                        {!! Form::open([
                                                                            'method' => 'DELETE',
                                                                            'route' => ['iso_dic.iso_systems.procedure.delete', \Illuminate\Support\Facades\Crypt::encrypt($procedure->id)],
                                                                        ]) !!}
                                                                        {{-- <a class="avtar avtar-xs btn-link-primary text-primary"
                                                                            data-bs-toggle="tooltip"
                                                                            data-bs-original-title="{{ __('Attachments') }}"
                                                                            href="{{ route('iso_dic.procedures.configure', $procedure->id) }}"
                                                                            data-title="{{ __('Edit User') }}">
                                                                            <i class="ti ti-settings fs-2"></i>
                                                                        </a>
                                                                        <a class="avtar avtar-xs btn-link-secondary text-secondary customModal"
                                                                            data-bs-toggle="tooltip" data-size="lg"
                                                                            data-bs-original-title="{{ __('Edit') }}"
                                                                            href="#"
                                                                            data-url="{{ route('iso_dic.procedures.edit', $procedure->id) }}"
                                                                            data-title="{{ __('Edit Procedure') }}">
                                                                            <i class="ti ti-edit fs-2"></i>
                                                                        </a> --}}
                                                                        <a class="avtar avtar-xs btn-link-danger text-danger confirm_dialog"
                                                                            data-bs-toggle="tooltip"
                                                                            data-bs-original-title="{{ __('Detete') }}"
                                                                            data-dialog-title="{{ __('Are you sure you want to delete this record ?') }}"
                                                                            data-dialog-text="{{ __('This record can not be restore after delete. Do you want to confirm?') }}"
                                                                            href="#"><i
                                                                                class="ti ti-trash fs-2"></i>
                                                                        </a>
                                                                        <a class="avtar avtar-xs btn-link-secondary text-secondary"
                                                                            data-bs-toggle="tooltip" data-size="lg"
                                                                            data-bs-original-title="{{ __('Download') }}"
                                                                            href="{{ route('iso_dic.iso_systems.procedure.download', \Illuminate\Support\Facades\Crypt::encrypt($procedure->id)) }}"
                                                                            data-title="{{ __('Download') }}">
                                                                            <i data-feather="download"> </i>
                                                                        </a>
                                                                        <a class="avtar avtar-xs btn-link-warning text-warning"
                                                                            data-bs-toggle="tooltip"
                                                                            data-bs-original-title="{{ __('preview') }}"
                                                                            target="blank"
                                                                            href="{{ route('iso_dic.iso_systems.procedure.preview', \Illuminate\Support\Facades\Crypt::encrypt($procedure->id)) }}"
                                                                            data-title="{{ __('preview') }}">
                                                                            <i class="ti ti-eye fs-2"></i>
                                                                        </a>
                                                                        {!! Form::close() !!}
                                                                    </div>

                                                                </td>
                                                            </tr>
                                                        @empty
                                                        @endforelse
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>

                                        {{-- form --}}
                                        <div class="tab-pane fade " id="sample" role="tabpanel"
                                            aria-labelledby="sample-tab">
                                            <div>
                                                <form id="procedure-filter-form" action="{{ url()->current() }}"
                                                    method="GET">
                                                    <div class="row">
                                                        <div class="col-md-4">
                                                            <select id="procedure-filter" name="procedure_id"
                                                                class="form-control">
                                                                <option value="-1">{{ __('All Procedures') }}</option>
                                                                @foreach ($procedures as $procedure)
                                                                    <option value="{{ $procedure->id }}"
                                                                        {{ $selectedProcedureId == $procedure->id ? 'selected' : '' }}>
                                                                        {{ $procedure->procedure->procedure_name }}
                                                                    </option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </div>
                                                </form>
                                            </div>
                                            <div class="dt-responsive table-responsive">
                                                <table class="table table-hover basic-datatable text-center">
                                                    <thead>
                                                        <tr>
                                                            <th class="text-center">{{ __('no') }}</th>
                                                            <th class="text-center">{{ __('Sample Name') }}</th>
                                                            <th class="text-center">{{ __('Procedure Name') }}</th>
                                                            <th class="text-center">{{ __('Coding') }}</th>
                                                            <th class="text-center">{{ __('Required Sample') }}</th>
                                                            <th class="text-center">{{ __('status') }}</th>
                                                            <th class="text-center">{{ __('Action') }}</th>

                                                        </tr>
                                                    </thead>

                                                    <tbody id="results-container">
                                                        {{-- @include('partials.forms') --}}
                                                        @include('partials.forms', ['forms' => $forms])

                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>

                                        <div class="tab-pane fade" id="responsibility" role="tabpanel"
                                            aria-labelledby="responsibility-tab">
                                        </div>

                                        <div class="tab-pane fade" id="specifcationItem" role="tabpanel"
                                            aria-labelledby="specifcationItem-tab">
                                            <div class="row">
                                                @include('iso_dic.specification_items.table', [
                                                    'specificationItems' => $specificationItems,
                                                ])
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endsection
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#procedure-filter').on('change', function() {
                var selectedProcedureId = $(this).val();
                $.ajax({
                    url: $('#procedure-filter-form').attr('action'),
                    type: 'GET',
                    data: {
                        procedure_id: selectedProcedureId
                    },
                    success: function(response) {
                        $('#results-container').html(response);
                    },
                    error: function(xhr) {
                        console.error('Error occurred:', xhr);
                    }
                });
            });
        });
    </script>
