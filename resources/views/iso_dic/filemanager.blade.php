@extends('layouts.admin-app')
@php
    $profile = asset(Storage::url('upload/profile/'));
@endphp
@stack('css-page')

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.2/font/bootstrap-icons.min.css">
<link href="{{ asset('vendor/file-manager/css/file-manager.css') }}" rel="stylesheet">

@section('page-title')
    {{ __('File Manager') }}
@endsection


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
    <!-- File manager -->
@endsection
@push('script-page')
    <script>
        document.addEventListener('DOMContentLoaded', function() {

            document.getElementById('fm-main-block').setAttribute('style', 'height:' + window.innerHeight + 'px');
            fm.$store.commit('fm/setFileCallBack', function(fileUrl) {
                window.opener.fmSetLink(fileUrl);

                window.close();

            });

        });
    </script>
    </script>
