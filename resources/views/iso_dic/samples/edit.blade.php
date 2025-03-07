{{Form::model($procedure, array('route' => array('iso_dic.samples.update', $procedure->id), 'method' => 'PUT','enctype' => "multipart/form-data")) }}

<div class="modal-body">
    <div class="row">
        <!-- Sample Name -->
        <div class="form-group col-md-12">
            {{ Form::label('procedure_name', __('Sample Name') . ' <span class="text-danger">*</span>', ['class' => 'form-label'], false) }}
            {{ Form::text('procedure_name', $procedure->procedure_name, [
                'class' => 'form-control' . ($errors->has('procedure_name') ? ' is-invalid' : ''),
                'placeholder' => __('Enter Sample Name'),
                'required' => 'required'
            ]) }}
            @if ($errors->has('procedure_name'))
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $errors->first('procedure_name') }}</strong>
                </span>
            @endif
        </div>

        <!-- Sample Description -->
        <div class="form-group col-md-12">
            {{ Form::label('procedure_description', __('Sample Description'), ['class' => 'form-label']) }}
            {{ Form::textarea('procedure_description', $procedure->description, [
                'class' => 'form-control' . ($errors->has('procedure_description') ? ' is-invalid' : ''),
                'placeholder' => __('Enter Sample Description'),
                'rows' => 2
            ]) }}
            @if ($errors->has('procedure_description'))
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $errors->first('procedure_description') }}</strong>
                </span>
            @endif
        </div>
         {{-- <!-- Template Path with File Manager Integration -->
        <div class="form-group col-md-4">
            {{ Form::label('template_path', __('Template Path'), ['class' => 'form-label'], false) }}
            {{ Form::text('template_path', null, ['class' => 'form-control', 'placeholder' => __('Enter Template Path'), 'id' => 'file_path', 'readonly' => 'readonly']) }}
            <button type="button" id="open-file-manager" class="btn btn-primary">{{ __('Open File Manager') }}</button>
        </div>
        <!-- Is Optional --> --}}
        <div class="form-group col-md-4">
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

        <!-- Status -->
        <div class="form-group col-md-4">
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
    </div>
</div>
<div class="modal-footer">
    {{ Form::submit(__('Update'), ['class' => 'btn btn-secondary btn-rounded']) }}
    <button type="button" class="btn btn-light btn-rounded" data-dismiss="modal">{{ __('Cancel') }}</button>
</div>
{{ Form::close() }}
 