@extends('layouts.admin-app')
@section('page-title')
    {{ __('Create Policy') }}
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('iso_dic.dashboard') }}">{{ __('Dashboard') }}</a></li>
    <li class="breadcrumb-item"><a href="{{ route('iso_dic.policies.index') }}">{{ __('Policies') }}</a></li>
    <li class="breadcrumb-item">{{ __('Create') }}</li>
@endsection
@push('script-page')
    <script src="{{ asset('assets/js/plugins/tinymce/tinymce.min.js') }}"></script>
@endpush

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form method="POST" action="{{ route('iso_dic.policies.store') }}" enctype="multipart/form-data">
                        @csrf

                        <div class="row">
                            
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">{{ __('Name (Arabic)') }} <span class="text-danger">*</span></label>
                                    <input type="text" name="name_ar" class="form-control @error('name_ar') is-invalid @enderror"
                                        value="{{ old('name_ar') }}" required>
                                    @error('name_ar')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">{{ __('Name (English)') }} <span class="text-danger">*</span></label>
                                    <input type="text" name="name_en" class="form-control @error('name_en') is-invalid @enderror"
                                        value="{{ old('name_en') }}" required>
                                    @error('name_en')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">{{ __('Description (Arabic)') }}</label>
                                    <textarea name="description_ar" class="form-control @error('description_ar') is-invalid @enderror"
                                        rows="3">{{ old('description_ar') }}</textarea>
                                    @error('description_ar')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">{{ __('Description (English)') }}</label>
                                    <textarea name="description_en" class="form-control @error('description_en') is-invalid @enderror"
                                        rows="3">{{ old('description_en') }}</textarea>
                                    @error('description_en')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="form-label">{{ __('Content') }}</label>
                                    <textarea name="content" class="form-control summernote @error('content') is-invalid @enderror"
                                        rows="5">{{ old('content') }}</textarea>
                                    @error('content')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="form-label">{{ __('Attachments') }}</label>
                                    <input type="file" name="attachments[]" class="form-control @error('attachments.*') is-invalid @enderror"
                                        multiple accept=".pdf,.doc,.docx,.xls,.xlsx">
                                    @error('attachments.*')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                    <small class="text-muted">{{ __('Allowed file types: PDF, DOC, DOCX, XLS, XLSX. Max size: 10MB') }}</small>
                                </div>
                            </div>
                            <div class="form-group mb-3 col-md-6">
                                <label for="status">Status:</label>
                                <select id="status" name="status" class="form-control @error('status') is-invalid @enderror" required>
                                    <option value="draft" {{ old('status') == 'draft' ? 'selected' : '' }}>Draft</option>
                                    <option value="pending" {{ old('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                                    <option value="approved" {{ old('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                                    <option value="rejected" {{ old('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                                </select>
                                @error('status')
                                    <span class="invalid-feedback" role="alert">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="col-md-6 d-none">
                                <div class="form-group">
                                    <div class="form-check form-switch">
                                        <input type="checkbox" name="is_published" class="form-check-input" id="is_published"
                                            {{ old('is_published') ? 'checked' : '' }}>
                                        <label class="form-check-label" for="is_published">{{ __('Published') }}</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="text-end">
                            <button type="submit" class="btn btn-primary">{{ __('Create') }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
