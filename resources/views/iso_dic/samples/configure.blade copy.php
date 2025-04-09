@extends('layouts.admin-app')
@section('page-title')
    {{ $pageTitle }}
@endsection
@push('css-page')
    <!-- Include the Select2 CSS (usually in the <head> section) -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/css/select2.min.css" rel="stylesheet" />
@endpush
@section('breadcrumb')
    <ul class="breadcrumb mb-0">
        <li class="breadcrumb-item">
            <a href="{{ route('home') }}">{{ __('Dashboard') }}</a>
        </li>
        <li class="breadcrumb-item">
            <a href="">{{ __('ISO Systems') }}</a>
        </li>
        <li class="breadcrumb-item active">
            <a href="#"> {{ $pageTitle }} </a>
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
                                                {{ $pageTitle }}
                                            </h5>
                                        </div>
                                        <div class="col-auto">
                                            <button type="button" class="btn btn-secondary  form-generate-btn">
                                                <i class="ti ti-circle-plus align-text-bottom"></i>
                                                {{ __('Create') }}
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-body ">
                                    <div class="row mb-none-30">
                                        <div class="col-lg-12">


                                            <p class="text-danger">
                                                *{{ __('Please ensure that you include all the variables related to the sample in the template. These variables are essential to ensure the sample works correctly, as they will be used to customize and execute the required process.') }}
                                            </p>
                                            <div class="card-body">

                                                <form method="POST">
                                                    @csrf

                                                    <input type="hidden" name="id" value="{{ $sample->id }}"
                                                        required>
                                                    <div class="row addedField">
                                                        @php
                                                            $form = @$sample->form->form_data;
                                                        @endphp
                                                        @foreach (@$form ?? [] as $formData)
                                                            <div class="col-md-4">
                                                                <div class="card border mb-3" id="{{ $loop->index }}">
                                                                    <input type="hidden"
                                                                        name="form_generator[is_required][]"
                                                                        value="{{ $formData->is_required }}">
                                                                    <input type="hidden"
                                                                        name="form_generator[extensions][]"
                                                                        value="{{ $formData->extensions }}">
                                                                    <input type="hidden" name="form_generator[options][]"
                                                                        value="{{ implode(',', $formData->options) }}">

                                                                    <div class="card-body">
                                                                        <div class="form-group">
                                                                            <label>@lang('Label')</label>
                                                                            <input type="text"
                                                                                name="form_generator[form_label][]"
                                                                                class="form-control"
                                                                                value="{{ $formData->name }}" readonly>
                                                                        </div>
                                                                        <div class="form-group">
                                                                            <label>@lang('Type')</label>
                                                                            <input type="text"
                                                                                name="form_generator[form_type][]"
                                                                                class="form-control"
                                                                                value="{{ $formData->type }}" readonly>
                                                                        </div>
                                                                        @php
                                                                            $jsonData = json_encode([
                                                                                'type' => $formData->type,
                                                                                'is_required' => $formData->is_required,
                                                                                'label' => $formData->name,
                                                                                'extensions' =>
                                                                                    explode(
                                                                                        ',',
                                                                                        $formData->extensions,
                                                                                    ) ?? 'null',
                                                                                'options' => $formData->options,
                                                                                'old_id' => '',
                                                                            ]);
                                                                        @endphp
                                                                        <div class="btn-group w-100">
                                                                            <button type="button"
                                                                                class="btn btn-primary editFormData"
                                                                                data-form_item="{{ $jsonData }}"
                                                                                data-update_id="{{ $loop->index }}"><i
                                                                                    class="ti ti-edit"></i></button>
                                                                            <button type="button"
                                                                                class="btn btn-danger removeFormData"><i
                                                                                    class="ti ti-trash"></i></button>
                                                                        </div>
                                                                    </div>

                                                                </div>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                    <button type="submit"
                                                        class="btn btn-primary w-100 h-45">@lang('Submit')</button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <x-form-generator />
                            </div>

                        </div>
                    </div>
                </div>
            </div>

            {{-- template path  --}}
            <div class="card">
                <div class="card-body pt-0">
                    <form method="POST" action="{{ route('iso_dic.samples.saveTemplatePath', $sample->id) }}"
                        enctype="multipart/form-data">
                        @csrf
                        <div class="row">
                            <p class="pt-3 text-danger">
                                *{{ __('Please provide the template that the system will process to create the required operations or samples.') }}
                            </p>
                            <div class="form-group col-md-6">
                                <label for="template_path" class="form-label">{{ __('Template Path') }}</label>
                                <div class="input-group">
                                    <input type="text" name="template_path" id="file_path" class="form-control"
                                        placeholder="{{ __('Enter Template Path') }}"
                                        value="@if (isset($sample->template_path)) {{ $sample->template_path }} @endif"
                                        readonly>
                                    <button type="button" id="open-file-manager"
                                        class="btn btn-primary">{{ __('Open File Manager') }}</button>
                                </div>
                            </div>
                            <div class="form-group col-md-6 align-content-start text-end">
                                <button type="submit" id="open-file-manager"
                                    class="btn btn-secondary ">{{ __('Save') }}</button>
                            </div>
                        </div>
                        <hr>
                    </form>
                </div>
            </div>
        </div>
    </div>
    </div>
@endsection
<x-file-manager />

@push('script-page')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/js/select2.min.js"></script>
    <script>
        "use strict"
        var formGenerator = new FormGenerator();
        formGenerator.totalField = {{ @$form ? count((array) @$form->form_data) : 0 }}
    </script>

    <script src="{{ asset('assets/js/form_actions.js') }}"></script>
@endpush
