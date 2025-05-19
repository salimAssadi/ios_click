@extends('tenant::layouts.app')

@section('page-title', __('Private Procedures'))
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('tenant.document.index') }}">{{ __('Documents') }}</a></li>
    <li class="breadcrumb-item active">{{ __('Private Procedures') }}</li>
@endsection

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div class="col">
                        <h4 class="mb-0">{{ __('Private Procedures') }}</h4>
                      
                    </div>
                    <div class="col-auto">
                        {{ Form::open(['route' => 'tenant.document.procedures.create', 'method' => 'post']) }}
                        {{ Form::hidden('category_id', $category_id) }}
                        {{ Form::submit(__('Create Private Procedure'), ['class' => 'btn btn-primary']) }}
                        {{ Form::close() }}
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                       <x-document::documents-table 
                       documentType="procedure" 
                       title="{{ __('Private Procedure List') }}" 
                       relatedProcess="Modules\Document\Entities\IsoSystemProcedure" 
                       :categoryId=$category_id
                       :customColumns="$customColumns"
                       :filters="$filters" 
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
        $('#private-procedures-table').DataTable({
          
        });
    });
</script>
@endpush