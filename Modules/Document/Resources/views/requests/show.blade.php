@extends('tenant::layouts.app')

@section('page-title')
    {{ __('Request Details') }}
@endsection

@section('breadcrumb')
    <li class="breadcrumb-item">
        <a href="">{{ __('Dashboard') }}</a>
    </li>
    <li class="breadcrumb-item" aria-current="page">{{ __('Request Details') }}</li>
@endsection
@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header">
                    <h5>{{ __('Request Details') }}</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">{{ __('Document') }}</label>
                                <p>
                                    <a href="{{ route('tenant.document.show', $documentRequest->document_id) }}">
                                        {{ $documentRequest->document->title }}
                                    </a>
                                </p>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">{{ __('Request Type') }}</label>
                                <p>{{ $documentRequest->requestType->name }}</p>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">{{ __('Requested By') }}</label>
                                <p>{{ $documentRequest->creator->name }}</p>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">{{ __('Status') }}</label>
                                <p>{!! $documentRequest->status_badge !!}</p>
                            </div>
                        </div>
                        
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label class="form-label fw-bold">{{ __('Request Details') }}</label>
                                <p>{{ $documentRequest->notes }}</p>
                            </div>
                        </div>

                        @if($documentRequest->response)
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label class="form-label fw-bold">{{ __('Response') }}</label>
                                    <p>{{ $documentRequest->response }}</p>
                                </div>
                            </div>
                        @endif
                    </div>

                    @if(auth()->user()->can('manage_documents') && $documentRequest->status === 'pending')
                        <div class="mt-4">
                            <form action="{{ route('tenant.document.requests.update-status', $documentRequest->id) }}" method="POST">
                                @csrf
                                @method('PUT')
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="form-label">{{ __('Update Status') }} <span class="text-danger">*</span></label>
                                            <select name="status" class="form-control @error('status') is-invalid @enderror" required>
                                                <option value="">{{ __('Select Status') }}</option>
                                                <option value="in_progress">{{ __('In Progress') }}</option>
                                                <option value="approved">{{ __('Approve') }}</option>
                                                <option value="rejected">{{ __('Reject') }}</option>
                                            </select>
                                            @error('status')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label class="form-label">{{ __('Response') }} <span class="text-danger">*</span></label>
                                            <textarea name="response" rows="3" 
                                                      class="form-control @error('response') is-invalid @enderror" 
                                                      required>{{ old('response') }}</textarea>
                                            @error('response')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="mt-3">
                                    <button type="submit" class="btn btn-primary">
                                        {{ __('Update Status') }}
                                    </button>
                                </div>
                            </form>
                        </div>
                    @endif

                    <div class="mt-4">
                        <a href="{{ url()->previous() }}" class="btn btn-secondary">
                            {{ __('Back') }}
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
