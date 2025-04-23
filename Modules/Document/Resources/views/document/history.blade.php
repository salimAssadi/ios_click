@extends('layouts.app')

@section('title', __('Document History'))

@section('content')
<div class="page-header d-print-none">
    <div class="container-xl">
        <div class="row g-2 align-items-center">
            <div class="col">
                <h2 class="page-title">
                    {{ __('Document History') }}: {{ $document->title }}
                </h2>
            </div>
        </div>
    </div>
</div>
<div class="page-body">
    <div class="container-xl">
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-vcenter card-table">
                        <thead>
                            <tr>
                                <th>{{ __('Date') }}</th>
                                <th>{{ __('Version') }}</th>
                                <th>{{ __('Action') }}</th>
                                <th>{{ __('Performed By') }}</th>
                                <th>{{ __('Notes') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($history as $log)
                            <tr>
                                <td>{{ $log->created_at->format('Y-m-d H:i') }}</td>
                                <td>v{{ $log->version->version ?? 'N/A' }}</td>
                                <td>{{ $log->action_type }}</td>
                                <td>{{ $log->performer->name }}</td>
                                <td>
                                    <div>{{ $log->change_summary }}</div>
                                    @if($log->notes)
                                        <small class="text-muted">{{ $log->notes }}</small>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                {{ $history->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
