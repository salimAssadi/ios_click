@extends('tenant::layouts.app')

@section('page-title', __('Public Procedures'))
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('tenant.document.index') }}">{{ __('Documents') }}</a></li>
    <li class="breadcrumb-item active">{{ __('Public Procedures') }}</li>
@endsection
@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div class="col">
                        <h5 class="mb-0">{{ __('Public Procedures') }}</h5>
                    </div>
                    <div class="col-auto">
                        <div class="col-auto">
                            {{ Form::open(['route' => 'tenant.document.procedures.create', 'method' => 'post']) }}
                            {{ Form::hidden('category_id', $category_id) }}
                            {{ Form::submit(__('Create Public Procedure'), ['class' => 'btn btn-primary']) }}
                            {{ Form::close() }}
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <x-document::documents-table 
                        documentType="procedure" 
                        title="{{ __('Public Procedure List') }}" 
                        relatedProcess="Modules\Document\Entities\Procedure" 
                        :categoryId="2"
                        :customColumns="$customColumns"
                      />
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script-page')
<script>
    $(document).ready(function() {
        $('#public-procedures-table').DataTable({
            // Configuration options
            // language: {
            //     url: '{{ asset("js/datatable-" . app()->getLocale() . ".json") }}'
            // }
        });
    });
</script>
@endpush
