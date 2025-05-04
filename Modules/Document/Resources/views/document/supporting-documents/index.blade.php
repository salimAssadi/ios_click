@extends('tenant::layouts.app')

@section('page-title')
    {{ __('Supporting Documents') }}
@endsection

@section('breadcrumb')
    <li class="breadcrumb-item">
        <a href="{{ route('tenant.dashboard') }}">{{ __('Dashboard') }}</a>
    </li>
    <li class="breadcrumb-item">
        <a href="{{ route('tenant.document.index') }}">{{ __('Documents') }}</a>
    </li>
    <li class="breadcrumb-item active">
        <a href="#">{{ __('Supporting Documents') }}</a>
    </li>
@endsection

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">{{ __('Supporting Documents') }}</h5>
                    <div>
                        <a href="{{ route('tenant.document.supporting-documents.create') }}" class="btn btn-primary btn-sm">
                            <i class="ti ti-plus me-1"></i>{{ __('New Supporting Document') }}
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover table-striped">
                            <thead class="thead-light">
                                <tr>
                                    <th>{{ __('N') }}</th>
                                    <th>{{ __('Document Title') }}</th>
                                    <th>{{ __('Category') }}</th>
                                    <th>{{ __('File Size') }}</th>
                                    <th>{{ __('Created') }}</th>
                                    <th class="text-end">{{ __('Action') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($supportingDocuments as $document)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div>
                                                    <h6 class="mb-0">{{ $document->title }}</h6>
                                                    @if ($document->description)
                                                        <small class="text-muted">{{ Str::limit($document->description, 50) }}</small>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            @if ($document->category)
                                                {{ $document->category->title }}
                                            @else
                                                <span class="text-muted">{{ __('None') }}</span>
                                            @endif
                                        </td>
                                        <td>
                                            {{ number_format($document->file_size / 1024, 2) }} KB
                                        </td>
                                        <td>
                                            {{ $document->created_at->format('Y-m-d') }}
                                        </td>
                                        <td>
                                            <div class="d-flex justify-content-end gap-2">
                                                <a href="{{ route('tenant.document.supporting-documents.download', $document->id) }}" 
                                                   class="btn btn-sm btn-icon btn-light-primary" 
                                                   data-bs-toggle="tooltip" 
                                                   data-bs-placement="top" 
                                                   title="{{ __('Download') }}">
                                                    <i class="ti ti-download"></i>
                                                </a>
                                                
                                                <a href="{{ route('tenant.document.supporting-documents.show', $document->id) }}" 
                                                   class="btn btn-sm btn-icon btn-light-warning" 
                                                   data-bs-toggle="tooltip" 
                                                   data-bs-placement="top" 
                                                   title="{{ __('View') }}">
                                                    <i class="ti ti-eye"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center py-4">
                                            <div class="d-flex flex-column align-items-center">
                                                <i class="ti ti-file-off text-secondary mb-2" style="font-size: 24px;"></i>
                                                <p class="text-body-secondary mb-0">{{ __('No supporting documents found') }}</p>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="d-flex justify-content-end mt-4">
                        {{ $supportingDocuments->appends(request()->except('page'))->links('pagination::bootstrap-5') }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
