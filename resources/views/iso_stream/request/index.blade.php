@extends('layouts.app')
@section('page-title')
    {{ __('Request') }}
@endsection

@section('breadcrumb')
    <li class="breadcrumb-item">
        <a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a>
    </li>
    <li class="breadcrumb-item active">
        <a href="#">{{ __('Request') }}</a>
    </li>
@endsection
@section('card-action-btn')
    @if (Gate::check('create request'))
        <a class="btn btn-secondary btn-sm ml-20 customModal" href="#" data-size="lg"
            data-url="{{ route('request.create') }}" data-title="{{ __('Create Request') }}"> <i
                class="ti-plus mr-5"></i>{{ __('Create Request') }}</a>
    @endif
@endsection
@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="row align-items-center g-2">
                        <div class="col">
                            <h5>
                                {{ __('Request') }}
                            </h5>
                        </div>
                        @if (Gate::check('create request'))
                            <div class="col-auto">
                                <a class="btn btn-secondary customModal" href="#" data-size="lg"
                                    data-url="{{ route('request.create') }}" data-title="{{ __('Create Request') }}">
                                    <i class="ti ti-circle-plus align-text-bottom"></i>{{ __('Create Request') }}</a>
                            </div>
                        @endif
                    </div>
                </div>
                <div class="card-body pt-0">
                    <div class="dt-responsive table-responsive">
                        <table class="table table-hover advance-datatable">
                            <thead>
                                <tr>
                                    <th>{{ __('Subject') }}</th>
                                    <th>{{ __('Request type') }}</th>
                                    <th>{{ __('Description') }}</th>
                                    <th>{{ __('Created By') }}</th>
                                    <th>{{ __('date') }}</th>
                                    <th>{{ __('Request status') }}</th>
                                    @if (Gate::check('edit request') || Gate::check('delete request') || Gate::check('show request'))
                                        <th class="text-right">{{ __('Action') }}</th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($requests as $request)
                                    <tr role="row">
                                        <td> {{ $request->subject }} </td>
                                        <td>{{ $request->request_type}}</td>
                                        <td> {{ $request->description }} </td>
                                        <td> {{ !empty($request->createdBy) ? $request->createdBy->name : '-' }} </td>
                                        <td>{{ dateFormat($request->created_date) }}</td>
                                        <td>
                                            <div class="flex-grow-1 ms-3">
                                                <span class="d-inline badge text-bg-{{ $request->status_badge_class }}">
                                                    {{ __($request->request_status) }}
                                                </span>
                                            </div>
                                        </td>
                                        
                                        
                                        @if (Gate::check('edit request') || Gate::check('delete request') || Gate::check('show request'))
                                            <td class="text-right">
                                                <div class="cart-action">
                                                    {!! Form::open(['method' => 'DELETE', 'route' => ['request.destroy', $request->id]]) !!}
                                                    @if (Gate::check('show request'))
                                                        <a class="avtar avtar-xs btn-link-warning text-warning customModal" data-size="lg"
                                                            data-bs-toggle="tooltip"
                                                            data-bs-original-title="{{ __('Show') }}" href="#"
                                                            data-url="{{ route('request.show', $request->id) }}"
                                                            data-title="{{ __('Details') }}"> <i
                                                                data-feather="eye"></i></a>
                                                    @endif
                                                    @if (Gate::check('edit request'))
                                                        <a class="avtar avtar-xs btn-link-secondary text-secondary customModal" data-size="lg"
                                                            data-bs-toggle="tooltip"
                                                            data-bs-original-title="{{ __('Edit') }}" href="#"
                                                            data-url="{{ route('request.edit', $request->id) }}"
                                                            data-title="{{ __('Edit Request') }}"> <i
                                                                data-feather="edit"></i></a>
                                                    @endif
                                                    @if (Gate::check('delete request'))
                                                        <a class=" avtar avtar-xs btn-link-danger text-danger confirm_dialog" data-bs-toggle="tooltip"
                                                            data-bs-original-title="{{ __('Detete') }}" href="#"> <i
                                                                data-feather="trash-2"></i></a>
                                                    @endif
                                                    {!! Form::close() !!}
                                                </div>
                                            </td>
                                        @endif
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
