@extends('tenant::layouts.app')

@section('page-title', __('Main Procedures'))
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('tenant.document.index') }}">{{ __('Documents') }}</a></li>
    <li class="breadcrumb-item active">{{ __('Main Procedures') }}</li>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">{{ __('Main Procedures') }}</h5>
            </div>
            <div class="card-body">
                <!-- Tabs -->
                <ul class="nav nav-tabs" id="procedureTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="original-tab" data-bs-toggle="tab" data-bs-target="#original"
                            type="button" role="tab" aria-controls="original" aria-selected="true">
                            {{ __('Original Copy') }}
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="used-tab" data-bs-toggle="tab" data-bs-target="#used"
                            type="button" role="tab" aria-controls="used" aria-selected="false">
                            {{ __('Used Copy') }}
                        </button>
                    </li>
                </ul>
                
                <div class="tab-content pt-3" id="procedureTabsContent">
                    <!-- Original Copy Tab -->
                    <div class="tab-pane fade show active" id="original" role="tabpanel" aria-labelledby="original-tab">
                        @include('tenant::document.procedures._procedure_table', ['procedures' => $procedures->where('is_original', 1)])
                    </div>

                    <!-- Used Copy Tab -->
                    <div class="tab-pane fade" id="used" role="tabpanel" aria-labelledby="used-tab">
                        @include('tenant::document.procedures._procedure_table', ['procedures' => $procedures->where('is_original', 0)])
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
