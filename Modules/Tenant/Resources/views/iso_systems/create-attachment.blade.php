
{{ Form::open(['url' => ['iso_dic/attachments', \Illuminate\Support\Facades\Crypt::encrypt(0)], 'method' => 'post', 'files' => true]) }}
<div class="modal-body">
    <div class="row">
        {{ Form::hidden('document_id', 0, ['class' => 'form-control']) }}
        <!-- Select ISO System -->
        <div class="form-group col-md-6">
            {{ Form::label('iso_system_id', __('ISO System'), ['class' => 'form-label']) }}
            {{ Form::select('iso_system_id', $isoSystems->pluck('name_ar', 'id'), null, ['class' => 'form-control  showsearch', 'placeholder' => __('Select ISO System'), 'required' => 'required']) }}
        </div>
        <!-- Arabic Name -->
        <div class="form-group col-md-6">
            {{ Form::label('document_name_ar', __('اسم المرفق عربي'), ['class' => 'form-label']) }}
            {{ Form::text('document_name_ar', null, ['class' => 'form-control', 'required']) }}
        </div>

        <!-- English Name -->
        <div class="form-group col-md-6">
            {{ Form::label('document_name_en', __('اسم المرفق انجليزي'), ['class' => 'form-label']) }}
            {{ Form::text('document_name_en', null, ['class' => 'form-control', 'required']) }}
        </div>

        <!-- File Type Selection -->
        <div class="form-group col-md-6 hidesearch">
            {{ Form::label('file_type', __('المادة'), ['class' => 'form-label']) }}
            {{ Form::select(
                'file_type',
                [
                    'pdf' => __('PDF'),
                    'image' => __('صورة'),
                    'audio' => __('صوت'),
                    'video' => __('فيديو'),
                ],
                null,
                ['class' => 'form-control', 'id' => 'fileType', 'required'],
            ) }}
        </div>

        <!-- File Inputs -->
        <div class="form-group col-md-6" id="fileInputContainer">
            <!-- PDF Input -->
            <div id="pdfInput" >
                {{ Form::label('pdf_file', __('Upload PDF'), ['class' => 'form-label']) }}
                {{ Form::file('pdf_file', ['class' => 'form-control', 'accept' => 'application/pdf']) }}
            </div>

            <!-- Image Input -->
            <div id="imageInput" style="display: none;">
                {{ Form::label('image_file', __('Upload Image'), ['class' => 'form-label']) }}
                {{ Form::file('image_file', ['class' => 'form-control', 'accept' => 'image/*']) }}
            </div>

            <!-- Audio Input -->
            <div id="audioInput" style="display: none;">
                {{ Form::label('audio_file', __('Upload Audio'), ['class' => 'form-label']) }}
                {{ Form::file('audio_file', ['class' => 'form-control', 'accept' => 'audio/*']) }}
            </div>

            <!-- Video Input -->
            <div id="videoInput" style="display: none;">
                {{ Form::label('video_file', __('Upload Video'), ['class' => 'form-label']) }}
                {{ Form::file('video_file', ['class' => 'form-control', 'accept' => 'video/*']) }}
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
    {{ Form::submit(__('Upload'), ['class' => 'btn btn-secondary ml-10']) }}
</div>
{{ Form::close() }}

<!-- JavaScript to Handle File Type Selection -->
<script>
    document.getElementById('fileType').addEventListener('change', function() {
        let fileType = this.value;
        let inputs = ['pdfInput', 'imageInput', 'audioInput', 'videoInput'];

        // Hide all inputs first
        inputs.forEach(id => document.getElementById(id).style.display = 'none');

        // Show the selected input
        if (fileType) {
            document.getElementById(fileType + 'Input').style.display = 'block';
        }
    });
</script>
