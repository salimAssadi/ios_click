@extends('layouts.app')
@section('page-title')
    {{ __('Edit Instruction') }}
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('iso_dic.dashboard') }}">{{ __('Dashboard') }}</a></li>
    <li class="breadcrumb-item"><a href="{{ route('iso_dic.instructions.index') }}">{{ __('Instructions') }}</a></li>
    <li class="breadcrumb-item">{{ __('Edit') }}</li>
@endsection
@push('script-page')
    <script src="{{ asset('assets/js/plugins/tinymce/tinymce.min.js') }}"></script>
@endpush

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form method="POST" action="{{ route('iso_dic.instructions.update', $instruction->id) }}" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">{{ __('Name (Arabic)') }} <span class="text-danger">*</span></label>
                                    <input type="text" name="name_ar" class="form-control @error('name_ar') is-invalid @enderror"
                                        value="{{ old('name_ar', $instruction->name_ar) }}" required>
                                    @error('name_ar')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">{{ __('Name (English)') }} <span class="text-danger">*</span></label>
                                    <input type="text" name="name_en" class="form-control @error('name_en') is-invalid @enderror"
                                        value="{{ old('name_en', $instruction->name_en) }}" required>
                                    @error('name_en')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="form-label">{{ __('Description (Arabic)') }}</label>
                                    <textarea name="description_ar" class="form-control @error('description_ar') is-invalid @enderror"
                                        rows="3">{{ old('description_ar', $instruction->description_ar) }}</textarea>
                                    @error('description_ar')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="form-label">{{ __('Description (English)') }}</label>
                                    <textarea name="description_en" class="form-control @error('description_en') is-invalid @enderror"
                                        rows="3">{{ old('description_en', $instruction->description_en) }}</textarea>
                                    @error('description_en')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-12 mb-3 ">
                                <div class="form-group border p-3">
                                    <label class="form-label">{{ __('Related Procedures') }}</label>
                                    <div class="row">
                                        @foreach($procedures as $procedure)
                                            <div class="col-md-4 mb-2">
                                                <div class="form-check">
                                                    <input type="checkbox" name="procedures[]" value="{{ $procedure->id }}"
                                                        class="form-check-input @error('procedures') is-invalid @enderror"
                                                        id="procedure_{{ $procedure->id }}"
                                                        {{ in_array($procedure->id, old('procedures', $instruction->procedures->pluck('id')->toArray())) ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="procedure_{{ $procedure->id }}">
                                                        {{ $procedure->procedure_name }}
                                                    </label>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                    @error('procedures')
                                        <span class="invalid-feedback d-block">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="form-label">{{ __('Content') }}</label>
                                    <textarea name="content" class="form-control summernote @error('content') is-invalid @enderror"
                                        rows="5">{{ old('content', $instruction->content) }}</textarea>
                                    @error('content')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="form-label">{{ __('Current Attachments') }}</label>
                                    <div class="attachment-list">
                                        @foreach($instruction->attachments as $attachment)
                                            <div class="attachment-item d-flex align-items-center mb-2">
                                                <a href="{{ route('iso_dic.instructions.attachments.download', $attachment->id) }}"
                                                    class="text-primary me-2">
                                                    {{ $attachment->original_name }}
                                                </a>
                                                <form method="POST"
                                                    action="{{ route('iso_dic.instructions.attachments.destroy', $attachment->id) }}"
                                                    class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-danger show_confirm"
                                                        data-bs-toggle="tooltip" title="{{ __('Delete') }}">
                                                        <i class="ti ti-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="form-label">{{ __('Add New Attachments') }}</label>
                                    <input type="file" name="attachments[]" class="form-control @error('attachments.*') is-invalid @enderror"
                                        multiple accept=".pdf,.doc,.docx,.xls,.xlsx">
                                    @error('attachments.*')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                    <small class="text-muted">{{ __('Allowed file types: PDF, DOC, DOCX, XLS, XLSX. Max size: 10MB') }}</small>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <div class="form-check form-switch">
                                        <input type="checkbox" name="is_published" class="form-check-input" id="is_published"
                                            {{ old('is_published', $instruction->is_published) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="is_published">{{ __('Published') }}</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="text-end">
                            <button type="submit" class="btn btn-primary">{{ __('Update') }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            $('.select2').select2();
        });
    </script>
@endpush
