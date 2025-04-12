@extends('layouts.admin-app')
@php
    $profile = asset(Storage::url('upload/profile/'));
@endphp
@section('page-title')
    {{ __('ISO Systems') }}
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item">
        <a href="{{ route('home') }}">{{ __('Dashboard') }}</a>
    </li>
    <li class="breadcrumb-item" aria-current="page">
        {{ __('Procedures') }}
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
                                {{ __('Procedures') }}
                            </h5>
                        </div>
                        <div class="col-auto">
                            <a href="{{ route('iso_dic.procedures.create') }}" class="btn btn-secondary">

                                <i class="ti ti-circle-plus align-text-bottom"></i>
                                {{ __('Create Procedure') }}
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body pt-0">
                    <div class="dt-responsive table-responsive">
                        <table class="table table-hover advance-datatable">
                            <thead>
                                <tr class="align-center">
                                    <th>{{ __('Procedure Name') }}</th>
                                    <th>{{ __('Procedure Description') }}</th>
                                    <th>{{ __('Attachments') }}</th>
                                    <th>{{ __('status') }}</th>
                                    <th>{{ __('Action') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($procedures as $procedure)
                                    <tr>
                                        <td>
                                            <div class="flex-grow-1 ms-3">
                                                <h5 class="mb-1">
                                                    {{ $procedure->procedure_name_ar }}

                                                </h5>
                                            </div>
                                        </td>



                                        <td>
                                            <div class="flex-grow-1 ms-3">
                                                <h5 class="mb-1">
                                                    {{ $procedure->description_ar }}

                                                </h5>
                                            </div>
                                        </td>



                                        <td>
                                            <div class="flex-grow-1 ms-3">
                                                @foreach ($procedure->attachments as $attachment)
                                                    <div class="d-flex align-items-center mb-1">
                                                        <a href="{{ route('iso_dic.procedures.attachments.download', $attachment->id) }}"
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
                                                @if ($procedure->status == 1)
                                                    <span class="d-inline badge text-bg-success">{{ __('publish') }}</span>
                                                @else
                                                    <span
                                                        class="d-inline badge text-bg-danger">{{ __('unpublish') }}</span>
                                                @endif
                                            </div>
                                        </td>
                                        <td>
                                            <div class="cart-action align-items-center">
                                                {!! Form::open(['method' => 'DELETE', 'route' => ['iso_dic.procedures.destroy', $procedure->id]]) !!}

                                                <a class="avtar avtar-xs btn-link-primary text-primary"
                                                    data-bs-toggle="tooltip"
                                                    data-bs-original-title="{{ __('Attachments') }}"
                                                    href="{{ route('iso_dic.procedures.configure', $procedure->id) }}"
                                                    data-title="{{ __('Edit User') }}">
                                                    <i class="ti ti-settings fs-2"></i>
                                                </a>
                                                <a class="avtar avtar-xs btn-link-secondary text-secondary"
                                                    href="{{ route('iso_dic.procedures.edit', $procedure->id) }}"
                                                    data-title="{{ __('Edit Procedure') }}">
                                                    <i class="ti ti-edit fs-2"></i>
                                                </a>
                                                <a class="avtar avtar-xs btn-link-danger text-danger confirm_dialog"
                                                    data-bs-toggle="tooltip" data-bs-original-title="{{ __('Detete') }}"
                                                    href="#"><i class="ti ti-trash fs-2"></i>

                                                </a>
                                                <a class="avtar avtar-xs btn-link-warning text-warning"
                                                    data-bs-toggle="tooltip" data-bs-original-title="{{ __('preview') }}"
                                                    href="{{ route('iso_dic.procedures.show', \Illuminate\Support\Facades\Crypt::encrypt($procedure->id)) }}"
                                                    data-title="{{ __('preview') }}">
                                                    <i class="ti ti-eye fs-2"></i>
                                                </a>
                                                {!! Form::close() !!}
                                            </div>

                                        </td>
                                    </tr>
                                @empty
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
