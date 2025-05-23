@extends('tenant::layouts.app')

@section('page-title')
    {{ __('My Requests') }}
@endsection

@section('breadcrumb')
    <li class="breadcrumb-item">
        <a href="">{{ __('Dashboard') }}</a>
    </li>
    <li class="breadcrumb-item" aria-current="page">{{ __('My Requests') }}</li>
@endsection
@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header">
                    <h5>{{ __('My Requests') }}</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>{{ __('ID') }}</th>
                                    <th>{{ __('Document') }}</th>
                                    <th>{{ __('Request Type') }}</th>
                                    <th>{{ __('Status') }}</th>
                                    <th>{{ __('Created At') }}</th>
                                    <th class="text-right">{{ __('Actions') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($requests as $request)
                                    <tr>
                                        <td>{{ $request->id }}</td>
                                        <td>
                                            <a href="{{ route('tenant.document.show', $request->document_id) }}">
                                                {{ $request->document->title }}
                                            </a>
                                        </td>
                                        <td>{{ $request->requestType->name }}</td>
                                        <td>{!! $request->status_badge !!}</td>
                                        <td>{{ $request->created_at->format('Y-m-d') }}</td>
                                        <td class="text-right">
                                            <a href="{{ route('tenant.document.requests.show', $request->id) }}" 
                                               class="btn btn-info btn-sm">
                                                <i class="ti ti-eye"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center">{{ __('No requests found') }}</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="mt-4">
                        {{ $requests->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
