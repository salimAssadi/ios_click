@extends('layouts.admin-app')
@section('page-title')
    {{ __('ISO System Details') }}
@endsection

@section('breadcrumb')
    <ul class="breadcrumb mb-0">
        <li class="breadcrumb-item">
            <a href="{{ route('iso_dic.home') }}">{{ __('Dashboard') }}</a>
        </li>
        <li class="breadcrumb-item">
            <a href="{{ route('iso_dic.iso_systems.index') }}">{{ __('ISO Systems') }}</a>
        </li>
        <li class="breadcrumb-item active">
            <a href="#">{{ __('Detail') }}</a>
        </li>
    </ul>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <!-- System Info Card -->
        <div class="card mb-4">
            <div class="card-header">
                <div class="row align-items-center g-3">
                    <div class="col">
                        <div class="d-flex align-items-center">
                            <div class="flex-shrink-0 me-3">
                                <img src="{{ getISOImage(getFilePath('isoIcon') . '/' . $isoSystem->image) }}"
                                    alt="{{ $isoSystem->name_ar }}" class="img-fluid rounded"
                                    style="width: 48px; height: 48px; object-fit: cover;">
                            </div>
                            <div>
                                <h4 class="mb-0">{{ $isoSystem->name_ar }}</h4>
                                <small class="text-body-secondary">{{ $isoSystem->name_en }}</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-auto">
                        <a class="btn btn-primary me-2" href="{{ route('iso_dic.iso_systems.procedure.create', \Illuminate\Support\Facades\Crypt::encrypt($isoSystem->id)) }}">
                            <i class="ti ti-plus"></i> {{ __('Create Procedure') }}
                        </a>
                        <a class="btn btn-secondary" href="{{ route('iso_dic.iso_systems.sample.create', \Illuminate\Support\Facades\Crypt::encrypt($isoSystem->id)) }}">
                            <i class="ti ti-plus"></i> {{ __('Create Sample') }}
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Procedures Card -->
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">{{ __('Procedures') }}</h5>
                <button class="btn btn-link" type="button" data-bs-toggle="collapse" data-bs-target="#proceduresCollapse">
                    <i class="ti ti-chevron-down"></i>
                </button>
            </div>
            <div class="collapse show" id="proceduresCollapse">
                <div class="card-body p-0" id="procedures-content">
                    @include('iso_dic.iso_systems._procedures', ['procedures' => $procedures])
                </div>
            </div>
        </div>

        <!-- Samples Card -->
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">{{ __('Samples') }}</h5>
                <div class="d-flex align-items-center gap-3">
                    <div class="input-group">
                        <span class="input-group-text"><i class="ti ti-filter"></i></span>
                        <select id="procedure-filter" name="procedure_id" class="form-select">
                            <option value="-1">{{__('All Procedures')}}</option>
                            @foreach ($procedures as $procedure)
                                <option value="{{ $procedure->id }}" {{ $selectedProcedureId == $procedure->id ? 'selected' : '' }}>
                                    {{ $procedure->procedure_name}}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <button class="btn btn-link" type="button" data-bs-toggle="collapse" data-bs-target="#samplesCollapse">
                        <i class="ti ti-chevron-down"></i>
                    </button>
                </div>
            </div>
            <div class="collapse show" id="samplesCollapse">
                <div class="card-body p-0" id="samples-content">
                    @include('iso_dic.iso_systems._samples', ['forms' => $forms])
                </div>
            </div>
        </div>

        <!-- Specification Items Card -->
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">{{ __('ISO Specification Items') }}</h5>
                <button class="btn btn-link" type="button" data-bs-toggle="collapse" data-bs-target="#specificationCollapse">
                    <i class="ti ti-chevron-down"></i>
                </button>
            </div>
            <div class="collapse show" id="specificationCollapse">
                <div class="card-body p-0" id="specification-items-content">
                    @include('iso_dic.specification_items.table', ['specificationItems' => $specificationItems])
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('script-page')
<script>
$(document).ready(function() {
    // Handle procedure filter change
    $('#procedure-filter').change(function() {
        loadSamples();
    });

    function loadSamples() {
        const procedureId = $('#procedure-filter').val();
        $.ajax({
            url: '{{ route("iso_dic.iso_systems.samples", Crypt::encrypt($isoSystem->id)) }}?procedure_id=' + procedureId,
            success: function(data) {
                $('#samples-content').html(data);
            }
        });
    }


    // Initialize tooltips
    $('[data-bs-toggle="tooltip"]').tooltip();

    // Handle collapse icons
    $('.collapse').on('show.bs.collapse hide.bs.collapse', function() {
        const icon = $(this).siblings('.card-header').find('.ti');
        icon.toggleClass('ti-chevron-down ti-chevron-up');
    });
});
</script>
@endpush
