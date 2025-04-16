@extends('tenant::layouts.master')

@section('content')
<div class="pc-container">
    <div class="pc-content">
        <div class="page-header">
            <div class="page-block">
                <div class="row align-items-center">
                    <div class="col-md-12">
                        <div class="page-header-title">
                            <h2 class="mb-0">{{ __('Reports & Analytics') }}</h2>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            @foreach($reports as $type => $report)
            <div class="col-sm-6 col-xl-3">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-grow-1">
                                <h5 class="mb-2">{{ $report['title'] }}</h5>
                                <p class="text-muted mb-0">{{ $report['description'] }}</p>
                                <div class="mt-3">
                                    <a href="{{ route('reports.show', $type) }}" class="btn btn-primary btn-sm">
                                        {{ __('View Report') }}
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>
@endsection
