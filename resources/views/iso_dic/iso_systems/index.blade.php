@extends('layouts.admin-app')
@php
    $profile = asset(Storage::url('upload/profile/'));
@endphp
@section('page-title')
    {{ __('ISO Systems') }}
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item">
        <a href="">{{ __('Dashboard') }}</a>
    </li>
    <li class="breadcrumb-item" aria-current="page">{{ __('ISO Systems') }}</li>
@endsection

@section('content')
<div class="row">
    <div class="col-sm-12">
        <div class="card">
            <div class="card-header">
                <div class="row align-items-center g-3">
                    <div class="col">
                        <h4 class="mb-0">{{ __('ISO Systems') }}</h4>
                    </div>
                    <div class="col-auto">
                        <a href="#"  class="btn btn-secondary customModal" data-size="lg" data-url="{{ route('iso_dic.iso_systems.create') }}" data-title="{{ __('Create ISO System') }}">
                            <i class="ti ti-plus me-1"></i>
                            {{ __('Create ISO System') }}
                        </a>
                    </div>
                </div>
            </div>
            
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover table-striped">
                        <thead class="thead-light">
                            <tr>
                                <th>{{ __('image') }}</th>
                                <th>{{ __('name ar').'/'. __('name en')}}</th>
                                <th>{{ __('code') .'/'. __('version')}}</th>
                                <th class="w-15">{{ __('Documents') }}</th>
                                <th class="w-10">{{ __('status') }}</th>
                                <th class="w-15 text-end">{{ __('Action') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($iso_systems as $system)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="flex-shrink-0 wid-40">
                                                <img  class="img-radius img-fluid wid-40" src="{{ getISOImage(getFilePath('isoIcon').'/'. $system->image)}}" alt="@lang('Image')" >
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div>
                                                <h6 class="mb-0">{{ $system->name_ar }} ({{ $system->symbole }})</h6>
                                                <small class="text-body-secondary">{{ $system->name_en }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div>
                                                <h6 class="mb-0">{{ $system->code .'/'.$system->version }}</h6>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex gap-2">
                                            <span class="badge bg-info-subtle text-info">
                                                {{ __('Procedures') }}: {{ $system->procedures_count }}
                                            </span>
                                            <span class="badge bg-warning-subtle text-warning">
                                                {{ __('Samples') }}: {{ $system->samples_count }}
                                            </span>
                                        </div>
                                    </td>
                                    <td>
                                        @if ($system->status == 1)
                                            <span class="badge bg-success-subtle text-success">{{ __('publish') }}</span>
                                        @else
                                            <span class="badge bg-danger-subtle text-danger">{{ __('unpublish') }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="d-flex justify-content-end gap-2">
                                            {!! Form::open(['method' => 'DELETE', 'route' => ['iso_dic.iso_systems.destroy', Crypt::encrypt($system->id)], 'class' => 'd-inline']) !!}
                                            
                                            <a href="{{ route('iso_dic.iso_systems.show', Crypt::encrypt($system->id)) }}"
                                                class="btn btn-sm btn-icon btn-light-warning"
                                                data-bs-toggle="tooltip"
                                                data-bs-placement="top"
                                                title="{{ __('ISO Items') }}">
                                                <i class="ti ti-eye"></i>
                                            </a>
                                            <a href="#"
                                                class="btn btn-sm btn-icon btn-light-info customModal"
                                                data-url="{{ route('iso_dic.iso_systems.edit', Crypt::encrypt($system->id)) }}"
                                                data-title="{{ __('Edit ISO System') }}"
                                                data-size="lg">

                                                <i class="ti ti-edit"></i>
                                            </a>

                                            <button type="button"
                                                class="btn btn-sm btn-icon btn-light-danger confirm_dialog"
                                                data-bs-toggle="tooltip"
                                                data-bs-placement="top"
                                                title="{{ __('Delete') }}">
                                                <i class="ti ti-trash"></i>
                                            </button>

                                           
                                            {!! Form::close() !!}
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-4">
                                        <div class="d-flex flex-column align-items-center">
                                            <i class="ti ti-file-off text-secondary mb-2" style="font-size: 24px;"></i>
                                            <p class="text-body-secondary mb-0">{{ __('No ISO systems found') }}</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="d-flex justify-content-end mt-4">
                    {{ $iso_systems->links('pagination::bootstrap-5') }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
