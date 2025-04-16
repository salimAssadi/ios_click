@extends('tenant::layouts.master')

@section('content')
<div class="pc-container">
    <div class="pc-content">
        <div class="page-header">
            <div class="page-block">
                <div class="row align-items-center">
                    <div class="col-md-12">
                        <div class="page-header-title">
                            <h2 class="mb-0">{{ $report->title }}</h2>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5>{{ __('Report Details') }}</h5>
                            <div>
                                <a href="{{ route('reports.export', ['type' => $report->type, 'format' => 'pdf']) }}" class="btn btn-primary btn-sm">
                                    {{ __('Export PDF') }}
                                </a>
                                <a href="{{ route('reports.export', ['type' => $report->type, 'format' => 'excel']) }}" class="btn btn-success btn-sm ms-2">
                                    {{ __('Export Excel') }}
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        @switch($report->type)
                            @case('document')
                                @include('report::reports.partials.document', ['data' => $data])
                                @break
                            @case('audit')
                                @include('report::reports.partials.audit', ['data' => $data])
                                @break
                            @case('training')
                                @include('report::reports.partials.training', ['data' => $data])
                                @break
                            @case('risk')
                                @include('report::reports.partials.risk', ['data' => $data])
                                @break
                            @default
                                <p>{{ __('No data available for this report type.') }}</p>
                        @endswitch
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
