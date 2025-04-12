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
    <li class="breadcrumb-item" aria-current="page">
        {{ __('Samples') }}

    </li>
@endsection

@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="card table-card">
                <div class="card-header">
                    <div class="row align-items-center g-2">
                        <div class="col">
                            {{-- <h5>
                                {{ __('Samples') }}
                            </h5> --}}
                            <form id="procedure-filter-form" action="{{ url()->current() }}" method="GET">
                                <div class="row">
                                    <div class="col-md-4">
                                        <select id="procedure-filter" name="procedure_id" class="form-control">
"                                            <option value="-1">{{__('All Procedures')}}</option>
"                                            @foreach ($procedures as $procedure)
                                                <option value="{{ $procedure->id }}" {{ $selectedProcedureId == $procedure->id ? 'selected' : '' }}>
                                                    {{ $procedure->procedure_name_ar }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </form>      
                        </div>
                        <div class="col-auto">
                            <a href="{{ route('iso_dic.samples.create') }}" class="btn btn-secondary">

                                <i class="ti ti-circle-plus align-text-bottom"></i>
                                {{ __('Create Sample') }}
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body pt-0">
                    <div class="dt-responsive table-responsive">
                        <table class="table table-hover basic-datatable">
                            <thead>
                                <tr class="align-center">
                                    <th>{{ __('no') }}</th>
                                    <th>{{ __('Sample Name') }}</th>
                                    <th>{{ __('Procedure Name')}}</th>
                                    <th>{{ __('Attachments') }}</th>
                                    <th>{{ __('status') }}</th>
                                    <th>{{ __('Action') }}</th>
                                </tr>
                            </thead>
                            <tbody id="results-container">
                                @include('iso_dic.samples.sample', ['samples' => $samples])

                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function () {
        $('#procedure-filter').on('change', function () {
            var selectedProcedureId = $(this).val();
            $.ajax({
                url: $('#procedure-filter-form').attr('action'),
                type: 'GET',
                data: { procedure_id: selectedProcedureId },
                success: function (response) {
                    $('#results-container').html(response);
                },
                error: function (xhr) {
                    console.error('Error occurred:', xhr);
                }
            });
        });
  
    });
</script>