{{ Form::model($IsoSpecificationItem, ['route' => ['iso_dic.users.update', $IsoSpecificationItem->id], 'method' => 'PUT']) }}
<div class="modal-body">
    <div class="row">
            <input type="hidden" name="item_id" id="item_id">
            <div class="form-group col-md-6">
                {{ Form::label('first_name', __('First Name'), ['class' => 'form-label']) }}
                {{ Form::text('first_name', null, ['class' => 'form-control', 'placeholder' => __('Enter First Name'), 'required' => 'required']) }}
            </div>
            <div class="form-group col-md-6">
                {{ Form::label('last_name', __('Last Name'), ['class' => 'form-label']) }}
                {{ Form::text('last_name', null, ['class' => 'form-control', 'placeholder' => __('Enter Name'), 'required' => 'required']) }}
            </div>
        <div class="form-group col-md-6">
            {{ Form::label('name', __('Name'), ['class' => 'form-label']) }}
            {{ Form::text('name', null, ['class' => 'form-control', 'placeholder' => __('Enter Name'), 'required' => 'required']) }}
        </div>
        <div class="form-group col-md-6">
            {{ Form::label('email', __('Email'), ['class' => 'form-label']) }}
            {{ Form::text('email', null, ['class' => 'form-control', 'placeholder' => __('Enter Email'), 'required' => 'required']) }}
        </div>
        <div class="form-group col-md-6">
            {{ Form::label('phone_number', __('Phone Number'), ['class' => 'form-label']) }}
            {{ Form::text('phone_number', null, ['class' => 'form-control', 'placeholder' => __('Enter Phone Number')]) }}
        </div>

    </div>
</div>
<div class="modal-footer">
    {{ Form::submit(__('Update'), ['class' => 'btn btn-secondary btn-rounded']) }}
</div>
{{ Form::close() }}
