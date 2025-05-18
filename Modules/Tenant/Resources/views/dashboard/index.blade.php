@extends('tenant::layouts.app')
@section('page-title')
    {{ __('Dashboard') }}
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item" aria-current="page">{{ __('Dashboard') }}</li>
@endsection
@push('script-page')
    <script>
        var documentStatusData = {!! json_encode(array_values($result['documentStatusChart'] ?? [])) !!};
        var documentStatusLabels = {!! json_encode(array_keys($result['documentStatusChart'] ?? [])) !!};
        var statusColors = [
            '#f26755', '#f7b731', '#6c5ce7', '#00b894', '#636e72', '#0984e3', '#fdcb6e', '#00b894', '#d63031',
            '#6c5ce7', '#636e72'
        ].slice(0, (documentStatusLabels || []).length);

        var statusOptions = {
            chart: {
                type: 'donut',
                height: 300
            },
            labels: documentStatusLabels,
            series: documentStatusData,
            colors: statusColors,
            legend: {
                position: 'bottom'
            }
        };
        var statusChart = new ApexCharts(document.querySelector("#document_status_chart"), statusOptions);
        statusChart.render();
    </script>

    <script>
        var documentByCategoryData = {!! json_encode($result['documentByCategory']['data']) !!};
        var documentByCategory = {!! json_encode($result['documentByCategory']['category']) !!};
        var documentBySubCategoryData = {!! json_encode($result['documentBySubCategory']['data']) !!};
        var documentBySubCategory = {!! json_encode($result['documentBySubCategory']['category']) !!};
    </script>
    {{-- <script src="{{ asset('js/dashboard.js') }}"></script> --}}
    <script>
        var timezone = '{{ !empty($settings['timezone']) ? $settings['timezone'] : 'Asia/Kolkata' }}';

        let today = new Date(new Date().toLocaleString("en-US", {
            timeZone: timezone
        }));
        var curHr = today.getHours()
        var target = document.getElementById("greetings");

        if (curHr < 12) {
            target.innerHTML = "{{ __('Good Morning,') }}";
        } else if (curHr < 17) {
            target.innerHTML = "{{ __('Good Afternoon,') }}";
        } else {
            target.innerHTML = "{{ __('Good Evening,') }}";
        }
    </script>
    <script>
        var options = {
            chart: {
                type: 'area',
                height: 250,
                toolbar: {
                    show: false
                }
            },
            colors: ['#f26755', '#0a2342'],
            dataLabels: {
                enabled: false
            },
            legend: {
                show: true,
                position: 'top'
            },
            markers: {
                size: 1,
                colors: ['#fff', '#fff', '#fff'],
                strokeColors: ['#2ca58d', '#0a2342'],
                strokeWidth: 1,
                shape: 'circle',
                hover: {
                    size: 4
                }
            },
            stroke: {
                width: 2,
                curve: 'smooth'
            },
            fill: {
                type: 'gradient',
                gradient: {
                    shadeIntensity: 1,
                    type: 'vertical',
                    inverseColors: false,
                    opacityFrom: 0.5,
                    opacityTo: 0
                }
            },
            grid: {
                show: false
            },
            series: [{
                name: "{{ __('Total Document') }}",
                data: documentByCategoryData
            }, ],
            xaxis: {
                categories: documentByCategory,
                tooltip: {
                    enabled: true
                },
                labels: {
                    hideOverlappingLabels: true
                },
                axisBorder: {
                    show: false
                },
                axisTicks: {
                    show: false
                }
            }
        };
        var chart = new ApexCharts(document.querySelector('#document_by_cat'), options);
        chart.render();
    </script>
@endpush
@push('css-page')
    <style>
        .avater2 {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 4px;
            border-radius: 50%;
        }

        .customcard {
            padding: 10px;
            max-height: 60px;
        }
    </style>
@endpush
@section('content')
    <div class="row">

        <div class="col-lg-12 col-md-12">
            <div class="bg-light-secondary card fw-bold text-black ">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="avtar bg-light-warning">
                                <i class="ti ti-user f-24"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <p class="mb-1"><span id="greetings"> </span> {{ Auth::user()->name }}</p>
                            <p>{{ __('we guide you through preparing your required documentation, quality manual, and documented processes.') }}
                            </p>

                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-9 col-md-9">
            <div class="row">
                <div class="col-lg-4 col-md-6">
                    <div class="card">
                        <div class="card-body customcard">
                            <div class="d-flex align-items-center">
                                <div class="flex-shrink-0">
                                    <div class="avater2 bg-light-secondary">
                                        <i class="ti ti-users f-24"></i>
                                    </div>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <p class="mb-1">{{ __('Total Users') }}</p>

                                </div>
                                <div class="me-3">
                                    <h4 class="mb-0">{{ $result['totalUser'] }}</h4>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4 col-md-6">
                    <div class="card">
                        <div class="card-body customcard">
                            <div class="d-flex align-items-center">
                                <div class="flex-shrink-0">
                                    <div class="avater2 bg-light-warning">
                                        <i class="ti ti-package f-24"></i>
                                    </div>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <p class="mb-1">{{ __('Total ISO System') }}</p>

                                </div>
                                <div class="me-3">
                                    <h4 class="mb-0">{{ $result['totalISOSystem'] }}</h4>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="card">
                        <div class="card-body customcard">
                            <div class="d-flex align-items-center">
                                <div class="flex-shrink-0">
                                    <div class="avater2 bg-light-danger">
                                        <i class="ti ti-package f-24"></i>
                                    </div>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <p class="mb-1">{{ __('Total Specification Item') }}</p>

                                </div>
                                <div class="me-3">
                                    <h4 class="mb-0">{{ $result['totalSpecificationItem'] }}</h4>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4 col-md-6">
                    <div class="card">
                        <div class="card-body customcard">
                            <div class="d-flex align-items-center">
                                <div class="flex-shrink-0">
                                    <div class="avater2 bg-light-danger">
                                        <i class="ti ti-file-text f-24"></i>
                                    </div>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <p class="mb-1">{{ __('Draft') }}</p>

                                </div>
                                <div class="me-3">
                                    <h4 class="mb-0">{{ $result['draftDocs'] }}
                                    </h4>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4 col-md-6">
                    <div class="card">
                        <div class="card-body customcard">
                            <div class="d-flex align-items-center">
                                <div class="flex-shrink-0">
                                    <div class="avater2 bg-light-warning">
                                        <i class="ti ti-file-text f-24"></i>
                                    </div>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <p class="mb-1">{{ __('Under Review') }}</p>

                                </div>
                                <div class="me-3">
                                    <h4 class="mb-0">{{ $result['underReviewDocs'] ?? 0 }}</h4>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4 col-md-6">
                    <div class="card">
                        <div class="card-body customcard">
                            <div class="d-flex align-items-center">
                                <div class="flex-shrink-0">
                                    <div class="avater2 bg-light-info">
                                        <i class="ti ti-file-check f-24"></i>
                                    </div>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <p class="mb-1">{{ __('Pending Approval') }}</p>

                                </div>
                                <div class="me-3">
                                    <h4 class="mb-0">{{ $result['pendingApprovalDocs'] ?? 0 }}</h4>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4 col-md-6">
                    <div class="card">
                        <div class="card-body customcard">
                            <div class="d-flex align-items-center">
                                <div class="flex-shrink-0">
                                    <div class="avater2 bg-light-success">
                                        <i class="ti ti-file-check f-24"></i>
                                    </div>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <p class="mb-1">{{ __('Approved') }}</p>

                                </div>
                                <div class="me-3">
                                    <h4 class="mb-0">{{ $result['approvedDocs'] ?? 0 }}</h4>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4 col-md-6">
                    <div class="card">
                        <div class="card-body customcard">
                            <div class="d-flex align-items-center">
                                <div class="flex-shrink-0">
                                    <div class="avater2 bg-light-secondary">
                                        <i class="ti ti-archive f-24"></i>
                                    </div>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <p class="mb-1">{{ __('Archived') }}</p>

                                </div>
                                <div class="me-3">
                                    <h4 class="mb-0">{{ $result['archivedDocs'] ?? 0 }}</h4>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>


                <div class="col-lg-12 col-md-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex align-items-start justify-content-between">
                                <div>
                                    <h5 class="mb-1">{{ __('Document By Category') }}</h5>
                                </div>

                            </div>
                            <div id="document_by_cat"></div>
                        </div>
                    </div>
                </div>

                {{-- <div class="col-lg-12 col-md-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex align-items-start justify-content-between">
                                <div>
                                    <h5 class="mb-1">{{ __('Document By Sub Category') }}</h5>
                                </div>

                            </div>
                            <div id="document_by_subcat"></div>
                        </div>
                    </div>
                </div> --}}


            </div>
        </div>


        <div class="col-lg-3 col-md-3">
            <div class="row">
                <div class="card col-12">
                    <div class="card-header border-0 pb-0">
                        <h5 class="mb-0">{{ __('Reminders') }}</h5>
                        <hr>
                    </div>
                    <div class="card-body p-2">
                        <div class="row g-3">
                            <div class="col-12">
                                <div class="d-flex align-items-center">
                                    <div class="flex-shrink-0">
                                        <div class="avater2 bg-light-primary">
                                            <i class="ti ti-bell f-24"></i>
                                        </div>
                                    </div>
                                    <div class="flex-grow-1 ms-3">
                                        <p class="mb-1">{{ __('Total Reminders') }}</p>

                                    </div>
                                    <div class="me-3">
                                        <h4 class="mb-0">{{ $result['total'] ?? 0 }}</h4>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="d-flex align-items-center">
                                    <div class="flex-shrink-0">
                                        <div class="avater2 bg-light-warning">
                                            <i class="ti ti-calendar-time f-24"></i>
                                        </div>
                                    </div>
                                    <div class="flex-grow-1 ms-3">
                                        <p class="mb-1">{{ __('Upcoming') }}</p>

                                    </div>
                                    <div class="me-3">
                                        <h4 class="mb-0">{{ $result['upcoming'] ?? 0 }}</h4>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="d-flex align-items-center">
                                    <div class="flex-shrink-0">
                                        <div class="avater2 bg-light-danger">
                                            <i class="ti ti-file-alert f-24"></i>
                                        </div>
                                    </div>
                                    <div class="flex-grow-1 ms-3">
                                        <p class="mb-1">{{ __('Document Expiry') }}</p>

                                    </div>
                                    <div class="me-3">
                                        <h4 class="mb-0">{{ $result['document'] ?? 0 }}</h4>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="d-flex align-items-center">
                                    <div class="flex-shrink-0">
                                        <div class="avater2 bg-light-success">
                                            <i class="ti ti-user f-24"></i>
                                        </div>
                                    </div>
                                    <div class="flex-grow-1 ms-3">
                                        <p class="mb-1">{{ __('Personal') }}</p>

                                    </div>
                                    <div class="me-3">
                                        <h4 class="mb-0">{{ $result['personal'] ?? 0 }}</h4>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="mb-1">{{ __('Document Status Distribution') }}</h5>
                            <div id="document_status_chart"></div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
@endsection
