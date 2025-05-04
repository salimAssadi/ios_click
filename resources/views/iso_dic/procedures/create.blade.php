@extends('layouts.admin-app')

@section('page-title')
    {{ __('Create Procedure') }}
@endsection

@section('breadcrumb')
    <li class="breadcrumb-item">
        <a href="">{{ __('Dashboard') }}</a>
    </li>
    <li class="breadcrumb-item">
        <a href="{{ route('iso_dic.procedures.index') }}">{{ __('Procedures') }}</a>
    </li>
    <li class="breadcrumb-item" aria-current="page">
        {{ __('Create Procedure') }}
    </li>
@endsection

@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header">
                    <h5>{{ __('Create New Procedure') }}</h5>
                </div>
                <div class="card-body">
                    {{ Form::open(['route' => 'iso_dic.procedures.store', 'method' => 'post', 'files' => true]) }}
                    <div class="form-group col-md-12">
                        {{ Form::label('category_id', __('Category'), ['class' => 'form-label']) }}
                        {{ Form::select('category_id', $categories, null, ['class' => 'form-control hidesearch']) }}
                    </div>
                    <div class="row">
                        <div class="form-group col-md-6">
                            {{ Form::label('procedure_name_ar', __('Procedure Name (arabic)') . ' <span class="text-danger">*</span>', ['class' => 'form-label'], false) }}
                            {{ Form::text('procedure_name_ar', null, ['class' => 'form-control', 'placeholder' => __('Enter Procedure Name (arabic)'), 'required' => 'required']) }}
                        </div>

                        <div class="form-group col-md-6">
                            {{ Form::label('procedure_name_en', __('Procedure Name (english)') . ' <span class="text-danger">*</span>', ['class' => 'form-label'], false) }}
                            {{ Form::text('procedure_name_en', null, ['class' => 'form-control', 'placeholder' => __('Enter Procedure Name (english)'), 'required' => 'required']) }}
                        </div>
                        <div class="form-group col-md-6">
                            {{ Form::label('procedure_description_ar', __('Procedure Description (arabic)'), ['class' => 'form-label']) }}
                            {{ Form::textarea('procedure_description_ar', null, ['class' => 'form-control', 'placeholder' => __('Enter Procedure Description (arabic)'), 'rows' => 2]) }}
                        </div>

                        <div class="form-group col-md-6">
                            {{ Form::label('procedure_description_en', __('Procedure Description (english)'), ['class' => 'form-label']) }}
                            {{ Form::textarea('procedure_description_en', null, ['class' => 'form-control', 'placeholder' => __('Enter Procedure Description (english)'), 'rows' => 2]) }}
                        </div>

                    </div>



                    <div class="form-group col-md-12">
                        <label for="attachments" class="form-label">{{ __('Attachments') }}</label>
                        <input type="file" name="attachments[]" id="attachments" class="form-control" multiple>
                        <small class="text-muted">{{ __('You can select multiple files') }}</small>
                    </div>

                    <div class="form-group col-md-6">
                        {{ Form::label('is_optional', __('Required Procedure'), ['class' => 'form-label d-block']) }}
                        <div class="form-check form-check-inline">
                            {{ Form::radio('is_optional', 1, true, ['class' => 'form-check-input', 'id' => 'is_optional']) }}
                            {{ Form::label('Optional', __('Optional'), ['class' => 'form-check-label']) }}
                        </div>
                        <div class="form-check form-check-inline">
                            {{ Form::radio('is_optional', 0, false, ['class' => 'form-check-input', 'id' => 'required']) }}
                            {{ Form::label('Required', __('Required'), ['class' => 'form-check-label']) }}
                        </div>
                    </div>

                    <div class="form-group col-md-6">
                        {{ Form::label('status', __('Status'), ['class' => 'form-label d-block']) }}
                        <div class="form-check form-check-inline">
                            {{ Form::radio('status', 1, false, ['class' => 'form-check-input', 'id' => 'status_active']) }}
                            {{ Form::label('status_active', __('Active'), ['class' => 'form-check-label']) }}
                        </div>
                        <div class="form-check form-check-inline">
                            {{ Form::radio('status', 0, true, ['class' => 'form-check-input', 'id' => 'status_inactive']) }}
                            {{ Form::label('status_inactive', __('Inactive'), ['class' => 'form-check-label']) }}
                        </div>
                    </div>

                    <div class="form-group col-md-12">
                        <div class="form-check form-check-inline">
                            {!! Form::checkbox('enable_upload_file', 1, null, ['class' => 'form-check-input', 'id' => 'enable_upload_file']) !!}
                            {!! Form::label('enable_upload_file', __('Enable Upload File'), ['class' => 'form-check-label']) !!}
                        </div>

                        <div class="form-check form-check-inline">
                            {!! Form::checkbox('enable_editor', 1, null, ['class' => 'form-check-input', 'id' => 'enable_editor']) !!}
                            {!! Form::label('enable_editor', __('Enable Editor'), ['class' => 'form-check-label']) !!}
                        </div>

                        <div class="form-check form-check-inline">
                            {!! Form::checkbox('has_menual_config', 1, null, ['class' => 'form-check-input', 'id' => 'has_menual_config' , 'checked' => true ,'readonly' => true]) !!}
                            {!! Form::label('has_menual_config', __('Has Manual Config'), ['class' => 'form-check-label']) !!}
                        </div>
                    </div>

                    <div class="form-group col-md-12" id="blade-view-field" style="display: none;">
                        {!! Form::label('blade_view', __('Blade View')) !!}
                        {!! Form::text('blade_view', null, ['class' => 'form-control', 'id' => 'blade_view']) !!}
                    </div>

                    <div class="form-group col-md-12 text-end">
                        <a href="{{ route('iso_dic.procedures.index') }}"
                            class="btn btn-secondary">{{ __('Cancel') }}</a>
                        {{ Form::submit(__('Create'), ['class' => 'btn btn-primary']) }}
                    </div>
                </div>
                {{ Form::close() }}
            </div>
        </div>
    </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            function toggleBladeViewField() {
                if ($('#has_menual_config').is(':checked')) {
                    $('#blade-view-field').show();
                } else {
                    $('#blade-view-field').hide();
                    $('#blade_view').val('');
                }
            }

            toggleBladeViewField();

            $('#has_menual_config').on('change', function() {
                toggleBladeViewField();
            });
        });
    </script>
@endpush
