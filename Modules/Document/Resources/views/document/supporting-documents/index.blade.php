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
                            <i class="ti ti-plus me-1"></i>{{ __('Create Supporting Document') }}
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <x-document::documents-table 
                            documentType="supporting" 
                            title="{{ __('Supporting Documents List') }}" 
                            relatedProcess="Modules\Document\Entities\SupportingDocument" 
                            :categoryId="$category_id"
                            :customColumns="$customColumns"
                            :filters="$filters"
                        />
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
