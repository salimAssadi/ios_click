@extends('tenant::layouts.app')
@section('page_title', 'Edit Company Seal')
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('tenant.setting.index') }}">{{ __('Settings') }}</a></li>
    <li class="breadcrumb-item"><a href="{{ route('tenant.setting.company-seals.index') }}">{{ __('Company Seals') }}</a></li>
    <li class="breadcrumb-item active">{{ __('Edit Company Seal') }}</li>
@endsection
@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">{{ __('Edit Company Seal') }}</h3>
            <div class="card-tools">
                <a href="{{ route('tenant.setting.company-seals.index') }}" class="btn btn-default btn-sm">
                    <i class="fas fa-arrow-left"></i> {{ __('Back to List') }}
                </a>
            </div>
        </div>
        <div class="card-body">
            <form action="{{ route('tenant.setting.company-seals.update', $companySeal) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="name_ar">{{ __('Name (Arabic)') }} <span class="text-danger">*</span></label>
                            <input type="text" name="name_ar" id="name_ar" class="form-control @error('name_ar') is-invalid @enderror" value="{{ old('name_ar', $companySeal->name_ar) }}" required>
                            @error('name_ar')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="name_en">{{ __('Name (English)') }} <span class="text-danger">*</span></label>
                            <input type="text" name="name_en" id="name_en" class="form-control @error('name_en') is-invalid @enderror" value="{{ old('name_en', $companySeal->name_en) }}" required>
                            @error('name_en')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>
                
                
                
                <div class="form-group">
                    <label for="seal_file">{{ __('Seal File') }}</label>
                    
                    @if($companySeal->file_path)
                        <div class="mb-2">
                            <img src="{{ route('tenant.setting.file', $companySeal->file_path) }}" alt="{{ $companySeal->name }}" class="img-thumbnail" style="max-height: 100px;">
                        </div>
                    @endif
                    
                    <div class="custom-file">
                        <input type="file" name="seal_file" id="seal_file" class="custom-file-input @error('seal_file') is-invalid @enderror">
                        <label class="custom-file-label" for="seal_file">{{ __('Choose file to replace existing') }}</label>
                        @error('seal_file')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                    <small class="form-text text-muted">{{ __('Leave empty to keep current file. Accepted file types: jpeg, png, jpg, gif, svg') }}</small>
                </div>
                
                <div class="form-group">
                    <div class="custom-control custom-switch">
                        <input type="checkbox" name="is_active" id="is_active" class="custom-control-input" value="1" {{ old('is_active', $companySeal->is_active) ? 'checked' : '' }}>
                        <label class="custom-control-label" for="is_active">{{ __('Active') }}</label>
                    </div>
                </div>
                
                <div class="form-group">
                    <button type="submit" class="btn btn-primary">{{ __('Update') }}</button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('script-page')
<script>
    // Update file input label with selected filename
    $(document).ready(function() {
        $('#seal_file').on('change', function() {
            var fileName = $(this).val().split('\\').pop();
            $(this).next('.custom-file-label').html(fileName);
        });
    });
</script>
@endpush
