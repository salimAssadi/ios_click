@extends('layouts.admin-app')
@php
    $profile = asset(Storage::url('upload/profile/'));
@endphp


@section('page-title')
    {{ __('File Manager') }}
@endsection


@section('content')
<div class="row justify-content-center mt-5">
    <div class="col-md-12">
           <div class="card style-two">
            <div class="card-header justify-content-center d-flex">
                <h5 class="card-title"> {{$pageTitle}}</h5>
            </div>
            <div class="card-body">
                <div class="row ">
                    <div class="col-lg-6">
                        <form action="" method="post" enctype="multipart/form-data">
                            @csrf
                            <x-iso-click-form identifier="act" identifierValue="{{$identifier}}"></x-iso-click-form>
                            <div class="form-group">
                                <button type="submit" class="btn btn-secondary w-100">@lang('Submit')</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
