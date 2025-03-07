{{ Form::open(['url' => 'iso_dic/samples', 'method' => 'post', 'files' => true]) }}
<div class="modal-body">
    <div class="row">
        <div class="form-group col-md-12">
            {{ Form::label('sample_name', __('Sample Name') . ' <span class="text-danger">*</span>', ['class' => 'form-label'], false) }}
            {{ Form::text('sample_name', null, ['class' => 'form-control', 'placeholder' => __('Enter Sample Name'), 'required' => 'required']) }}
        </div>
        
        <div class="form-group col-md-12">
            {{ Form::label('sample_description', __('Sample Description'), ['class' => 'form-label']) }}
            {{ Form::textarea('sample_description', null, ['class' => 'form-control', 'placeholder' => __('Enter Sample Description'),'rows'=>2]) }}
        </div>
{{-- 
        <!-- Template Path with File Manager Integration -->
        <div class="form-group col-md-6">
            {{ Form::label('template_path', __('Template Path'), ['class' => 'form-label'], false) }}
            {{ Form::text('template_path', null, ['class' => 'form-control', 'placeholder' => __('Enter Template Path'), 'id' => 'file_path', 'readonly' => 'readonly']) }}
            <button type="button" id="open-file-manager" class="btn btn-primary">{{ __('Open File Manager') }}</button>
        </div> --}}

        <!-- is_optional -->
        <div class="form-group col-md-6">
            {{ Form::label('is_optional', __('Is Required'), ['class' => 'form-label d-block']) }}
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
<x-file-manager />
