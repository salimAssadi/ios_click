{{ Form::open(['url' => 'iso_dic/specification_items', 'method' => 'post']) }}
<div class="modal-body model-xxl">
    <div class="row">
        <!-- Select ISO System -->
        <div class="form-group col-md-6">
            {{ Form::label('iso_system_id', __('ISO System'), ['class' => 'form-label']) }}
            {{ Form::select('iso_system_id', $isoSystems->pluck('name_ar', 'id'), null, ['class' => 'form-control  showsearch', 'placeholder' => __('Select ISO System'), 'required' => 'required']) }}
        </div>

        <!-- Select Parent Item -->
        <div class="form-group col-md-6">
            {{ Form::label('parent_id', __('Parent Item'), ['class' => 'form-label']) }}
            {{ Form::select('parent_id', $parentItems, null, ['class' => 'form-control  showsearch', 'placeholder' => __('Select Parent Item (optional)')]) }}
        </div>


        <div class="row">
            <!-- Inspection Question -->
            <div class="form-group col-md-6">
                {{ Form::label('inspection_question_ar', __('Arabic Inspection Question'), ['class' => 'form-label']) }}
                {{ Form::textarea('inspection_question_ar', null, ['class' => 'form-control', 'id' => 'inspection_question', 'placeholder' => __('Enter Arabic Inspection Question'), 'rows' => 2]) }}
            </div>

            <!-- Inspection Question -->
            <div class="form-group col-md-6">
                {{ Form::label('inspection_question_en', __('English Inspection Question'), ['class' => 'form-label']) }}
                {{ Form::textarea('inspection_question_en', null, ['class' => 'form-control', 'id' => 'inspection_question_en', 'placeholder' => __('Enter English Inspection Question'), 'rows' => 2]) }}
            </div>
        </div>

        <!-- Additional Text -->
        <div class="row">

            <div class="form-group col-md-6">
                {{ Form::label('additional_text_ar', __('Arabic Additional Text'), ['class' => 'form-label']) }}
                {{ Form::textarea('additional_text_ar', null, ['class' => 'form-control', 'id' => 'additional_text', 'placeholder' => __('Enter Arabic Additional Text'), 'rows' => 2]) }}
            </div>
            <!-- Additional Text -->
            <div class="form-group col-md-6">
                {{ Form::label('additional_text_en', __('English Additional Text'), ['class' => 'form-label']) }}
                {{ Form::textarea('additional_text_en', null, ['class' => 'form-control', 'id' => 'additional_text', 'placeholder' => __('Enter English Additional Text'), 'rows' => 2]) }}
            </div>
        </div>

        <!-- Attachment -->
        <div class="form-group col-md-6">
            {{ Form::label('attachment', __('Attachment'), ['class' => 'form-label']) }}
            {{ Form::file('attachment', ['class' => 'form-control', 'id' => 'attachment']) }}
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
