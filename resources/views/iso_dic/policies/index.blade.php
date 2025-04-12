@extends('layouts.admin-app')
@section('page-title')
    {{ __('Policies') }}
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('iso_dic.dashboard') }}">{{ __('Dashboard') }}</a></li>
    <li class="breadcrumb-item">{{ __('Policies') }}</li>
@endsection

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header row">
                    <div class="col">
                        <h5>{{ __('Policies') }}</h5>
                    </div>
                    <div class="col-auto">
                        <a href="{{ route('iso_dic.policies.create') }}" class="btn btn-sm btn-primary" data-bs-toggle="tooltip"
                            title="{{ __('Create') }}">
                            <i class="ti ti-plus"></i>
                        </a>
                    </div>
                </div>
                <div class="card-body table-border-style">
                    <div class="table-responsive">
                        <table class="table basic-datatable table-striped">
                            <thead>
                                <tr>
                                    <th>{{ __('Name') }}</th>
                                    <th>{{ __('Description') }}</th>
                                    <th>{{ __('Attachments') }}</th>
                                    <th>{{ __('Status') }}</th>
                                    <th width="200px">{{ __('Action') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($policies as $policy)
                                    <tr>
                                        <td>
                                            {{ app()->getLocale() == 'ar' ? $policy->name_ar : $policy->name_en }}
                                        </td>
                                        <td>
                                            {{ app()->getLocale() == 'ar' ? $policy->description_ar : $policy->description_en }}
                                        </td>
                                        <td>
                                            @foreach($policy->attachments as $attachment)
                                                <div class="mb-1">
                                                    <a href="{{ route('iso_dic.policies.attachments.download', $attachment->id) }}" 
                                                       class="btn btn-sm btn-light me-2">
                                                       <i class="ti ti-download"></i>
                                                        {{ $attachment->original_name }}
                                                    </a>
                                                </div>
                                            @endforeach
                                        </td>
                                        <td>
                                            @if($policy->is_published)
                                                <span class="badge bg-success">{{ __('Published') }}</span>
                                            @else
                                                <span class="badge bg-warning">{{ __('Unpublished') }}</span>
                                            @endif
                                        </td>
                                        <td class="Action d-flex gap-2 ">
                                            <div class="action-btn">
                                                <a href="{{ route('iso_dic.policies.edit', $policy->id) }}"
                                                    class="mx-3 btn btn-sm align-items-center btn-primary"
                                                    data-bs-toggle="tooltip" title="{{ __('Edit') }}">
                                                    <i class="ti ti-pencil text-white"></i>
                                                </a>
                                            </div>
                                            <div class="action-btn">
                                                {!! Form::open(['method' => 'DELETE', 'route' => ['iso_dic.policies.destroy', $policy->id], 'class' => 'd-inline']) !!}
                                                <a href="#" class="mx-3 btn btn-sm align-items-center btn-danger show_confirm"
                                                    data-bs-toggle="tooltip" title="{{ __('Delete') }}">
                                                    <i class="ti ti-trash text-white"></i>
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
