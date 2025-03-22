@extends('layouts.admin-app')
@section('page-title')
    {{ __('Document Details') }}
@endsection

@section('breadcrumb')
    <ul class="breadcrumb mb-0">
        <li class="breadcrumb-item">
            <a href="{{ route('iso_dic.home') }}">{{ __('Dashboard') }}</a>
        </li>
        <li class="breadcrumb-item">
            <a href="#">{{ __('ISO Systems') }}</a>
        </li>
        <li class="breadcrumb-item active">
            <a href="#">{{ __('Attachments') }}</a>
        </li>
    </ul>
@endsection
@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="email-body">
                        
                            <div class="card">
                                <div class="card-header">
                                    <div class="row align-items-center g-2">
                                        <div class="col">
                                            <h5>
                                                {{ __('Attachments') }}
                                            </h5>
                                        </div>
                                        <div class="col-auto">
                                            <a href="#" class="btn btn-secondary customModal" data-size="lg"
                                                data-url="{{ route('iso_dic.attachments.create') }}" data-title="{{ __('Create Attachment') }}">
                
                                                <i class="ti ti-circle-plus align-text-bottom"></i>
                                                {{ __('Create Attachment') }}
                                            </a>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-body pt-0">
                                    <div class="dt-responsive table-responsive">
                                        <table class="table table-hover advance-datatable">
                                            <thead>
                                                <tr>
                                                    <th>{{ __('Uploaded At') }}</th>
                                                    <th>{{ __('Uploaded By') }}</th>
                                                    <th>{{ __('Status') }}</th>
                                                    <th>{{ __('Action') }}</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                {{-- @foreach ($versions as $version)
                                        <tr role="row">
                                            <td>{{ dateFormat($version->created_at) }}
                                                {{ timeFormat($version->created_at) }}</td>
                                            <td>{{ !empty($version->createdBy) ? $version->createdBy->name : '-' }}
                                            </td>
                                            <td>
                                                @if ($version->current_version == 1)
                                                    <span
                                                        class="d-inline badge text-bg-success">{{ __('Current Version') }}</span>
                                                @else
                                                    <span
                                                        class="d-inline badge text-bg-warning">{{ __('Old Version') }}</span>
                                                @endif
                                            </td>
                                                <td>
                                                        <a class="avtar avtar-xs btn-link-info text-info"
                                                            data-bs-toggle="tooltip"
                                                            data-bs-original-title="{{ __('View') }}"
                                                            href="{{ !empty($version->document) ? asset(Storage::url('upload/document/')) . '/' . $version->document : '#' }}"
                                                            target="_blank"> <i
                                                                data-feather="maximize"></i></a>
                                                        <a class="avtar avtar-xs btn-link-primary text-primary"
                                                            data-bs-toggle="tooltip"
                                                            data-bs-original-title="{{ __('Download') }}"
                                                            href="{{ !empty($version->document) ? asset(Storage::url('upload/document/')) . '/' . $version->document : '#' }}"
                                                            download=""> <i
                                                                data-feather="download"></i></a>
                                                    
                                                </td>
                                        </tr>
                                    @endforeach --}}
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
