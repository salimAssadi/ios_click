{{Form::model($procedure, array('route' => array('iso_dic.procedures.update', $procedure->id), 'method' => 'PUT','enctype' => "multipart/form-data")) }}

<div class="modal-body">
    <div class="row">
        <div class="form-group col-md-12">
            {{ Form::label('category_id', __('Category'), ['class' => 'form-label']) }}
            <span class="text-danger">*</span> <!-- Add the asterisk outside the label -->
            {{ Form::select('category_id', $categories, $selectedCategoryId, ['class' => 'form-control hidesearch', 'id' => 'category']) }}
        </div>

        <div class="form-group col-md-12" id="iso-system-field">
            {{ Form::label('iso_system_id', __('ISO System'), ['class' => 'form-label']) }}
            {{ Form::select('iso_system_id', $isoSystems, $selectedIsoSystemId, ['class' => 'form-control showsearch', 'id' => 'iso_system']) }}
        </div>
        <!-- Procedure Name -->
        <div class="form-group col-md-12">
            {{ Form::label('procedure_name', __('Procedure Name') . ' <span class="text-danger">*</span>', ['class' => 'form-label'], false) }}
            {{ Form::text('procedure_name', $procedure->procedure_name, [
                'class' => 'form-control' . ($errors->has('procedure_name') ? ' is-invalid' : ''),
                'placeholder' => __('Enter Procedure Name'),
                'required' => 'required'
            ]) }}
            @if ($errors->has('procedure_name'))
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $errors->first('procedure_name') }}</strong>
                </span>
            @endif
        </div>

        <!-- Procedure Description -->
        <div class="form-group col-md-12">
            {{ Form::label('procedure_description', __('Procedure Description'), ['class' => 'form-label']) }}
            {{ Form::textarea('procedure_description', $procedure->description, [
                'class' => 'form-control' . ($errors->has('procedure_description') ? ' is-invalid' : ''),
                'placeholder' => __('Enter Procedure Description'),
                'rows' => 2
            ]) }}
            @if ($errors->has('procedure_description'))
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $errors->first('procedure_description') }}</strong>
                </span>
            @endif
        </div>
        
        <!-- Is Optional --> 
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
<script>
    $(document).ready(function() {
        $('#category').on('change', function() {
            var selectedValue = $(this).val();
            if (selectedValue == 2) {
                $('#iso-system-field').hide();
            } else {
                $('#iso-system-field').show();
            }
        });

        $('#category').trigger('change');
    });
</script>
 