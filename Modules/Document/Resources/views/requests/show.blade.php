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
                        @if($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
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
                                <p>{!! $documentRequest->requestStatusBadge !!}</p>
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

                    @if($documentRequest->requestStatus->code === 'pending' && $documentRequest->requested_by != auth('tenant')->id() )
                        <div class="mt-4">
                            <form action="{{ route('tenant.document.requests.update-status', $documentRequest->id) }}" method="POST">
                                @csrf
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="form-label">{{ __('Status') }} <span class="text-danger">*</span></label>
                                            <select name="status" id="status" class="form-control  @error('status') is-invalid @enderror" required>
                                                <option value="">{{ __('Select Status') }}</option>
                                                @foreach($requestStatus as $status)
                                                    <option value="{{ $status->id }}" 
                                                            data-code="{{ $status->code }}"
                                                            {{ old('status') === $status->id ? 'selected' : '' }}>
                                                        {{ $status->name }}
                                                    </option>
                                                @endforeach
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

                                    @if($documentRequest->requestType->code === 'review')
                                    <div class="col-md-12 review-fields" style="display: none;">
                                        <div class="form-group">
                                            <label class="form-label">{{ __('Assigned To') }} <span class="text-danger">*</span></label>
                                            <select name="assigned_to[]" class="form-control hidesearch @error('assigned_to') is-invalid @enderror" multiple>
                                                @foreach($employees as $employee)
                                                <option value="{{ $employee->id }}" {{ (is_array(old('assigned_to')) && in_array($employee->id, old('assigned_to'))) ? 'selected' : '' }}>
                                                    {{ $employee->name }}
                                                </option>
                                                @endforeach
                                            </select>
                                            @error('assigned_to')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="form-group mt-3">
                                            <label class="form-label">{{ __('Request Details') }} <span class="text-danger">*</span></label>
                                            <textarea name="request_details" rows="3" 
                                                      class="form-control @error('request_details') is-invalid @enderror">{{ old('request_details') }}</textarea>
                                            @error('request_details')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    @endif

                                    <div class="col-md-12 mt-3">
                                        <button type="submit" class="btn btn-primary">{{ __('Update Status') }}</button>
                                    </div>
                                </div>
                            </form>

                            @push('script-page')
                            <script>
                                $(document).ready(function() {
                                

                                    $('#status').on('change', function() {
                                        var statusCode = $(this).find('option:selected').data('code');
                                        if (statusCode === 'approved') {
                                            $('.review-fields').slideDown();
                                            $('.review-fields select, .review-fields textarea').prop('required', true);
                                        } else {
                                            $('.review-fields').slideUp();
                                            $('.review-fields select, .review-fields textarea').prop('required', false);
                                        }
                                    });

                                    // Trigger change on page load if status is pre-selected
                                    $('#status').trigger('change');
                                });
                            </script>
                            @endpush
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
