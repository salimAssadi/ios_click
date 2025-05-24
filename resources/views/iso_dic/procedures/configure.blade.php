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
            <a href="{{ route('iso_dic.home') }}">{{ __('Dashboard') }}</a>
        </li>
        <li class="breadcrumb-item">
            <a href="{{ route('iso_dic.procedures.index') }}">{{ __('Procedures') }}</a>
        </li>
        <li class="breadcrumb-item active">
            <a href=""> {{ $pageTitle }} </a>
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
                                            <ul class="nav nav-tabs" id="myTab" role="tablist">

                                                @if ($procedure->has_menual_config)
                                                    <li class="nav-item" role="presentation">
                                                        <button class="nav-link active" id="menual_config-tab"
                                                            data-bs-toggle="tab" data-bs-target="#menual_config"
                                                            type="button" role="tab" aria-controls="menual_config"
                                                            aria-selected="true">
                                                            {{ __('Menual Configrature') }}
                                                        </button>
                                                    </li>
                                                @endif
                                                {{-- scope-tab --}}
                                                @if ($procedure->enable_upload_file)
                                                    <li class="nav-item" role="presentation">
                                                        <button class="nav-link " id="upload_file-tab" data-bs-toggle="tab"
                                                            data-bs-target="#upload_file" type="button" role="tab"
                                                            aria-controls="upload_file" aria-selected="false">
                                                            {{ __('Upload File') }}
                                                        </button>
                                                    </li>
                                                @endif
                                                @if ($procedure->enable_editor)
                                                    <li class="nav-item  px-2" role="presentation">
                                                        <a href="{{ route('iso_dic.samples.configure', ['id' => Crypt::encrypt($procedure->id)]) . '?config=editor' }}"
                                                            class="btn btn-secondary customModal">
                                                            {{ __('Editor') }}
                                                        </a>
                                                    </li>
                                                @endif
                                              
                                                <button type="button" id="replacePlaceholdersBtn" class="btn btn-primary mx-2">
                                                    <i class="fas fa-exchange-alt"></i>
                                                </button>
                                               
                                            </ul>

                                        </div>
                                    </div>
                                </div>
                                <div class="card-body ">
                                    <div class="tab-content mt-3" id="headerContent">
                                        @if ($procedure->has_menual_config)
                                            <div class="tab-pane fade  show active" id="menual_config" role="tabpanel"
                                                aria-labelledby="menual_config-tab">
                                                @include('iso_dic.procedures.config.procedure' , [
                                                    'purposes' => $purposes,
                                                    'scopes' => $scopes,
                                                    'responsibilities' => $responsibilities,
                                                    'definitions' => $definitions,
                                                    'forms' => $forms,
                                                    'procedures' => $procedures,
                                                    'risk_matrix' => $risk_matrix,
                                                    'kpis' => $kpis,
                                                ])

                                                <div class="text-end mt-3">
                                                    <button type="button" id="save-configuration" class="btn btn-primary">{{ __('Save and Exit') }}</button>
                                                    <button type="button" id="save-configuration-continue" class="btn btn-success">{{ __('Save and Continue') }}</button>
                                                </div>
                                            </div>
                                        @endif
                                        @if ($procedure->enable_upload_file)
                                            <div class="tab-pane fade" id="upload_file" role="tabpanel"
                                                aria-labelledby="upload_file-tab">
                                                <div class="row">
                                                    <div class="col-sm-12">
                                                        <div class="mb-3">
                                                            <label for="upload_file"
                                                                class="form-label">{{ __('Upload File') }}</label>
                                                            <input type="file" class="form-control" id="upload_file"
                                                                name="upload_file"
                                                                accept=".txt,.doc,.docx,.pdf,.jpg,.jpeg,.png">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif


                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>


            </div>
        </div>
    @endsection
    @push('script-page')
    <script>
        
     // وظيفة عامة لحفظ الإعدادات
     function saveConfigurationAjax(button, doRedirect = true) {
            let originalText = button.html();
            button.html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> {{ __('Saving...') }}');
            button.prop('disabled', true);
            $('#save-procedure').prop('disabled', true);
            const configData = collectAllFormData();
            Swal.fire({
                icon: 'info',
                title: '{{ __('Saving...') }}',
                text: '{{ __('Please wait while saving the data') }}',
                allowOutsideClick: false,
                allowEscapeKey: false,
                showConfirmButton: false,
                willOpen: () => {
                    Swal.showLoading();
                }
            });
            $.ajax({
                url: '{{ route("iso_dic.procedures.saveConfigure", $procedure->id) }}',
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    procedure_setup_data: JSON.stringify(configData),
                    category_id: '{{ $procedure->category_id }}'
                },
                success: function(response) {
                    Swal.close();
                    notifier.show('Success!', response.message || '{{ __('Configuration saved successfully') }}', 'success',successImg, 4000);
                    button.html(originalText);
                    button.prop('disabled', false);
                    $('#save-procedure').prop('disabled', false);
                    if (doRedirect) {
                        setTimeout(() => {
                            window.history.back();
                        }, 2000);
                    }
                },
                error: function(xhr) {
                    Swal.close();
                    button.html(originalText);
                    button.prop('disabled', false);
                    $('#save-procedure').prop('disabled', false);
                    let errorMessage = '{{ __('Error saving configuration') }}';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMessage = xhr.responseJSON.message;
                    }
                    notifier.show('Error!', errorMessage, 'error',errorImg, 4000);
                }
            });
        }

        // ربط الأزرار بالوظيفة العامة
        $('#save-configuration').on('click', function() {
            saveConfigurationAjax($(this), true);
        });
        $('#save-configuration-continue').on('click', function() {
            saveConfigurationAjax($(this), false);
        });
    </script>
    @endpush
