@extends('layouts.admin-app')

@section('page-title')
    {{ __('Edit Procedure') }}
@endsection

@section('breadcrumb')
    <li class="breadcrumb-item">
        <a href="">{{ __('Dashboard') }}</a>
    </li>
    <li class="breadcrumb-item">
        <a href="{{ route('iso_dic.procedures.index') }}">{{ __('Procedures') }}</a>
    </li>
    <li class="breadcrumb-item" aria-current="page">
        {{ __('Edit Procedure') }}
    </li>
@endsection

@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header">
                    <h5>{{ __('Edit Procedure') }}</h5>
                </div>
                <div class="card-body">
                    {{ Form::model($procedure, ['route' => ['iso_dic.procedures.update', $procedure->id], 'method' => 'PUT', 'files' => true]) }}
                        <div class="form-group col-md-12">
                            {{ Form::label('category_id', __('Category'), ['class' => 'form-label']) }}
                            {{ Form::select('category_id', $categories, $procedure->category_id, ['class' => 'form-control hidesearch']) }}
                        </div>
                        <div class="row">
                            <div class="form-group col-md-6">
                                {{ Form::label('procedure_name_ar', __('Procedure Name (arabic)') . ' <span class="text-danger">*</span>', ['class' => 'form-label'], false) }}
                                {{ Form::text('procedure_name_ar', $procedure->procedure_name_ar, ['class' => 'form-control', 'placeholder' => __('Enter Procedure Name (arabic)'), 'required' => 'required']) }}
                            </div>

                            <div class="form-group col-md-6">
                                {{ Form::label('procedure_name_en', __('Procedure Name (english)') . ' <span class="text-danger">*</span>', ['class' => 'form-label'], false) }}
                                {{ Form::text('procedure_name_en', $procedure->procedure_name_en, ['class' => 'form-control', 'placeholder' => __('Enter Procedure Name (english)'), 'required' => 'required']) }}
                            </div>

                            <div class="form-group col-md-6">
                                {{ Form::label('procedure_description_ar', __('Procedure Description (arabic)'), ['class' => 'form-label']) }}
                                {{ Form::textarea('procedure_description_ar', $procedure->description_ar, ['class' => 'form-control', 'placeholder' => __('Enter Procedure Description (arabic)'), 'rows' => 2]) }}
                            </div>

                            <div class="form-group col-md-6">
                                {{ Form::label('procedure_description_en', __('Procedure Description (english)'), ['class' => 'form-label']) }}
                                {{ Form::textarea('procedure_description_en', $procedure->description_en, ['class' => 'form-control', 'placeholder' => __('Enter Procedure Description (english)'), 'rows' => 2]) }}
                            </div>

                            @if ($procedure->attachments->count() > 0)
                                <div class="col-12 mt-3">
                                    <label class="form-label">{{ __('Current Attachments') }}</label>
                                    <div class="table-responsive">
                                        <table class="table table-bordered">
                                            <thead>
                                                <tr>
                                                    <th>{{ __('File Name') }}</th>
                                                    <th class="text-end">{{ __('Action') }}</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($procedure->attachments as $attachment)
                                                    <tr>
                                                        <td>
                                                            <a href="{{ route('iso_dic.procedures.attachments.download', $attachment->id) }}"
                                                                class="text-primary">
                                                                {{ $attachment->original_name }}
                                                            </a>
                                                        </td>
                                                        <td class="text-end">
                                                            <form action=""
                                                                method="POST" class="d-inline">
                                                                @csrf
                                                                {{-- @method('DELETE') --}}
                                                                <a href="#" class="text-danger delete-attachment"
                                                                    onclick="event.preventDefault(); this.closest('form').submit();">
                                                                    <i class="ti ti-trash fs-2"></i>
                                                                </a>
                                                            </form>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            @endif

                            <div class="form-group col-md-12 mt-3">
                                <label class="form-label" for="attachments">{{ __('New Attachments') }}</label>
                                <input type="file" name="attachments[]" id="attachments" class="form-control" multiple>
                                <small class="text-muted">{{ __('Supported formats: PDF, DOC, DOCX, XLS, XLSX. Max size: 10MB') }}</small>
                                @error('attachments.*')
                                    <span class="invalid-feedback" role="alert">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="form-group col-md-6">
                                {{ Form::label('is_optional', __('Is Required'), ['class' => 'form-label d-block']) }}
                                <div class="form-check form-check-inline">
                                    {{ Form::radio('is_optional', 1, $procedure->is_optional == 1, ['class' => 'form-check-input', 'id' => 'is_optional']) }}
                                    {{ Form::label('is_optional', __('Optional'), ['class' => 'form-check-label']) }}
                                </div>
                                <div class="form-check form-check-inline">
                                    {{ Form::radio('is_optional', 0, $procedure->is_optional == 0, ['class' => 'form-check-input', 'id' => 'required']) }}
                                    {{ Form::label('required', __('Required'), ['class' => 'form-check-label']) }}
                                </div>
                            </div>

                            <div class="form-group col-md-6">
                                {{ Form::label('status', __('Status'), ['class' => 'form-label d-block']) }}
                                <div class="form-check form-check-inline">
                                    {{ Form::radio('status', 1, $procedure->status == 1, ['class' => 'form-check-input', 'id' => 'status_active']) }}
                                    {{ Form::label('status_active', __('Active'), ['class' => 'form-check-label']) }}
                                </div>
                                <div class="form-check form-check-inline">
                                    {{ Form::radio('status', 0, $procedure->status == 0, ['class' => 'form-check-input', 'id' => 'status_inactive']) }}
                                    {{ Form::label('status_inactive', __('Inactive'), ['class' => 'form-check-label']) }}
                                </div>
                            </div>

                            <div class="form-group col-md-12">
                                <div class="form-check form-check-inline">
                                    {!! Form::checkbox('enable_upload_file', 1, $procedure->enable_upload_file, ['class' => 'form-check-input', 'id' => 'enable_upload_file']) !!}
                                    {!! Form::label('enable_upload_file', __('Enable Upload File'), ['class' => 'form-check-label']) !!}
                                </div>

                                <div class="form-check form-check-inline">
                                    {!! Form::checkbox('enable_editor', 1, $procedure->enable_editor, ['class' => 'form-check-input', 'id' => 'enable_editor']) !!}
                                    {!! Form::label('enable_editor', __('Enable Editor'), ['class' => 'form-check-label']) !!}
                                </div>

                                <div class="form-check form-check-inline">
                                    {!! Form::checkbox('has_menual_config', 1, $procedure->has_menual_config, ['class' => 'form-check-input', 'id' => 'has_menual_config']) !!}
                                    {!! Form::label('has_menual_config', __('Has Manual Config'), ['class' => 'form-check-label']) !!}
                                </div>
                            </div>

                            <div class="form-group col-md-12" id="blade-view-field" style="display: none;">
                                {!! Form::label('blade_view', __('Blade View')) !!}
                                {!! Form::text('blade_view', $procedure->blade_view, ['class' => 'form-control', 'id' => 'blade_view']) !!}
                            </div>

                            <div class="form-group col-md-12 text-end mt-3">
                                <a href="{{ route('iso_dic.procedures.index') }}" class="btn btn-secondary">{{ __('Cancel') }}</a>
                                {{ Form::submit(__('Update'), ['class' => 'btn btn-primary']) }}
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
