@extends('tenant::layouts.app')

@section('page-title', __('Private Procedures'))
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('tenant.document.index') }}">{{ __('Documents') }}</a></li>
    <li class="breadcrumb-item active">{{ __('Private Procedures') }}</li>
@endsection

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">{{ __('Private Procedures') }}</h5>
                    <div>
                        <a href="{{ route('tenant.document.create') }}" class="btn btn-primary btn-sm">
                            <i class="fas fa-plus me-1"></i>{{ __('New Document') }}
                        </a>
                        {{-- <a href="{{ route('tenant.document.create-livewire') }}" class="btn btn-success btn-sm">
                            <i class="fas fa-bolt me-1"></i>{{ __('New Document (Livewire)') }}
                        </a> --}}
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover" id="private-procedures-table">
                            <thead>
                                <tr>
                                    <th>{{ __('Title') }}</th>
                                    <th>{{ __('Document Number') }}</th>
                                    <th>{{ __('Version') }}</th>
                                    <th>{{ __('Department') }}</th>
                                    <th>{{ __('Status') }}</th>
                                    <th>{{ __('Actions') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Table content will be loaded via DataTables -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script-page')
<script>
    $(document).ready(function() {
        $('#private-procedures-table').DataTable({
          
        });
    });
</script>
@endpush