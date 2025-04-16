@extends('tenant::layouts.app')

@section('page-title')
    {{ __('File Manager') }}
@endsection

@push('css-page')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.2/font/bootstrap-icons.min.css">
<link href="{{ asset('vendor/file-manager/css/file-manager.css') }}" rel="stylesheet">
@endpush

@section('content')
<div class="row">
    <div class="container">
        <div class="row">
            <div class="col-md-12" id="fm-main-block">
                <div id="fm"></div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('script-page')
<script src="{{ asset('vendor/file-manager/js/file-manager.js') }}"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Set container height
    document.getElementById('fm-main-block').setAttribute('style', 'height:' + window.innerHeight + 'px');

    // Initialize file manager with tenant config
    window.fm = new FileManager({
        baseUrl: '{{ url('/') }}',
        basePath: '{{ $config['base_path'] }}',
        disk: '{{ $config['disk'] }}',
        maxUploadSize: {{ $config['max_size'] }},
        allowedMimeTypes: {!! json_encode($config['valid_mime']) !!},
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        }
    });

    // Set callback for file selection
    fm.$store.commit('fm/setFileCallBack', function(fileUrl) {
        if (window.opener && typeof window.opener.fmSetLink === 'function') {
            window.opener.fmSetLink(fileUrl);
            window.close();
        }
    });
});
</script>
@endpush
