@extends('layouts.admin-app')
@section('page-title')
    {{ __('Edit Reference') }}
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('iso_dic.dashboard') }}">{{ __('Dashboard') }}</a></li>
    <li class="breadcrumb-item"><a href="{{ route('iso_dic.references.index') }}">{{ __('References') }}</a></li>
    <li class="breadcrumb-item">{{ __('Edit Reference') }}</li>
@endsection

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('iso_dic.references.update', $reference->id) }}" method="POST"
                        enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label" for="name_ar">{{ __('Reference Name (Arabic)') }} <span
                                            class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('name_ar') is-invalid @enderror"
                                        id="name_ar" name="name_ar" value="{{ old('name_ar', $reference->name_ar) }}"
                                        required>
                                    @error('name_ar')
                                        <span class="invalid-feedback" role="alert">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label" for="name_en">{{ __('Reference Name (English)') }} <span
                                            class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('name_en') is-invalid @enderror"
                                        id="name_en" name="name_en" value="{{ old('name_en', $reference->name_en) }}"
                                        required>
                                    @error('name_en')
                                        <span class="invalid-feedback" role="alert">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row mt-3">
                            <div class="col-12">
                                <label class="form-label">{{ __('ISO Systems') }} <span
                                        class="text-danger">*</span></label>
                                <div class="row">
                                    @foreach ($isoSystems as $system)
                                        <div class="col-md-3 mb-2">
                                            <div class="form-check">
                                                <input type="checkbox"
                                                    class="form-check-input @error('iso_systems') is-invalid @enderror"
                                                    name="iso_systems[]" value="{{ $system->id }}"
                                                    id="system_{{ $system->id }}"
                                                    {{ in_array($system->id, old('iso_systems', $reference->isoSystems->pluck('id')->toArray())) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="system_{{ $system->id }}">
                                                    {{ $system->name }}
                                                </label>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                                @error('iso_systems')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        @if ($reference->attachments->count() > 0)
                            <div class="row mt-3">
                                <div class="col-12">
                                    <label class="form-label">{{ __('Current Attachments') }}</label>
                                    <div class="table-responsive">
                                        <table class="table table-bordered">
                                            <thead>
                                                <tr>
                                                    <th>{{ __('File Name') }}</th>
                                                    <th class="text-end">{{ __('Action') }}</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($reference->attachments as $attachment)
                                                    <tr>
                                                        <td>
                                                            <a href="{{ route('iso_dic.references.attachments.download', $attachment->id) }}"
                                                                class="text-primary">
                                                                {{ $attachment->original_name }}
                                                            </a>
                                                        </td>
                                                        <td class="text-end">
                                                            <form
                                                                action="{{ route('iso_dic.references.attachments.destroy', $attachment->id) }}"
                                                                method="POST" class="d-inline">
                                                                @csrf
                                                                @method('DELETE')
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

                        <div class="row mt-3">
                            <div class="col-12">
                                <div class="form-group">
                                    <label class="form-label" for="attachments">{{ __('New Attachments') }}</label>
                                    <input type="file" class="form-control @error('attachments.*') is-invalid @enderror"
                                        id="attachments" name="attachments[]" multiple>
                                    <small class="form-text text-muted">{{ __('Supported formats') }}: PDF, DOC, DOCX, XLS,
                                        XLSX. {{ __('Max size') }}: 10MB</small>
                                    @error('attachments.*')
                                        <span class="invalid-feedback" role="alert">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row mt-3">
                            <div class="col-12">
                                <div class="form-check form-switch">
                                    <input type="checkbox" class="form-check-input" id="is_published" name="is_published"
                                        value="1"
                                        {{ old('is_published', $reference->is_published) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_published">{{ __('Published') }}</label>
                                </div>
                            </div>
                        </div>

                        <div class="row mt-4">
                            <div class="col-12">
                                <button type="submit" class="btn btn-primary">{{ __('Update') }}</button>
                                <a href="{{ route('iso_dic.references.index') }}"
                                    class="btn btn-secondary">{{ __('Cancel') }}</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
