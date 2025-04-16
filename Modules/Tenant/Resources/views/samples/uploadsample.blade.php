{{ Form::open(['url' => 'iso_dic/samples/uploadsample', 'method' => 'post', 'files' => true]) }}]
@csrf
<div class="modal-body">
    <div class="row">
        <!-- Download Sample Template Section -->
        <div class="mb-3">
            <label for="download_sample" class="form-label">{{ __('Download Sample Template') }}</label>
            <div>
                <a href="{{ asset(Storage::url($sample->template_path))}}" class="btn btn-primary" download>
                    {{ __('Download Sample Template') }}
                </a>
                <p class="text-muted small mt-2">
                    {{ __('This is a sample template file. Please download and use it as a reference before uploading your file.') }}
                </p>
            </div>
        </div>
        <input type="hidden" name="sample_id" value="{{$sample->id}}" id="">
        <!-- Upload File Section -->
        <div class="mb-3">
            <label for="upload_file" class="form-label">{{ __('Upload File') }}</label>
            <input type="file" class="form-control" id="upload_file" name="upload_file"
                accept=".txt,.doc,.docx,.pdf,.jpg,.jpeg,.png" required>
            <p class="text-muted small mt-2">
                {{ __('Ensure your file matches the format and structure of the sample template.') }}
            </p>
        </div>
    </div>
</div>
<div class="modal-footer">
    {{ Form::submit(__('Upload'), ['class' => 'btn btn-secondary ml-10']) }}
</div>
{{ Form::close() }}