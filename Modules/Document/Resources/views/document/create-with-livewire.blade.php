@extends('tenant::layouts.app')

@section('page-title', __('Create Document'))
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('tenant.document.index') }}">{{ __('Documents') }}</a></li>
    <li class="breadcrumb-item active">{{ __('Create Document') }}</li>
@endsection

@section('content')
    <div class="row">
        <div class="col-12">
            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if (session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @livewire('document::create-document')
        </div>
    </div>
@endsection

@push('scripts')
<script>
    document.addEventListener('livewire:initialized', () => {
        Livewire.on('documentCreated', () => {
            // Show success notification
            Swal.fire({
                title: "{{ __('Success!') }}",
                text: "{{ __('Document created successfully.') }}",
                icon: "success",
                showConfirmButton: false,
                timer: 1500
            });
            
            // Redirect to documents index after a short delay
            setTimeout(() => {
                window.location.href = "{{ route('tenant.document.index') }}";
            }, 1500);
        });
    });
</script>
@endpush
