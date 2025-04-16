@extends('layouts.admin-app')
@php
    $profile = asset(Storage::url('upload/profile/'));
@endphp
@section('page-title')
    {{ __('ISO Systems') }}
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item">
        <a href="{{ route('home') }}">{{ __('Dashboard') }}</a>
    </li>
    <li class="breadcrumb-item" aria-current="page">{{ __('Samples') }}</li>
@endsection

@section('content')
<div class="row">
    <div class="col-sm-12">
        <div class="card">
            <div class="card-header">
                <div class="row align-items-center g-3">
                    <div class="col">
                        <div class="me-2">
                            <form id="procedure-filter-form" action="{{ url()->current() }}" method="GET">
                                <div class="row g-2">
                                    <div class="col-md-4">
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="ti ti-filter"></i></span>
                                            <select id="procedure-filter" name="procedure_id" class="form-select">
                                                <option value="-1">{{__('All Procedures')}}</option>
                                                @foreach ($procedures as $procedure)
                                                    <option value="{{ $procedure->id }}" {{ $selectedProcedureId == $procedure->id ? 'selected' : '' }}>
                                                        {{ $procedure->procedure_name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                    <div class="col-auto">
                        <a href="{{ route('iso_dic.samples.create') }}" class="btn btn-primary">
                            <i class="ti ti-plus me-1"></i>
                            {{ __('Create Sample') }}
                        </a>
                    </div>
                </div>
            </div>
            
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover table-striped">
                        <thead class="thead-light">
                            <tr>
                                <th class="w-5">{{ __('No') }}</th>
                                <th>{{ __('Sample Name') }}</th>
                                <th>{{ __('Procedure Name')}}</th>
                                <th class="w-15">{{ __('Attachments') }}</th>
                                <th class="w-10">{{ __('Status') }}</th>
                                <th class="w-15 text-end">{{ __('Action') }}</th>
                            </tr>
                        </thead>
                        <tbody id="results-container">
                            @include('iso_dic.samples.sample', ['samples' => $samples])
                        </tbody>
                    </table>
                </div>

                <div class="d-flex justify-content-end mt-4">
                    {{ $samples->links('pagination::bootstrap-5') }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('script-page')
<script>
$(document).ready(function () {
    $('#procedure-filter').on('change', function () {
        var selectedProcedureId = $(this).val();
        $.ajax({
            url: $('#procedure-filter-form').attr('action'),
            type: 'GET',
            data: { 
                procedure_id: selectedProcedureId,
                page: 1 // Reset to first page when filter changes
            },
            success: function (response) {
                $('#results-container').html(response);
                // Update URL without page refresh
                window.history.pushState({}, '', '?procedure_id=' + selectedProcedureId);
            },
            error: function (xhr) {
                console.error('Error occurred:', xhr);
            }
        });
    });
});
</script>
@endpush