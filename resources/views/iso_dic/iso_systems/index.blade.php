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
        {{ __('ISO Systems') }}

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
                                {{ __('ISO Systems') }}
                            </h5>
                        </div>
                        <div class="col-auto">
                            <a href="#" class="btn btn-secondary customModal" data-size="lg"
                                data-url="{{ route('iso_dic.iso_systems.create') }}" data-title="{{ __('Create ISO System') }}">

                                <i class="ti ti-circle-plus align-text-bottom"></i>
                                {{ __('Create ISO System') }}
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body pt-0">
                    <div class="dt-responsive table-responsive">
                        <table class="table table-hover advance-datatable">
                            <thead>
                                <tr class="align-center">
                                    <th>{{ __('image') }}</th>
                                    <th>{{ __('name ar').'/'. __('name en')}}</th>
                                    <th>{{ __('code') .'/'. __('version')}}</th>
                                    <th>{{ __('status') }}</th>
                                    <th>{{ __('Action') }}</th>
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
                                            <div class="flex-grow-1 ms-3">
                                                <h5 class="mb-1">
                                                    {{ $system->name_ar }} ({{ $system->symbole }})

                                                </h5>
                                                <p> {{ $system->name_en}}</p>
                                            </div>
                                        </td>
                                      

                                        <td>
                                            <div class="flex-grow-1 ms-3">
                                                <h5 class="mb-1">
                                                    {{ $system->code .'/'.$system->version }}

                                                </h5>
                                                
                                            </div>
                                        </td>

                                       
                                        <td>
                                            <div class="flex-grow-1 ms-3">
                                                @if ($system->status == 1)
                                                    <span class="d-inline badge text-bg-success">{{ __('publish') }}</span>
                                                @else
                                                    <span
                                                        class="d-inline badge text-bg-danger">{{ __('unpublish') }}</span>
                                                @endif
                                            </div>
                                        </td>
                                        <td>
                                            <div class="cart-action align-items-center">
                                                {!! Form::open(['method' => 'DELETE', 'route' => ['iso_dic.iso_systems.destroy', $system->id]]) !!}
                                                
                                                <a class="btn btn-info"
                                                    data-bs-toggle="tooltip" data-bs-original-title="{{ __('ISO Items') }}"
                                                    href="{{ route('iso_dic.iso_systems.show',\Illuminate\Support\Facades\Crypt::encrypt($system->id)) }}"
                                                    data-title="{{ __('ISO Items') }}">{{ __('ISO Items') }}</a>
                                                <a class="btn btn-secondary customModal"
                                                    data-bs-toggle="tooltip" data-size="lg"
                                                    data-bs-original-title="{{ __('Edit') }}" href="#"
                                                    data-url="{{ route('iso_dic.iso_systems.edit', $system->id) }}"
                                                    data-title="{{ __('Edit') }}"> {{ __('Edit') }}</a>
                                                {{-- <a class="avtar avtar-xs btn-link-danger text-danger confirm_dialog"
                                                    data-bs-toggle="tooltip" data-bs-original-title="{{ __('Detete') }}"
                                                    href="#"> <i data-feather="trash-2"></i></a> --}}
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
