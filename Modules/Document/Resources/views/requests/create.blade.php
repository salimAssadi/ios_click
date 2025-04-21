@extends('tenant::layouts.app')


@section('page-title')
    {{ __('Create Document Request') }}
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item">
        <a href="">{{ __('Dashboard') }}</a>
    </li>
    <li class="breadcrumb-item" aria-current="page">{{ __('Create Document Request') }}</li>
@endsection
@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header">
                    <h5>{{ __('Create Document Request') }}</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('tenant.document.requests.store') }}" method="POST">
                        @csrf
                        <input type="hidden" name="document_id" value="{{ $document->id }}">
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">{{ __('Document') }}</label>
                                    <input type="text" class="form-control" value="{{ $document->title }}" readonly>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">{{ __('Request Type') }} <span class="text-danger">*</span></label>
                                    <select name="request_type_id" class="form-control showsearch @error('request_type_id') is-invalid @enderror" required>
                                        <option value="">{{ __('Select Request Type') }}</option>
                                        @foreach($requestTypes as $type)
                                            <option value="{{ $type->id }}" {{ old('request_type_id') == $type->id ? 'selected' : '' }}>
                                                {{ $type->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('request_type_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="form-label">{{ __('Request Details') }} <span class="text-danger">*</span></label>
                                    <textarea name="request_details" rows="5" 
                                              class="form-control @error('request_details') is-invalid @enderror" 
                                              required>{{ old('request_details') }}</textarea>
                                    @error('request_details')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="mt-4">
                            <button type="submit" class="btn btn-primary">
                                {{ __('Submit Request') }}
                            </button>
                            <a href="{{ route('tenant.document.show', $document->id) }}" class="btn btn-secondary">
                                {{ __('Cancel') }}
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
