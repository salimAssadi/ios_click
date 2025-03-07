{{ Form::open(['url' => 'iso_dic/procedures', 'method' => 'post', 'files' => true]) }}
<div class="modal-body">
    <div class="row">

        <div class="form-group col-md-12">
            {{ Form::label('category_id', __('Category'), ['class' => 'form-label']) }}
            <span class="text-danger">*</span> <!-- Add the asterisk outside the label -->
            {{ Form::select('category_id', $category, null, ['class' => 'form-control hidesearch', 'id' => 'category']) }}
        </div>

        <div class="form-group col-md-12" id="iso-system-field">
            {{ Form::label('iso_system_id', __('ISO System'), ['class' => 'form-label']) }}
            {{ Form::select('iso_system_id', $isoSystems, null, ['class' => 'form-control showsearch', 'id' => 'iso_system']) }}
        </div>

        <div class="form-group col-md-12">
            {{ Form::label('procedure_name', __('Procedure Name') . ' <span class="text-danger">*</span>', ['class' => 'form-label'], false) }}
            {{ Form::text('procedure_name', null, ['class' => 'form-control', 'placeholder' => __('Enter Procedure Name'), 'required' => 'required']) }}
        </div>

        <div class="form-group col-md-12">
            {{ Form::label('procedure_description', __('Procedure Description'), ['class' => 'form-label']) }}
            {{ Form::textarea('procedure_description', null, ['class' => 'form-control', 'placeholder' => __('Enter Procedure Description'), 'rows' => 2]) }}
        </div>

        <!-- is_optional -->
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

        <!-- Status -->
        <div class="form-group col-md-6">
            {{ Form::label('status', __('Status'), ['class' => 'form-label d-block']) }}
            <div class="form-check form-check-inline">
                {{ Form::radio('status', 1, true, ['class' => 'form-check-input', 'id' => 'status_active']) }}
                {{ Form::label('status_active', __('Active'), ['class' => 'form-check-label']) }}
            </div>
            <div class="form-check form-check-inline">
                {{ Form::radio('status', 0, false, ['class' => 'form-check-input', 'id' => 'status_inactive']) }}
                {{ Form::label('status_inactive', __('Inactive'), ['class' => 'form-check-label']) }}
            </div>
        </div>
    </div>
</div>
<div class="modal-footer">
    {{ Form::submit(__('Create'), ['class' => 'btn btn-secondary ml-10']) }}
</div>
{{ Form::close() }}
<script>
    $(document).ready(function() {
        $('#category').on('change', function() {
            var selectedValue = $(this).val();
            if (selectedValue == 2) {
                $('#iso-system-field').hide();
                $('#iso_system').val('');
                ('#iso_system').trigger('change');
            } else {
                $('#iso-system-field').show();
            }
        });

        $('#category').trigger('change');
    });
</script>
<x-file-manager />
