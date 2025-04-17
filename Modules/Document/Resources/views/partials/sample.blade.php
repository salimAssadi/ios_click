@foreach ($templates as $template)
    <div class="col-md-4 mb-3">
        <div class="card h-100 cursor-pointer template-card">
            <div class="card-body shadow-sm rounded bg-gray-100">
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="template_id" value="{{ $template->id }}" id="template_{{ $template->id }}">
                    <label class="form-check-label w-100" for="template_{{ $template->id }}">
                        <h5 class="mb-2">{{ $template->sample_name_ar }}</h5>
                    </label>
                </div>
            </div>
        </div>
    </div>
@endforeach
<div class="col-md-4 mb-3">
    <div class="card h-100 cursor-pointer template-card ">
        <div class="card-body shadow-sm rounded bg-teal-100">
            <div class="form-check">
                <input class="form-check-input" type="radio" name="template_id" value="custom" id="template_custom">
                <label class="form-check-label w-100" for="template_custom">
                    <h5 class="mb-2">{{ __('Custom Sample') }}</h5>
                </label>
            </div>
        </div>
    </div>
</div>
