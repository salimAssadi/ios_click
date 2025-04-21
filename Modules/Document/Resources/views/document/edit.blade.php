@extends('tenant::layouts.app')
@section('page-title', __('Edit Document'))
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('tenant.document.index') }}">{{ __('Documents') }}</a></li>
    <li class="breadcrumb-item active">{{ __('Edit Document') }}</li>
@endsection

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('tenant.document.update', $document) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="title">{{ __('Title') }} <span class="text-danger">*</span></label>
                                    <input type="text" 
                                           class="form-control @error('title') is-invalid @enderror" 
                                           id="title" 
                                           name="title" 
                                           value="{{ old('title', $document->title) }}" 
                                           required>
                                    @error('title')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="description">{{ __('Description') }}</label>
                                    <textarea class="form-control @error('description') is-invalid @enderror" 
                                              id="description" 
                                              name="description" 
                                              rows="3">{{ old('description', $document->description) }}</textarea>
                                    @error('description')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="file">{{ __('New Document File') }}</label>
                                    <input type="file" 
                                           class="form-control @error('file') is-invalid @enderror" 
                                           id="file" 
                                           name="file">
                                    <small class="form-text text-muted">
                                        {{ __('Leave empty to keep the current file') }}
                                    </small>
                                    @error('file')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="status">{{ __('Status') }}</label>
                                    <select class="form-control @error('status') is-invalid @enderror" 
                                            id="status" 
                                            name="status">
                                       @foreach ($statuses as $status )
                                           <option value="{{ $status->id }}" {{ old('status', $document->lastVersion->status_id) === $status->id ? 'selected' : '' }}>
                                               {{ $status->name }}
                                           </option>
                                       @endforeach
                                    </select>
                                    @error('status')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12">
                                <div class="form-group">
                                    <label for="version_notes">{{ __('Version Notes') }}</label>
                                    <textarea class="form-control @error('version_notes') is-invalid @enderror" 
                                              id="version_notes" 
                                              name="version_notes" 
                                              rows="3"
                                              placeholder="{{ __('Describe the changes made in this version') }}">{{ old('version_notes') }}</textarea>
                                    @error('version_notes')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="mt-4">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-1"></i> {{ __('Update Document') }}
                            </button>
                            <a href="{{ route('tenant.document.index') }}" class="btn btn-secondary">
                                <i class="fas fa-times me-1"></i> {{ __('Cancel') }}
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
