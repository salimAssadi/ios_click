@extends('layouts.admin-app')
@php
    $profile = asset(Storage::url('upload/profile/'));
@endphp
@stack('css-page')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jstree/3.3.12/themes/default/style.min.css" />
<style>
    .jstree-default .jstree-node {
        font-size: 14px;
        line-height: 1.5;
    }

    /* Customize context menu appearance */

    /* Hover effect for context menu items */
    .jstree-contextmenu li a:hover {
        background: #f0f0f0;
    }
</style>
@section('page-title')
    {{ __('ISO specification items') }}
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item">
        <a href="{{ route('home') }}">{{ __('Dashboard') }}</a>
    </li>
    <li class="breadcrumb-item" aria-current="page">
        {{ __('ISO specification items') }}

    </li>
@endsection

@section('content')
    <div class="row mb-3">
        <div class="  d-flex justify-content-between align-items-center">
            <div class="col-md-6">
                <!-- Filter Form -->
                <form action="{{ route('iso_dic.specification_items.index') }}" method="GET" class="mb-3">
                    <div class="row">
                        <div class="col-md-4">
                            <select name="iso_system_id" id="iso_system_id" class="form-control">
                                <option value="">{{ __('All ISO Systems') }}</option>
                                @foreach ($isoSystems as $id => $name)
                                    <option value="{{ $id }}"
                                        {{ isset($selectedIsoId) && $selectedIsoId == $id ? 'selected' : '' }}>
                                        {{ $name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2 align-self-end">
                            <button type="submit" class=" btn btn-secondary">{{ __('Filter') }}</button>
                        </div>
                    </div>
                </form>

            </div>
            {{-- <h5>{{ __('ISO specification items') }}</h5> --}}
            <div style="">


                <button id="show-tree" class="btn btn-outline-primary"><i data-feather="layers"></i></button>
                <button id="show-table" class="btn btn-outline-secondary"><i data-feather="list"></i></button>
                <a href="#" class="btn btn-secondary customModal" data-size="lg"
                   data-url="{{ route('iso_dic.specification_items.create') }}"
                   data-title="{{ __('Create ISO System') }}">
                    <i class="ti ti-circle-plus align-text-bottom"></i>
                    {{ __('Create Specification Item') }}
                </a>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-body pt-0">
                    <div id="table-view">
                        <div class="dt-responsive table-responsive">
                            <div id="table-container" class="p-3" style="display: none;">
                                @include('iso_dic.specification_items.table', [
                                    'specificationItems' => $specificationItems,
                                ])
                            </div>
                        </div>
                    </div>

                    <!-- Tree View -->
                    <div id="iso-tree-container" class="p-3" style="display: none;">
                        <div id="iso-tree">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

<script src="{{ asset('assets/js/extended-ui-treeview.js') }}"></script>
<script>
x
</script>

