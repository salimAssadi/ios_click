{{Form::model($country, array('route' => array('iso_dic.countries.update', $country->id), 'method' => 'PUT')) }}
<div class="modal-body">
    <div class="row">
        <div class="form-group  col-md-4">
            {{Form::label('name_ar',__('name_ar'),array('class'=>'form-label'))}}
            {{Form::text('name_ar',null,array('class'=>'form-control','placeholder'=>__('Enter name_ar')))}}
        </div>
        <div class="form-group  col-md-4">
            {{Form::label('name_en',__('name_en'),array('class'=>'form-label'))}}
            {{Form::text('name_en',null,array('class'=>'form-control','placeholder'=>__('Enter name_en')))}}
        </div>
        <div class="form-group  col-md-4">
            {{Form::label('code',__('code'),array('class'=>'form-label'))}}
            {{Form::text('code',null,array('class'=>'form-control','placeholder'=>__('Enter code')))}}
        </div>
    </div>
</div>
<div class="modal-footer">
    {{Form::submit(__('Update'),array('class'=>'btn btn-secondary btn-rounded'))}}
</div>
{{ Form::close() }}

