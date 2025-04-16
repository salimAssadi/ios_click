{{ Form::open(['url' => 'iso_dic/iso_systems', 'method' => 'post', 'files' => true]) }}
<div class="modal-body">
    <div class="row">
       
        <div class="form-group col-md-6">
            {{ Form::label('name_ar', __('name ar') . ' <span class="text-danger">*</span>', ['class' => 'form-label'], false) }}
            {{ Form::text('name_ar', null, ['class' => 'form-control', 'placeholder' => __('Enter Name'), 'required' => 'required']) }}
        </div>
        <div class="form-group col-md-6">
            {{ Form::label('name_en', __('name en'). ' <span class="text-danger">*</span>', ['class' => 'form-label'], false) }}
            {{ Form::text('name_en', null, ['class' => 'form-control', 'placeholder' => __('Enter Name'), 'required' => 'required']) }}
        </div>
        <div class="form-group col-md-6">
            {{ Form::label('code', __('code'). ' <span class="text-danger">*</span>', ['class' => 'form-label'], false) }}
            {{ Form::text('code', null, ['class' => 'form-control', 'placeholder' => __('Enter code'), 'required' => 'required']) }}
        </div>
        <div class="form-group col-md-6">
            {{ Form::label('symbole', __('symbole'). ' <span class="text-danger">*</span>', ['class' => 'form-label'], false) }}
            {{ Form::text('symbole', null, ['class' => 'form-control', 'placeholder' => __('Enter symbole'), 'required' => 'required']) }}
        </div>
        <div class="form-group col-md-6">
            {{ Form::label('iso_image', __('ios image'), ['class' => 'form-label'], false) }}
            {{ Form::file('iso_image', ['class' => 'form-control', 'placeholder' => __('Enter ios image')]) }}
        </div>
        <div class="form-group col-md-6">
            {{ Form::label('version', __('version'). ' <span class="text-danger">*</span>', ['class' => 'form-label'], false) }}
            {{ Form::text('version', null, ['class' => 'form-control', 'placeholder' => __('Enter version'), 'required' => 'required']) }}

        </div>
        <div class="form-group col-md-6">
            {{ Form::label('status', __('iso status') . ' <span class="text-danger">*</span>', ['class' => 'form-label'], false) }}
            {{ Form::select('status', ['1' => __('Active'), '0' => __('Inactive')], null, ['class' => 'form-control', 'placeholder' => __('Select status'), 'required' => 'required']) }}

        </div>

        <div class="form-group col-md-12">
            {{ Form::label('specification', __('specification'), ['class' => 'form-label']) }}
            {{ Form::textarea('specification', null, ['class' => 'form-control', 'placeholder' => __('Enter specification'),'rows'=>2]) }}
        </div>
        
    </div>
</div>
<div class="modal-footer">
    {{ Form::submit(__('Create'), ['class' => 'btn btn-secondary ml-10']) }}
</div>
{{ Form::close() }}
