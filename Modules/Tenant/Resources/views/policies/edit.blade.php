@extends('layouts.admin-app')
@section('page-title')
    {{ __('Edit Policy') }}
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('iso_dic.dashboard') }}">{{ __('Dashboard') }}</a></li>
    <li class="breadcrumb-item"><a href="{{ route('iso_dic.policies.index') }}">{{ __('Policies') }}</a></li>
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
                    <form method="POST" action="{{ route('iso_dic.policies.update', $policy->id) }}"
                        enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">{{ __('Name (Arabic)') }} <span
                                            class="text-danger">*</span></label>
                                    <input type="text" name="name_ar"
                                        class="form-control @error('name_ar') is-invalid @enderror"
                                        value="{{ old('name_ar', $policy->name_ar) }}" required>
                                    @error('name_ar')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">{{ __('Name (English)') }} <span
                                            class="text-danger">*</span></label>
                                    <input type="text" name="name_en"
                                        class="form-control @error('name_en') is-invalid @enderror"
                                        value="{{ old('name_en', $policy->name_en) }}" required>
                                    @error('name_en')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">{{ __('Description (Arabic)') }}</label>
                                    <textarea name="description_ar" class="form-control @error('description_ar') is-invalid @enderror" rows="3">{{ old('description_ar', $policy->description_ar) }}</textarea>
                                    @error('description_ar')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">{{ __('Description (English)') }}</label>
                                    <textarea name="description_en" class="form-control @error('description_en') is-invalid @enderror" rows="3">{{ old('description_en', $policy->description_en) }}</textarea>
                                    @error('description_en')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="form-label">{{ __('Content') }}</label>
                                    <textarea name="content" class="form-control summernote @error('content') is-invalid @enderror" rows="5">{!! old('content', $policy->content) !!}</textarea>
                                    @error('content')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            @if ($policy->attachments->count() > 0)
                                <div class="row mt-3">
                                    <div class="col-12">
                                        <label class="form-label">{{ __('Current Attachments')}}</label>
                                        <div class="table-responsive">
                                            <table class="table table-bordered">
                                                <thead>
                                                    <tr>
                                                        <th>{{ __('File Name') }}</th>
                                                        <th class="text-end">{{ __('Action') }}</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach ($policy->attachments as $attachment)
                                                        <tr>
                                                            <td>
                                                                <a href="{{ route('iso_dic.policies.attachments.download', $attachment->id) }}"
                                                                    class="text-primary">
                                                                    {{ $attachment->original_name }}
                                                                </a>
                                                            </td>
                                                            <td class="text-end" style="width: 40px;">
                                                                <form
                                                                    action="{{ route('iso_dic.policies.attachments.destroy', $attachment->id) }}"
                                                                    method="POST" class="d-inline">
                                                                    @csrf
                                                                    {{-- @method('DELETE') --}}
                                                                    <a class="avtar avtar-xs btn-link-danger text-danger confirm_dialog"
                                                                        data-bs-toggle="tooltip"
                                                                        data-bs-original-title="{{ __('Delete') }}"
                                                                        href="#"><i class="ti ti-trash fs-2"></i>
                                                                    </a>
                                                                </form>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            @endif
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">{{ __('Add New Attachments') }}</label>
                                    <input type="file" name="attachments[]"
                                        class="form-control @error('attachments.*') is-invalid @enderror" multiple
                                        accept=".pdf,.doc,.docx,.xls,.xlsx">
                                    @error('attachments.*')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                    <small
                                        class="text-muted">{{ __('Allowed file types: PDF, DOC, DOCX, XLS, XLSX. Max size: 10MB') }}</small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">{{ __('Status') }}</label>
                                    <select id="status" name="status" class="form-control @error('status') is-invalid @enderror" required>
                                        <option value="draft" {{ $policy->status == 'draft' ? 'selected' : '' }}>Draft</option>
                                        <option value="pending" {{ $policy->status == 'pending' ? 'selected' : '' }}>Pending</option>
                                    <option value="approved" {{ $policy->status == 'approved' ? 'selected' : '' }}>Approved</option>
                                    <option value="rejected" {{ $policy->status == 'rejected' ? 'selected' : '' }}>Rejected</option>
                                </select>
                                @error('status')
                                    <span class="invalid-feedback" role="alert">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="col-md-12 d-none">
                                <div class="form-group">
                                    <div class="form-check form-switch">
                                        <input type="checkbox" name="is_published" class="form-check-input"
                                            id="is_published"
                                            {{ old('is_published', $policy->is_published) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="is_published">{{ __('Published') }}</label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="text-end">
                            <a href="{{ route('iso_dic.policies.index') }}" class="btn btn-primary">{{ __('Cancel') }}</a>
                            <button type="submit" class="btn btn-secondary">{{ __('Update') }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
