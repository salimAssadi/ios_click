@extends('tenant::layouts.app')
@section('page-title', __('Company Seals'))
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('tenant.setting.index') }}">{{ __('Settings') }}</a></li>
    <li class="breadcrumb-item active">{{ __('Company Seals') }}</li>
@endsection
@section('content')
    <div class="card mt-2">
        <div class="card-header row">
            <h3 class="card-title col">{{ __('Company Seals') }}</h3>
            <div class="card-tools col-auto">
                <a href="{{ route('tenant.setting.company-seals.create') }}" class="btn btn-primary btn-sm">
                    <i class="fas fa-plus"></i> {{ __('Add New Seal') }}
                </a>
            </div>
        </div>
        <div class="card-body">
          

            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>{{ __('ID') }}</th>
                            <th>{{ __('Name') }}</th>
                            <th>{{ __('Image') }}</th>
                            <th>{{ __('Status') }}</th>
                            <th>{{ __('Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($companySeals as $seal)
                            <tr>
                                <td>{{ $seal->id }}</td>
                                <td>{{ $seal->name }}</td>
                                <td>
                                    @if($seal->file_path)
                                        <img src="{{ route('tenant.setting.file', $seal->file_path) }}" alt="{{ $seal->name }}" class="img-thumbnail" loading="lazy" style="max-height: 50px;">
                                    @else
                                        {{ __('No image') }}
                                    @endif
                                </td>
                                <td>
                                    <span class="badge {{ $seal->is_active ? 'bg-success' : 'bg-danger' }}">
                                        {{ $seal->is_active ? __('Active') : __('Inactive') }}
                                    </span>
                                </td>
                                <td>
                                    <div class="btn-group gap-1">
                                        <a href="{{ route('tenant.setting.company-seals.edit', $seal) }}" class="btn btn-info btn-sm">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="{{ route('tenant.setting.company-seals.show', $seal) }}" class="btn btn-primary btn-sm">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <form action="{{ route('tenant.setting.company-seals.destroy', $seal) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="button" class="btn btn-danger btn-sm confirm_dailog" data-title="{{ __('Delete Seal') }}" data-message="{{ __('Are you sure you want to delete this seal?') }}">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center">{{ __('No company seals found') }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
