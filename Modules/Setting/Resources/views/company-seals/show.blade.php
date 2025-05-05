@extends('tenant::layouts.app')
@section('page_title', 'View Company Seal')
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('tenant.setting.company-seals.index') }}">{{ __('Company Seals') }}</a></li>
    <li class="breadcrumb-item active">{{ __('View Company Seal') }}</li>
@endsection

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">{{ __('View Company Seal') }}</h3>
            <div class="card-tools">
                <a href="{{ route('tenant.setting.company-seals.index') }}" class="btn btn-default btn-sm">
                    <i class="fas fa-arrow-left"></i> {{ __('Back to List') }}
                </a>
            </div>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label>{{ __('ID') }}:</label>
                        <p>{{ $companySeal->id }}</p>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label>{{ __('Status') }}:</label>
                        <p>
                            <span class="badge {{ $companySeal->is_active ? 'badge-success' : 'badge-danger' }}">
                                {{ $companySeal->is_active ? __('Active') : __('Inactive') }}
                            </span>
                        </p>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label>{{ __('Name (Arabic)') }}:</label>
                        <p>{{ $companySeal->name_ar }}</p>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label>{{ __('Name (English)') }}:</label>
                        <p>{{ $companySeal->name_en }}</p>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label>{{ __('Type') }}:</label>
                <p>{{ ucfirst($companySeal->type) }}</p>
            </div>

            <div class="form-group">
                <label>{{ __('Seal Image') }}:</label>
                @if($companySeal->file_path)
                    <div class="mt-2">
                        <img src="{{ asset('storage/' . $companySeal->file_path) }}" alt="{{ $companySeal->name }}" class="img-fluid" style="max-height: 200px;">
                    </div>
                @else
                    <p>{{ __('No image available') }}</p>
                @endif
            </div>

            <div class="form-group">
                <label>{{ __('Created At') }}:</label>
                <p>{{ $companySeal->created_at->format('Y-m-d H:i:s') }}</p>
            </div>

            <div class="form-group">
                <label>{{ __('Last Updated') }}:</label>
                <p>{{ $companySeal->updated_at->format('Y-m-d H:i:s') }}</p>
            </div>

            <div class="mt-4">
                <a href="{{ route('tenant.setting.company-seals.edit', $companySeal) }}" class="btn btn-info">
                    <i class="fas fa-edit"></i> {{ __('Edit') }}
                </a>
                <form action="{{ route('tenant.setting.company-seals.destroy', $companySeal) }}" method="POST" class="d-inline" onsubmit="return confirm('{{ __('Are you sure you want to delete this seal?') }}');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-trash"></i> {{ __('Delete') }}
                    </button>
                </form>
            </div>
        </div>
    </div>
@endsection
