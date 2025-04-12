@extends('layouts.admin-app')

@section('page-title')
    {{ __('ISO References') }}
@endsection

@section('breadcrumb')
    <li class="breadcrumb-item">
        <a href="{{ route('home') }}">{{ __('Dashboard') }}</a>
    </li>
    <li class="breadcrumb-item" aria-current="page">
        {{ __('ISO References') }}
    </li>
@endsection

@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="card table-card">
                <div class="card-header">
                    <div class="row align-items-center g-2">
                        <div class="col">
                            <h5>
                                {{ __('ISO References') }}
                            </h5>
                        </div>
                        <div class="col-auto">
                            <a href="{{ route('iso_dic.references.create') }}" class="btn btn-secondary">
                                <i class="ti ti-circle-plus align-text-bottom"></i>
                                {{ __('Create Reference') }}
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body pt-0">
                    <div class="dt-responsive table-responsive">
                        <table class="table table-hover basic-datatable">
                            <thead>
                                <tr class="align-center">
                                    <th>{{ __('Reference Name (AR/EN)') }}</th>
                                    <th>{{ __('ISO Systems') }}</th>
                                    <th>{{ __('Attachments') }}</th>
                                    <th>{{ __('Status') }}</th>
                                    <th>{{ __('Action') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($references as $reference)
                                    <tr>
                                        <td>
                                            <div class="flex-grow-1 ms-3">
                                                <h5 class="mb-1">{{ $reference->name_ar }}</h5>
                                                <p>{{ $reference->name_en }}</p>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="flex-grow-1 ms-3">
                                                <div class="d-flex flex-wrap gap-1">
                                                    @foreach($reference->isoSystems as $system)
                                                        <span class="badge bg-primary">
                                                            {{ $system->name_ar }} ({{ $system->symbole }})
                                                        </span>
                                                    @endforeach
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="flex-grow-1 ms-3">
                                                @foreach($reference->attachments as $attachment)
                                                    <div class="d-flex align-items-center mb-1">
                                                        <a href="{{ route('iso_dic.references.attachments.download', $attachment->id) }}" 
                                                           class="btn btn-sm btn-light me-2">
                                                            <i class="ti ti-download"></i>
                                                        </a>
                                                        <span>{{ $attachment->original_name }}</span>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </td>
                                        <td>
                                            <div class="flex-grow-1 ms-3">
                                                @if ($reference->is_published)
                                                    <span class="badge text-bg-success">{{ __('Published') }}</span>
                                                @else
                                                    <span class="badge text-bg-danger">{{ __('Unpublished') }}</span>
                                                @endif
                                            </div>
                                        </td>
                                        <td>
                                            <div class="cart-action align-items-center">
                                                {!! Form::open(['method' => 'DELETE', 'route' => ['iso_dic.references.destroy', $reference->id]]) !!}
                                                <a class="btn btn-secondary "
                                                    href="{{ route('iso_dic.references.edit', $reference->id) }}">
                                                    {{ __('Edit') }}
                                                </a>
                                                <button type="submit" class="btn btn-danger show_confirm">
                                                    {{ __('Delete') }}
                                                </button>
                                                {!! Form::close() !!}
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                  
                                @endforelse
                            </tbody>
                        </table>
                        <div class="d-flex justify-content-center mt-3">
                            {{ $references->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
