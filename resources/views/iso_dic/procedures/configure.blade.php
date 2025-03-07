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

                                    </div>
                                </div>
                                <div class="card-body ">
                                    <ul class="nav nav-tabs" id="myTab" role="tablist">

                                        {{-- purpose-tab --}}
                                        <li class="nav-item" role="presentation">
                                            <button class="nav-link active" id="purpose-tab" data-bs-toggle="tab"
                                                data-bs-target="#purpose" type="button" role="tab"
                                                aria-controls="purpose" aria-selected="true">
                                                {{ __('purpose') }}
                                            </button>
                                        </li>

                                        {{-- scope-tab --}}
                                        <li class="nav-item" role="presentation">
                                            <button class="nav-link" id="scope-tab" data-bs-toggle="tab"
                                                data-bs-target="#scope" type="button" role="tab" aria-controls="scope"
                                                aria-selected="false">
                                                {{ __('scope') }}
                                            </button>
                                        </li>

                                        {{-- responsibility-tab --}}
                                        <li class="nav-item" role="presentation">
                                            <button class="nav-link" id="responsibility-tab" data-bs-toggle="tab"
                                                data-bs-target="#responsibility" type="button" role="tab"
                                                aria-controls="responsibility" aria-selected="false">
                                                {{ __('responsibility') }}
                                            </button>
                                        </li>

                                        {{-- definitions-tab --}}
                                        <li class="nav-item" role="presentation">
                                            <button class="nav-link" id="definitions-tab" data-bs-toggle="tab"
                                                data-bs-target="#definitions" type="button" role="tab"
                                                aria-controls="definitions" aria-selected="false">
                                                {{ __('definitions') }}
                                            </button>
                                        </li>

                                        {{-- forms-tab --}}
                                        <li class="nav-item" role="presentation">
                                            <button class="nav-link" id="forms-tab" data-bs-toggle="tab"
                                                data-bs-target="#forms" type="button" role="tab" aria-controls="forms"
                                                aria-selected="false">
                                                {{ __('forms') }}
                                            </button>
                                        </li>

                                        {{-- procedures-tab --}}
                                        <li class="nav-item" role="presentation">
                                            <button class="nav-link" id="procedures-tab" data-bs-toggle="tab"
                                                data-bs-target="#procedures" type="button" role="tab"
                                                aria-controls="procedures" aria-selected="false">
                                                {{ __('procedures') }}
                                            </button>
                                        </li>

                                        {{-- risk-matrix-tab --}}
                                        <li class="nav-item" role="presentation">
                                            <button class="nav-link" id="risk-matrix-tab" data-bs-toggle="tab"
                                                data-bs-target="#risk-matrix" type="button" role="tab"
                                                aria-controls="risk-matrix" aria-selected="false">
                                                {{ __('risk_matrix') }}
                                            </button>
                                        </li>

                                        {{-- kpis-tab --}}
                                        <li class="nav-item" role="presentation">
                                            <button class="nav-link" id="kpis-tab" data-bs-toggle="tab"
                                                data-bs-target="#kpis" type="button" role="tab" aria-controls="kpis"
                                                aria-selected="false">
                                                {{ __('kpis') }}
                                            </button>
                                        </li>

                                        {{-- references-tab --}}
                                        <li class="nav-item" role="presentation">
                                            <button class="nav-link" id="references-tab" data-bs-toggle="tab"
                                                data-bs-target="#references" type="button" role="tab"
                                                aria-controls="references" aria-selected="false">
                                                {{ __('references') }}
                                            </button>
                                        </li>
                                    </ul>

                                    <!-- Tabs Content -->
                                    <div class="tab-content mt-3" id="myTabContent">

                                        {{-- purpose --}}
                                        <div class="tab-pane fade show active" id="purpose" role="tabpanel"
                                            aria-labelledby="purpose-tab">
                                            {{-- <x-procedure-purpose purposes = {{$purposes}} /> --}}
                                            <form action="{{ route('iso_dic.procedures.saveConfigure', 'purpose') }}"
                                                method="POST" id="form-purpose">
                                                @csrf
                                                <div class="row align-items-center pb-2">
                                                    <h3 class="col">{{ __('purpose') }}</h3>
                                                </div>


                                                <div class="mb-3">
                                                    <table class="table" id="dynamic-table-purpose">
                                                        <thead>
                                                            <tr>
                                                                <th style="width: 50px;">التسلسل</th>
                                                                <th>المحتوى</th>
                                                                <th style="width: 50px;">
                                                                    <button type="button"
                                                                        class="btn btn-sm btn-success add-row px-3"
                                                                        data-tab="purpose">+</button>
                                                                </th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @forelse ($purposes->content as $index => $row)
                                                                <tr>
                                                                    <td style="width: 50px;">
                                                                        <input type="text"
                                                                            name="content[{{ $index }}][sequence]"
                                                                            class="form-control" readonly
                                                                            value="{{ $row['sequence'] }}">
                                                                    </td>
                                                                    <td>
                                                                        <textarea name="content[{{ $index }}][value]" class="form-control" rows="1">{{ $row['value'] }}</textarea>
                                                                    </td>
                                                                    <td style="width: 50px;">
                                                                        <button type="button"
                                                                            class="btn btn-sm btn-danger remove-row px-3">-</button>
                                                                    </td>
                                                                </tr>
                                                            @empty
                                                                <tr>
                                                                    <td colspan="3" class="text-center">لا توجد بيانات
                                                                    </td>
                                                                </tr>
                                                            @endforelse
                                                        </tbody>
                                                    </table>
                                                </div>
                                                <button type="button"
                                                    class="btn btn-info col-auto text-start float-end save-and-continue"
                                                    data-next-tab="scope">حفظ واستمرار</button>

                                                <button type="submit" class="btn btn-info">حفظ</button>
                                            </form>
                                        </div>

                                        {{-- scope --}}
                                        <div class="tab-pane fade" id="scope" role="tabpanel"
                                            aria-labelledby="scope-tab">
                                            <form action="{{ route('iso_dic.procedures.saveConfigure', 'scope') }}"
                                                method="POST" id="form-scope">
                                                @csrf
                                                <div class="row align-items-center pb-2">
                                                    <h3 class="col">{{ __('scope') }}</h3>
                                                </div>
                                                <div class="mb-3">
                                                    <table class="table" id="dynamic-table-scope">
                                                        <thead>
                                                            <tr>
                                                                <th style="width: 50px;">التسلسل</th>
                                                                <th>المحتوى</th>
                                                                <th style="width: 50px;">
                                                                    <button type="button"
                                                                        class="btn btn-sm btn-success add-row px-3"
                                                                        data-tab="scope">+</button>
                                                                </th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @forelse ($scopes->content as $index => $row)
                                                                <tr>
                                                                    <td style="width: 50px;">
                                                                        <input type="text"
                                                                            name="content[{{ $index }}][sequence]"
                                                                            class="form-control"
                                                                            value="{{ $row['sequence'] }}">
                                                                    </td>
                                                                    <td>
                                                                        <textarea name="content[{{ $index }}][value]" class="form-control" rows="1">{{ $row['value'] }}</textarea>
                                                                    </td>
                                                                    <td style="width: 50px;">
                                                                        <button type="button"
                                                                            class="btn btn-sm btn-danger remove-row px-3">-</button>
                                                                    </td>
                                                                </tr>
                                                            @empty
                                                                <tr>
                                                                    <td colspan="3" class="text-center">لا توجد بيانات
                                                                    </td>
                                                                </tr>
                                                            @endforelse
                                                        </tbody>
                                                    </table>
                                                </div>
                                                <button type="button"
                                                    class="btn btn-info col-auto text-start float-end  save-and-continue"
                                                    data-next-tab="responsibility">حفظ واستمرار</button>

                                            </form>
                                        </div>

                                        {{-- responsibility --}}
                                        <div class="tab-pane fade" id="responsibility" role="tabpanel"
                                            aria-labelledby="responsibility-tab">
                                            <form id="form-responsibility" data-tab-id="3"
                                                action="{{ route('iso_dic.procedures.saveConfigure', 'responsibility') }}"
                                                method="POST">

                                                @csrf
                                                <div class="row align-items-center pb-2">
                                                    <h3 class="col">{{ __('responsibility') }}</h3>
                                                </div>
                                                <div class="mb-3">
                                                    <table class="table" id="dynamic-table-responsibility">
                                                        <thead>
                                                            <tr>
                                                                <th style="width: 50px;">التسلسل</th>
                                                                <th>المحتوى</th>
                                                                <th style="width: 50px;">
                                                                    <button type="button"
                                                                        class="btn btn-sm btn-success add-row px-3"
                                                                        data-tab="responsibility">+</button>
                                                                </th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @forelse ($responsibilities->content as $index => $row)
                                                                <tr>
                                                                    <td style="width: 50px;">
                                                                        <input type="text"
                                                                            name="content[{{ $index }}][sequence]"
                                                                            class="form-control"
                                                                            value="{{ $row['sequence'] }}" readonly>
                                                                    </td>
                                                                    <td>
                                                                        <select name="content[{{ $index }}][value]"
                                                                            class="form-control showsearch">
                                                                            <option value="">اختر وظيفة</option>
                                                                            @forelse ($jobRoles as $item)
                                                                                <option value="{{ $item }}"
                                                                                    {{ $row['value'] === $item ? 'selected' : '' }}>
                                                                                    {{ $item }} </option>
                                                                            @empty
                                                                            @endforelse

                                                                        </select>
                                                                    </td>
                                                                    <td style="width: 50px;">
                                                                        <button type="button"
                                                                            class="btn btn-sm btn-danger remove-row px-3">-</button>
                                                                    </td>
                                                                </tr>
                                                            @empty
                                                                <tr>
                                                                    <td colspan="3" class="text-center">لا توجد بيانات
                                                                    </td>
                                                                </tr>
                                                            @endforelse
                                                        </tbody>
                                                    </table>
                                                </div>
                                                <button type="button"
                                                    class="btn btn-info col-auto text-start float-end  save-and-continue"
                                                    data-next-tab="definitions">حفظ واستمرار</button>
                                            </form>
                                        </div>

                                        {{-- definitions --}}
                                        <div class="tab-pane fade" id="definitions" role="tabpanel"
                                            aria-labelledby="definitions-tab">
                                            <form action="{{ route('iso_dic.procedures.saveConfigure', 'definition') }}"
                                                method="POST" id="form-definitions">
                                                @csrf
                                                <div class="row align-items-center pb-2">
                                                    <h3 class="col">{{ __('definitions') }}</h3>
                                                </div>
                                                <div class="mb-3">
                                                    <table class="table" id="dynamic-table-definitions">
                                                        <thead>
                                                            <tr>
                                                                <th style="width: 50px;">التسلسل</th>
                                                                <th>المحتوى</th>
                                                                <th style="width: 50px;">
                                                                    <button type="button"
                                                                        class="btn btn-sm btn-success add-row px-3"
                                                                        data-tab="definitions">+</button>
                                                                </th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @forelse ($definitions->content as $index => $row)
                                                                <tr>
                                                                    <td style="width: 50px;">
                                                                        <input type="text"
                                                                            name="content[{{ $index }}][sequence]"
                                                                            class="form-control"
                                                                            value="{{ $row['sequence'] }}">
                                                                    </td>
                                                                    <td>
                                                                        <textarea name="content[{{ $index }}][value]" class="form-control" rows="3">{{ $row['value'] }}</textarea>
                                                                    </td>
                                                                    <td style="width: 50px;">
                                                                        <button type="button"
                                                                            class="btn btn-sm btn-danger remove-row px-3">-</button>
                                                                    </td>
                                                                </tr>
                                                            @empty
                                                                <tr>
                                                                    <td colspan="3" class="text-center">لا توجد بيانات
                                                                    </td>
                                                                </tr>
                                                            @endforelse
                                                        </tbody>
                                                    </table>
                                                </div>
                                                <button type="button"
                                                    class="btn btn-info col-auto text-start float-end  save-and-continue"
                                                    data-next-tab="responsibility">حفظ واستمرار</button>

                                            </form>
                                        </div>

                                        {{-- forms --}}
                                        <div class="tab-pane fade" id="forms" role="tabpanel"
                                            aria-labelledby="forms-tab">
                                            <div class="row align-items-center pb-2">
                                                <h3 class="col">{{ __('forms') }}</h3>
                                            </div>
                                            <form
                                                id="dynamic-form"action="{{ route('iso_dic.procedures.saveConfigure', 'forms') }}"
                                                method="POST">
                                                @csrf
                                                <table id="forms-table" class="table text-center table-bordered">
                                                    <thead>
                                                        <tr>
                                                            <th>أسم النموذج </th>
                                                            <th>رقم النموذج</th>
                                                            <th style="width: 100px;">فترة الحفظ</th>
                                                            <th style="width:200px;">مكان الحفظ</th>
                                                            <th style="width: 50px;"> <button type="button"
                                                                    id="add-form-row" class="btn btn-success"><i
                                                                        class="ti ti-plus"></i>
                                                                </button></th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @if ($forms)
                                                            @forelse ($forms->content as $index => $row)
                                                                <tr>
                                                                    <td>
                                                                        <input type="text"
                                                                            name="content[{{ $index }}][col-0]"
                                                                            class="form-control"
                                                                            placeholder="أدخل اسم النموذج"
                                                                            value="{{ $row['col-0'] ?? '' }}">
                                                                    </td>
                                                                    <td>
                                                                        <input type="text"
                                                                            name="content[{{ $index }}][col-1]"
                                                                            class="form-control"
                                                                            placeholder="أدخل رقم النموذج"
                                                                            value="{{ $row['col-1'] ?? '' }}">
                                                                    </td>
                                                                    <td>
                                                                        <input type="text"
                                                                            name="content[{{ $index }}][col-2]"
                                                                            class="form-control"
                                                                            placeholder="أدخل فترة الحفظ"
                                                                            value="{{ $row['col-2'] ?? '3 سنوات' }}"
                                                                            readonly>
                                                                    </td>
                                                                    <td>
                                                                        <input type="text"
                                                                            name="content[{{ $index }}][col-3]"
                                                                            class="form-control"
                                                                            placeholder="أدخل مكان الحفظ"
                                                                            value="{{ $row['col-3'] ?? '' }}">
                                                                    </td>
                                                                    <td>
                                                                        <button type="button"
                                                                            class="btn btn-danger delete-form-row">
                                                                            <i class="fas fa-trash"></i>
                                                                        </button>
                                                                    </td>
                                                                </tr>
                                                            @empty
                                                                <tr>
                                                                    <td colspan="5" class="text-center">لا توجد بيانات
                                                                    </td>
                                                                </tr>
                                                            @endforelse
                                                        @endif
                                                    </tbody>
                                                </table>

                                                <button type="button"
                                                    class="btn btn-info col-auto text-start float-end save-and-continue"
                                                    data-next-tab="procedures">حفظ واستمرار</button>
                                            </form>
                                        </div>

                                        {{-- procedures --}}
                                        <div class="tab-pane fade" id="procedures" role="tabpanel"
                                            aria-labelledby="procedures-tab">
                                            <div class="row align-items-center pb-2">
                                                <h3 class="col">{{ __('procedures') }}</h3>
                                            </div>
                                            <div class="mb-3">
                                                <form
                                                    id="procedures-form"action="{{ route('iso_dic.procedures.saveConfigure', 'procedure') }}"
                                                    method="POST">
                                                    @csrf
                                                    <table id="procedures-table" class="table text-center table-bordered">
                                                        <thead>
                                                            <tr>
                                                                <th> الإجراء </th>
                                                                <th style="width:150px;"> المسؤولية</th>
                                                                <th style="width:200px;">النموذج المستخدم</th>
                                                                <th style="width: 100px;"> التحديث</th>
                                                                <th style="width:150px;">مسؤولية التحديث </th>
                                                                <th style="width: 50px;"> <button type="button"
                                                                        id="add-procedure-row" class="btn btn-success"><i
                                                                            class="ti ti-plus"></i>
                                                                    </button></th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @if ($procedures)
                                                                @forelse ($procedures->content as $index => $row)
                                                                    <tr>
                                                                        <td>
                                                                            <input type="text"
                                                                                name="content[{{ $index }}][col-0]"
                                                                                class="form-control" placeholder="الإجراء"
                                                                                value="{{ $row['col-0'] ?? '' }}">
                                                                        </td>
                                                                        <td>
                                                                            <select name="content[{{ $index }}][col-1]" class="form-control ">
                                                                                <option value="">اختر وظيفة</option>
                                                                                @foreach ($jobRoles as $item)
                                                                                    <option value="{{ $item }}" {{ isset($row['col-1']) && $row['col-1'] == $item ? 'selected' : '' }}>
                                                                                        {{ $item }}
                                                                                    </option>
                                                                                @endforeach
                                                                            </select>
                                                                        </td>
                                                                        <td>
                                                                            <input type="text"
                                                                                name="content[{{ $index }}][col-2]"
                                                                                class="form-control"
                                                                                placeholder="النموذج المستخدم"
                                                                                value="{{ $row['col-2'] ?? '' }}">
                                                                        </td>
                                                                        <td>
                                                                            <input type="text"
                                                                                name="content[{{ $index }}][col-3]"
                                                                                class="form-control" placeholder="التحديث"
                                                                                value="{{ $row['col-3'] ?? '' }}">
                                                                        </td>
                                                                        <td>
                                                                            <input type="text"
                                                                                name="content[{{ $index }}][col-4]"
                                                                                class="form-control"
                                                                                placeholder="مسؤولية التحديث"
                                                                                value="{{ $row['col-4'] ?? '' }}">
                                                                        </td>
                                                                        <td>
                                                                            <button type="button"
                                                                                class="btn btn-danger delete-procedure-row">
                                                                                <i class="fas fa-trash"></i>
                                                                            </button>
                                                                        </td>
                                                                    </tr>
                                                                @empty
                                                                    <tr>
                                                                        <td colspan="5" class="text-center">لا توجد
                                                                            بيانات
                                                                        </td>
                                                                    </tr>
                                                                @endforelse
                                                            @endif
                                                        </tbody>
                                                    </table>

                                                    <button type="button"
                                                        class="btn btn-info col-auto text-start float-end save-and-continue"
                                                        data-next-tab="risk-matrix">حفظ واستمرار</button>
                                                </form>
                                            </div>
                                        </div>

                                        {{-- risk-matrix --}}
                                        <div class="tab-pane fade" id="risk-matrix" role="tabpanel"
                                            aria-labelledby="risk-matrix-tab">
                                            <div class="row align-items-center pb-2">
                                                <h3 class="col">{{ __('risk_matrix') }}</h3>
                                            </div>
                                            <div class="mb-3">
                                                <form id="risk-matrix-form"
                                                    action="{{ route('iso_dic.procedures.saveConfigure', 'risk_matrix') }}"
                                                    method="POST">
                                                    @csrf
                                                    <table id="risk-matrix-table"
                                                        class="table text-center table-bordered">
                                                        <thead>
                                                            <tr>
                                                                <th>عامل المخاطر</th>
                                                                <th>وصف المخاطر</th>
                                                                <th style="width:70px;">درجة التأثير  (1 - 5) </th>
                                                                <th style="width:70px;">درجة الاحتمالية(1-5)</th>
                                                                <th style="width:50px;">درجة المخاطر  <br>الكلية= تأثير ×
                                                                   <br> احتمالية</th>
                                                                <th style="width:200px;">طريقة إدارة الخطر</th>
                                                                <th style="width:50px;">
                                                                    <button type="button" id="add-risk-matrix-row"
                                                                        class="btn btn-success">
                                                                        <i class="ti ti-plus"></i>
                                                                    </button>
                                                                </th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @if ($risk_matrix)
                                                                @forelse ($risk_matrix->content as $index => $row)
                                                                    <tr>

                                                                        <td>
                                                                            <input type="text"
                                                                                name="content[{{ $index }}][col-0]"
                                                                                class="form-control"
                                                                                placeholder="عامل المخاطر"
                                                                                value="{{ $row['col-0'] ?? '' }}">
                                                                        </td>
                                                                        <td>
                                                                            <textarea name="content[{{ $index }}][col-1]" class="form-control" placeholder="وصف المخاطر" rows="1">{{ $row['col-1'] ?? '' }}</textarea>
                                                                        </td>
                                                                        <td>
                                                                            <select name="content[{{ $index }}][col-2]" class="form-control impact ">
                                                                                <option value="">اختر قيمة</option>
                                                                                @for ($i = 1; $i <= 5; $i++)
                                                                                    <option value="{{ $i }}" {{ ($row['col-2'] ?? '') == $i ? 'selected' : '' }}>
                                                                                        {{ $i }}
                                                                                    </option>
                                                                                @endfor
                                                                            </select>
                                                                        </td>
                                                                        <td>
                                                                            <select name="content[{{ $index }}][col-3]" class="form-control probability ">
                                                                                <option value="">اختر قيمة</option>
                                                                                @for ($i = 1; $i <= 5; $i++)
                                                                                    <option value="{{ $i }}" {{ ($row['col-3'] ?? '') == $i ? 'selected' : '' }}>
                                                                                        {{ $i }}
                                                                                    </option>
                                                                                @endfor
                                                                            </select>
                                                                        </td>
                                                                        <td>
                                                                            <input type="text"
                                                                                name="content[{{ $index }}][col-4]"
                                                                                class="form-control total-risk"
                                                                                value="{{ ($row['col-2'] ?? 0) * ($row['col-3'] ?? 0) }}"
                                                                                readonly>
                                                                        </td>
                                                                        <td>
                                                                            <textarea name="content[{{ $index }}][col-5]" class="form-control" placeholder="طريقة إدارة الخطر"
                                                                                rows="1">{{ $row['col-5'] ?? '' }}</textarea>
                                                                        </td>

                                                                        <td>
                                                                            <button type="button"
                                                                                class="btn btn-danger delete-risk-matrix-row">
                                                                                <i class="fas fa-trash"></i>
                                                                            </button>
                                                                        </td>
                                                                    </tr>
                                                                @empty
                                                                    <tr>
                                                                        <td colspan="7" class="text-center">لا توجد
                                                                            بيانات
                                                                        </td>
                                                                    </tr>
                                                                @endforelse
                                                            @endif
                                                        </tbody>
                                                    </table>
                                                    <button type="button"
                                                        class="btn btn-info save-and-continue float-end"
                                                        data-next-tab="risk-matrix">حفظ واستمرار</button>
                                                </form>
                                            </div>
                                        </div>

                                        {{-- kpis --}}
                                        <div class="tab-pane fade" id="kpis" role="tabpanel"
                                            aria-labelledby="kpis-tab">
                                            <h3>مؤشرات الأداء KPIs</h3>
                                            <p>هذا هو القسم الخاص بمؤشرات الأداء. يمكنك إضافة تفاصيل هنا.</p>
                                        </div>

                                        {{-- references --}}
                                        <div class="tab-pane fade" id=" "role="tabpanel"
                                            aria-labelledby="references-tab">
                                            <h3>المراجع</h3>
                                            <p>هذا هو القسم الخاص بالمراجع. يمكنك إضافة تفاصيل هنا.</p>
                                        </div>


                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>


        </div>
    </div>
@endsection
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function() {

        const jobRoles = {!! json_encode($jobRoles) !!};

        initializeDynamicRows('purpose', {{ $purposes->content ? count($purposes->content) : 0 }},
            {{ $purposes->id }}, 'textarea', []);

        initializeDynamicRows('scope', {{ $scopes->content ? count($scopes->content) : 0 }},
            {{ $scopes->id }}, 'textarea', []);

        initializeDynamicRows('responsibility',
            {{ $responsibilities->content ? count($responsibilities->content) : 0 }},
            {{ $responsibilities->id }}, 'select', jobRoles);

        initializeDynamicRows('definitions', {{ $definitions->content ? count($definitions->content) : 0 }},
            {{ $definitions->id }}, 'textarea', []);


        function initializeDynamicRows(tabId, initialRowCount, index, inputType = 'textarea', options = []) {
            let rowCount = initialRowCount;

            $('#dynamic-table-' + tabId).on('click', '.add-row[data-tab="' + tabId + '"]', function() {
                let inputField;

                if (inputType === 'select') {
                    inputField = `
                        <select name="content[${rowCount}][value]" class="form-control showsearch">
                            <option value="">اختر وظيفة</option>
                            ${options.map(option => `<option value="${option}">${option}</option>`).join('')}
                        </select>
                    `;
                } else {
                    inputField = `
                        <textarea name="content[${rowCount}][value]" class="form-control" rows="1" placeholder="أدخل المحتوى"></textarea>
                    `;
                }

                const newRow = `
                        <tr>
                            <td style="width: 50px;">
                                <input type="text" name="content[${rowCount}][sequence]" class="form-control" readonly value="${(rowCount + 1)}-${index}">
                            </td>
                            <td>
                                ${inputField}
                            </td>
                            <td style="width: 50px;">
                                <button type="button" class="btn btn-sm btn-danger remove-row px-3">-</button>
                            </td>
                        </tr>
                    `;

                $('#dynamic-table-' + tabId + ' tbody').append(newRow);
                rowCount++;
            });

            $('#dynamic-table-' + tabId).on('click', '.remove-row', function() {
                $(this).closest('tr').remove();
            });
        }

        $('.save-and-continue').on('click', function() {
            const currentTab = $(this).closest('.tab-pane').attr('id');
            const nextTab = $(this).data('next-tab');
            const form = $(this).closest('form');
            const formData = form.serialize();

            $.ajax({
                url: form.attr('action'),
                method: form.attr('method'),
                data: formData,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    notifier.show('Success!', response.message, 'success', successImg,
                        4000);

                    if (nextTab) {
                        $(`#tabs button[data-bs-target="#${nextTab}"]`).trigger('click');
                    }
                },
                error: function(xhr) {
                    notifier.show('Error!', xhr.responseJSON?.message ||
                        'An unexpected error occurred.', 'error', errorImg, 4000);
                }
            });
        });


        $('#add-form-row').on('click', function() {
            const rowCount = $('#forms-table tbody tr').length;
            const newRow = `
                    <tr>
                        <td><input type="text" name="content[${rowCount}][col-0]" class="form-control" placeholder="أدخل اسم النموذج"></td>
                        <td><input type="text" name="content[${rowCount}][col-1]" class="form-control" placeholder="أدخل رقم النموذج"></td>
                        <td><input type="text" name="content[${rowCount}][col-2]" class="form-control" placeholder="أدخل فترة الحفظ" value="3 سنوات" readonly></td>
                        <td><input type="text" name="content[${rowCount}][col-3]" class="form-control" placeholder="أدخل مكان الحفظ"></td>
                        <td>
                            <button type="button" class="btn btn-danger delete-form-row">
                                <i class="fas fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                `;

            $('#forms-table tbody').append(newRow);
        });

        $('#forms-table tbody').on('click', '.delete-form-row', function() {
            $(this).closest('tr').remove();
        });

        // procedures table
        $('#add-procedure-row').on('click', function() {
            const rowCount = $('#procedures-table tbody tr').length;
            jobRolesSelect = `
                        <select name="content[${rowCount}][col-1]" class="form-control showsearch">
                            <option value="">اختر وظيفة</option>
                            ${jobRoles.map(option => `<option value="${option}">${option}</option>`).join('')}
                        </select>
                    `;
            const newRow = `
                    <tr>
                        <td><input type="text" name="content[${rowCount}][col-0]" class="form-control" placeholder="الإجراء"></td>
                         <td>
                            ${jobRolesSelect}
                        </td>
                        <td><input type="text" name="content[${rowCount}][col-2]" class="form-control" placeholder="النموذج المستخدم"></td>
                        <td><input type="text" name="content[${rowCount}][col-3]" class="form-control" placeholder="التحديث"></td>
                        <td><input type="text" name="content[${rowCount}][col-4]" class="form-control" placeholder="مسؤولية التحديث"></td>
                        <td>
                            <button type="button" class="btn btn-danger delete-procedure-row">
                                <i class="fas fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                `;

            $('#procedures-table tbody').append(newRow);
        });

        $('#procedures-table tbody').on('click', '.delete-procedure-row', function() {
            $(this).closest('tr').remove();
        });


        $('#add-risk-matrix-row').on('click', function() {
            const rowCount = $('#risk-matrix-table tbody tr').length;

              // Generate select options for impact and probability (1-5)
            const impactSelect = generateSelectOptions('impact', 5);
            const probabilitySelect = generateSelectOptions('probability', 5);

            const newRow = `
            <tr>
               
                <td>
                    <input type="text" name="content[${rowCount}][col-0]" class="form-control" 
                         placeholder="عامل المخاطر">
                </td>
                <td>
                    <textarea name="content[${rowCount}][col-1]" class="form-control" 
                        placeholder="وصف المخاطر"  rows=1></textarea>
                </td>
                 
                <td>
                    ${impactSelect}
                </td>
                <td>
                    ${probabilitySelect}
                </td>
                <td>
                    <input type="text" name="content[${rowCount}][col-4]" class="form-control total-risk" 
                        placeholder="درجة المخاطر الكلية" readonly>
                </td>
                 <td>
                    <textarea name="content[${rowCount}][col-5]" class="form-control" 
                       placeholder="طريقة إدارة الخطر" rows=1></textarea>
                </td>
               
                <td>
                    <button type="button" class="btn btn-danger delete-risk-matrix-row">
                        <i class="fas fa-trash"></i>
                    </button>
                </td>
            </tr>
        `;

            $('#risk-matrix-table tbody').append(newRow);
        });

        $('#risk-matrix-table tbody').on('click', '.delete-risk-matrix-row', function() {
            $(this).closest('tr').remove();

            // Show "No data" message if all rows are deleted
            if ($('#risk-matrix-table tbody tr').length === 0) {
                $('#risk-matrix-table tbody').html(`
                <tr>
                    <td colspan="7" class="text-center">لا توجد بيانات</td>
                </tr>
            `);
            }
        });

        // Calculate Total Risk (تأثير × احتمالية) dynamically
        $('#risk-matrix-table tbody').on('change', '.impact, .probability', function () {
            const row = $(this).closest('tr');
            const impact = parseInt(row.find('.impact').val()) || 0;
            const probability = parseInt(row.find('.probability').val()) || 0;
            const totalRisk = impact * probability;

            row.find('.total-risk').val(totalRisk);
        });


        function generateSelectOptions(className, max) {
            let options = '<select class="form-control ' + className + '">';
            options += '<option value="">اختر قيمة</option>';
            for (let i = 1; i <= max; i++) {
                options += `<option value="${i}">${i}</option>`;
            }
            options += '</select>';
            return options;
        }



    });
</script>
