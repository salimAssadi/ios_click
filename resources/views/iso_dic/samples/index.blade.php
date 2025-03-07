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
        {{ __('Samples') }}

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
                                {{ __('Samples') }}
                            </h5>
                        </div>
                        <div class="col-auto">
                            <a href="#" class="btn btn-secondary customModal" data-size="lg"
                                data-url="{{ route('iso_dic.samples.create') }}" data-title="{{ __('Create Sample') }}">

                                <i class="ti ti-circle-plus align-text-bottom"></i>
                                {{ __('Create Sample') }}
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body pt-0">
                    <div class="dt-responsive table-responsive">
                        <table class="table table-hover advance-datatable">
                            <thead>
                                <tr class="align-center">
                                    <th>{{ __('Sample Name') }}</th>
                                    <th>{{ __('Sample Description') }}</th>
                                    <th>{{ __('Required Sample') }}</th>
                                    <th>{{ __('status') }}</th>
                                    <th>{{ __('Action') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($samples as $sample)
                                    <tr>
                                        <td>
                                            <div class="flex-grow-1 ms-3">
                                                <h5 class="mb-1">
                                                    {{ $sample->sample_name }}

                                                </h5>
                                            </div>
                                        </td>



                                        <td>
                                            <div class="flex-grow-1 ms-3">
                                                <h5 class="mb-1">
                                                    {{ $sample->description }}

                                                </h5>
                                            </div>
                                        </td>


                                        <td>
                                            <div class="flex-grow-1 ms-3">
                                                @if ($sample->is_optional == 1)
                                                    <span class="d-inline badge  text-bg-danger px-4">{{ __('NO') }}</span>
                                                @else
                                                    <span class="d-inline badge text-bg-success px-3">{{ __('Yes') }}</span>
                                                @endif
                                            </div>
                                        </td>
                                        
                                        <td>
                                            <div class="flex-grow-1 ms-3">
                                                @if ($sample->status == 1)
                                                    <span class="d-inline badge text-bg-success">{{ __('publish') }}</span>
                                                @else
                                                    <span
                                                        class="d-inline badge text-bg-danger">{{ __('unpublish') }}</span>
                                                @endif
                                            </div>
                                        </td>
                                        <td>
                                            <div class="cart-action align-items-center">
                                                {!! Form::open(['method' => 'DELETE', 'route' => ['iso_dic.samples.destroy', $sample->id]]) !!}

                                                <a class="avtar avtar-xs btn-link-primary text-primary"
                                                    data-bs-toggle="tooltip"
                                                    data-bs-original-title="{{ __('Attachments') }}"
                                                    href="{{ route('iso_dic.samples.configure', $sample->id) }}"
                                                    data-title="{{ __('Edit User') }}">
                                                    <i class="ti ti-settings fs-2"></i>
                                                </a>
                                                <a class="avtar avtar-xs btn-link-secondary text-secondary customModal"
                                                    data-bs-toggle="tooltip" data-size="lg"
                                                    data-bs-original-title="{{ __('Edit') }}" href="#"
                                                    data-url="{{ route('iso_dic.samples.edit', $sample->id) }}"
                                                    data-title="{{ __('Edit Procedure') }}">
                                                    <i class="ti ti-edit fs-2"></i>
                                                </a>
                                                <a class="avtar avtar-xs btn-link-danger text-danger confirm_dialog"
                                                    data-bs-toggle="tooltip" data-bs-original-title="{{ __('Detete') }}"
                                                    href="#"><i class="ti ti-trash fs-2"></i>

                                                </a>
                                                <a class="avtar avtar-xs btn-link-warning text-warning"
                                                    data-bs-toggle="tooltip" data-bs-original-title="{{ __('preview') }}"
                                                    href="{{ route('iso_dic.samples.show', \Illuminate\Support\Facades\Crypt::encrypt($sample->id)) }}"
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
