<ul class="nav nav-tabs" id="myTab" role="tablist">
    {{-- purpose-tab --}}
    <li class="nav-item" role="presentation">
        <button class="nav-link active" id="purpose-tab" data-bs-toggle="tab" data-bs-target="#purpose" type="button"
            role="tab" aria-controls="purpose" aria-selected="true">
            {{ __('purpose') }}
        </button>
    </li>

    {{-- scope-tab --}}
    <li class="nav-item" role="presentation">
        <button class="nav-link" id="scope-tab" data-bs-toggle="tab" data-bs-target="#scope" type="button"
            role="tab" aria-controls="scope" aria-selected="false">
            {{ __('scope') }}
        </button>
    </li>

    {{-- responsibility-tab --}}
    <li class="nav-item" role="presentation">
        <button class="nav-link" id="responsibility-tab" data-bs-toggle="tab" data-bs-target="#responsibility"
            type="button" role="tab" aria-controls="responsibility" aria-selected="false">
            {{ __('responsibility') }}
        </button>
    </li>

    {{-- definitions-tab --}}
    <li class="nav-item" role="presentation">
        <button class="nav-link" id="definitions-tab" data-bs-toggle="tab" data-bs-target="#definitions" type="button"
            role="tab" aria-controls="definitions" aria-selected="false">
            {{ __('definitions') }}
        </button>
    </li>

    {{-- forms-tab --}}
    <li class="nav-item" role="presentation">
        <button class="nav-link" id="forms-tab" data-bs-toggle="tab" data-bs-target="#forms" type="button"
            role="tab" aria-controls="forms" aria-selected="false">
            {{ __('forms') }}
        </button>
    </li>

    {{-- procedures-tab --}}
    <li class="nav-item" role="presentation">
        <button class="nav-link" id="procedures-tab" data-bs-toggle="tab" data-bs-target="#procedures" type="button"
            role="tab" aria-controls="procedures" aria-selected="false">
            {{ __('procedures') }}
        </button>
    </li>

    {{-- risk-matrix-tab --}}
    <li class="nav-item" role="presentation">
        <button class="nav-link" id="risk-matrix-tab" data-bs-toggle="tab" data-bs-target="#risk-matrix" type="button"
            role="tab" aria-controls="risk-matrix" aria-selected="false">
            {{ __('risk_matrix') }}
        </button>
    </li>

    {{-- kpis-tab --}}
    <li class="nav-item" role="presentation">
        <button class="nav-link" id="kpis-tab" data-bs-toggle="tab" data-bs-target="#kpis" type="button"
            role="tab" aria-controls="kpis" aria-selected="false">
            {{ __('kpis') }}
        </button>
    </li>

    {{-- references-tab --}}
    <li class="nav-item" role="presentation">
        <button class="nav-link" id="references-tab" data-bs-toggle="tab" data-bs-target="#references" type="button"
            role="tab" aria-controls="references" aria-selected="false">
            {{ __('references') }}
        </button>
    </li>
</ul>
<!-- Tabs Content -->
<div class="tab-content mt-3" id="myTabContent">

    {{-- purpose --}}
    <div class="tab-pane fade show active" id="purpose" role="tabpanel" aria-labelledby="purpose-tab">
        {{-- <x-procedure-purpose purposes = {{$purposes}} /> --}}
        <form action="{{ route('iso_dic.procedures.saveConfigure', 'purpose') }}" method="POST" id="form-purpose">
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
                                <button type="button" class="btn btn-sm btn-success add-row px-3"
                                    data-tab="purpose">+</button>
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($purposes as $index => $row)
                            <tr>
                                <td style="width: 50px;">
                                    <input type="text" name="content[{{ $index }}][sequence]"
                                        class="form-control" readonly value="{{ $row['sequence'] }}">
                                </td>
                                <td>
                                    <textarea name="content[{{ $index }}][value]" class="form-control" rows="1">{{ $row['value'] }}</textarea>
                                </td>
                                <td style="width: 50px;">
                                    <button type="button" class="btn btn-sm btn-danger remove-row px-3">-</button>
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
            <button type="button" class="btn btn-info col-auto text-start float-end save-and-continue"
                data-next-tab="scope">حفظ واستمرار</button>

            <button type="submit" class="btn btn-info">حفظ</button>
        </form>
    </div>

    {{-- scope --}}
    <div class="tab-pane fade" id="scope" role="tabpanel" aria-labelledby="scope-tab">
        <form  id="form-scope">
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
                                <button type="button" class="btn btn-sm btn-success add-row px-3"
                                    data-tab="scope">+</button>
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($scopes  as $index => $row)
                            <tr>
                                <td style="width: 50px;">
                                    <input type="text" name="content[{{ $index }}][sequence]"
                                        class="form-control" value="{{ $row['sequence'] }}">
                                </td>
                                <td>
                                    <textarea name="content[{{ $index }}][value]" class="form-control" rows="1">{{ $row['value'] }}</textarea>
                                </td>
                                <td style="width: 50px;">
                                    <button type="button" class="btn btn-sm btn-danger remove-row px-3">-</button>
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
            <button type="button" class="btn btn-info col-auto text-start float-end  save-and-continue"
                data-next-tab="responsibility">حفظ واستمرار</button>

        </form>
    </div>

    {{-- responsibility --}}
    <div class="tab-pane fade" id="responsibility" role="tabpanel" aria-labelledby="responsibility-tab">
        <form id="form-responsibility" data-tab-id="3">

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
                                <button type="button" class="btn btn-sm btn-success add-row px-3"
                                    data-tab="responsibility">+</button>
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($responsibilities as $index => $row)
                            <tr>
                                <td style="width: 50px;">
                                    <input type="text" name="content[{{ $index }}][sequence]"
                                        class="form-control" value="{{ $row['sequence'] }}" readonly>
                                </td>
                                <td>
                                    <select name="content[{{ $index }}][value]"
                                        class="form-control showsearch">
                                        <option value="">اختر وظيفة</option>
                                        @forelse ($jobRoles as $key => $item)
                                            @if (is_array($jobRoles))
                                                <option value="{{ $item }}"
                                                    {{ $row['value'] == $item ? 'selected' : '' }}>
                                                    {{ $item }}
                                                </option>
                                            @elseif (is_object($item) && property_exists($item, 'name'))
                                                <option value="{{ $item->id ?? $item->name }}"
                                                    {{ $row['value'] == ($item->id ?? $item->name) ? 'selected' : '' }}>
                                                    {{ $item->name }}
                                                </option>
                                            @else
                                                <option value="{{ $key }}"
                                                    {{ $row['value'] == $key ? 'selected' : '' }}>
                                                    {{ $item }}
                                                </option>
                                            @endif
                                        @empty
                                        @endforelse
                                    </select>
                                </td>
                                <td style="width: 50px;">
                                    <button type="button" class="btn btn-sm btn-danger remove-row px-3">-</button>
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
            <button type="button" class="btn btn-info col-auto text-start float-end  save-and-continue"
                data-next-tab="definitions">حفظ واستمرار</button>
        </form>
    </div>

    {{-- definitions --}}
    <div class="tab-pane fade" id="definitions" role="tabpanel" aria-labelledby="definitions-tab">
        <form id="form-definitions" data-tab-id="4">
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
                                <button type="button" class="btn btn-sm btn-success add-row px-3"
                                    data-tab="definitions">+</button>
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($definitions as $index => $row)
                            <tr>
                                <td style="width: 50px;">
                                    <input type="text" name="content[{{ $index }}][sequence]"
                                        class="form-control" value="{{ $row['sequence'] }}">
                                </td>
                                <td>
                                    <textarea name="content[{{ $index }}][value]" class="form-control" rows="3">{{ $row['value'] }}</textarea>
                                </td>
                                <td style="width: 50px;">
                                    <button type="button" class="btn btn-sm btn-danger remove-row px-3">-</button>
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
            <button type="button" class="btn btn-info col-auto text-start float-end  save-and-continue"
                data-next-tab="responsibility">حفظ واستمرار</button>

        </form>
    </div>

    {{-- forms --}}
    <div class="tab-pane fade" id="forms" role="tabpanel" aria-labelledby="forms-tab">
        <div class="row align-items-center pb-2">
            <h3 class="col">{{ __('forms') }}</h3>
        </div>
        <form id="dynamic-form" data-tab-id="5">
            @csrf
            <table id="forms-table" class="table text-center table-bordered">
                <thead>
                    <tr>
                        <th>أسم النموذج </th>
                        <th>رقم النموذج</th>
                        <th style="width: 100px;">فترة الحفظ</th>
                        <th style="width:200px;">مكان الحفظ</th>
                        <th style="width: 50px;"> <button type="button" id="add-form-row"
                                class="btn btn-success"><i class="ti ti-plus"></i>
                            </button></th>
                    </tr>
                </thead>
                <tbody>
                    @if ($forms)
                        @forelse ($forms as $index => $row)
                            <tr>
                                <td>
                                    <input type="text" name="content[{{ $index }}][col-0]"
                                        class="form-control" placeholder="أدخل اسم النموذج"
                                        value="{{ $row['col-0'] ?? '' }}">
                                </td>
                                <td>
                                    <input type="text" name="content[{{ $index }}][col-1]"
                                        class="form-control" placeholder="أدخل رقم النموذج"
                                        value="{{ $row['col-1'] ?? '' }}">
                                </td>
                                <td>
                                    <input type="text" name="content[{{ $index }}][col-2]"
                                        class="form-control" placeholder="أدخل فترة الحفظ"
                                        value="{{ $row['col-2'] ?? '3 سنوات' }}" readonly>
                                </td>
                                <td>
                                    <input type="text" name="content[{{ $index }}][col-3]"
                                        class="form-control" placeholder="أدخل مكان الحفظ"
                                        value="{{ $row['col-3'] ?? '' }}">
                                </td>
                                <td>
                                    <button type="button" class="btn btn-danger delete-form-row">
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

            <button type="button" class="btn btn-info col-auto text-start float-end save-and-continue"
                data-next-tab="procedures">حفظ واستمرار</button>
        </form>
    </div>

    {{-- procedures --}}
    <div class="tab-pane fade" id="procedures" role="tabpanel" aria-labelledby="procedures-tab">
        <div class="row align-items-center pb-2">
            <h3 class="col">{{ __('procedures') }}</h3>
        </div>
        <div class="mb-3">
            <form id="procedures-form" data-tab-id="6">
                @csrf
                <table id="procedures-table" class="table text-center table-bordered">
                    <thead>
                        <tr>
                            <th> الإجراء </th>
                            <th style="width:150px;"> المسؤولية</th>
                            <th style="width:200px;">النموذج المستخدم</th>
                            <th style="width: 100px;"> التحديث</th>
                            <th style="width:150px;">مسؤولية التحديث </th>
                            <th style="width: 50px;"> <button type="button" id="add-procedure-row"
                                    class="btn btn-success"><i class="ti ti-plus"></i>
                                </button></th>
                        </tr>
                    </thead>
                    <tbody>
                        @if ($procedures)
                            @forelse ($procedures as $index => $row)
                                <tr>
                                    <td>
                                        <input type="text" name="content[{{ $index }}][col-0]"
                                            class="form-control" placeholder="الإجراء"
                                            value="{{ $row['col-0'] ?? '' }}">
                                    </td>
                                    <td>
                                        <select name="content[{{ $index }}][col-1]" class="form-control ">
                                            <option value="">اختر وظيفة</option>
                                            @foreach ($jobRoles as $item)
                                                <option value="{{ $item }}"
                                                    {{ isset($row['col-1']) && $row['col-1'] == $item ? 'selected' : '' }}>
                                                    {{ $item }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td>
                                        <input type="text" name="content[{{ $index }}][col-2]"
                                            class="form-control" placeholder="النموذج المستخدم"
                                            value="{{ $row['col-2'] ?? '' }}">
                                    </td>
                                    <td>
                                        <input type="text" name="content[{{ $index }}][col-3]"
                                            class="form-control" placeholder="التحديث"
                                            value="{{ $row['col-3'] ?? '' }}">
                                    </td>
                                    <td>
                                        <input type="text" name="content[{{ $index }}][col-4]"
                                            class="form-control" placeholder="مسؤولية التحديث"
                                            value="{{ $row['col-4'] ?? '' }}">
                                    </td>
                                    <td>
                                        <button type="button" class="btn btn-danger delete-procedure-row">
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

                <button type="button" class="btn btn-info col-auto text-start float-end save-and-continue"
                    data-next-tab="risk-matrix">حفظ واستمرار</button>
            </form>
        </div>
    </div>

    {{-- risk-matrix --}}
    <div class="tab-pane fade" id="risk-matrix" role="tabpanel" aria-labelledby="risk-matrix-tab">
        <div class="row align-items-center pb-2">
            <h3 class="col">{{ __('risk_matrix') }}</h3>
        </div>
        <div class="mb-3">
            <form id="risk-matrix-form" data-tab-id="7">
                @csrf
                <table id="risk-matrix-table" class="table text-center table-bordered">
                    <thead>
                        <tr>
                            <th>عامل المخاطر</th>
                            <th>وصف المخاطر</th>
                            <th style="width:70px;">درجة التأثير (1 - 5) </th>
                            <th style="width:70px;">درجة الاحتمالية(1-5)</th>
                            <th style="width:50px;">درجة المخاطر <br>الكلية= تأثير ×
                                <br> احتمالية
                            </th>
                            <th style="width:200px;">طريقة إدارة الخطر</th>
                            <th style="width:50px;">
                                <button type="button" id="add-risk-matrix-row" class="btn btn-success">
                                    <i class="ti ti-plus"></i>
                                </button>
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @if ($risk_matrix)
                            @forelse ($risk_matrix as $index => $row)
                                <tr>

                                    <td>
                                        <input type="text" name="content[{{ $index }}][col-0]"
                                            class="form-control" placeholder="عامل المخاطر"
                                            value="{{ $row['col-0'] ?? '' }}">
                                    </td>
                                    <td>
                                        <textarea name="content[{{ $index }}][col-1]" class="form-control" placeholder="وصف المخاطر" rows="1">{{ $row['col-1'] ?? '' }}</textarea>
                                    </td>
                                    <td>
                                        <select name="content[{{ $index }}][col-2]"
                                            class="form-control impact ">
                                            <option value="">اختر قيمة</option>
                                            @for ($i = 1; $i <= 5; $i++)
                                                <option value="{{ $i }}"
                                                    {{ ($row['col-2'] ?? '') == $i ? 'selected' : '' }}>
                                                    {{ $i }}
                                                </option>
                                            @endfor
                                        </select>
                                    </td>
                                    <td>
                                        <select name="content[{{ $index }}][col-3]"
                                            class="form-control probability ">
                                            <option value="">اختر قيمة</option>
                                            @for ($i = 1; $i <= 5; $i++)
                                                <option value="{{ $i }}"
                                                    {{ ($row['col-3'] ?? '') == $i ? 'selected' : '' }}>
                                                    {{ $i }}
                                                </option>
                                            @endfor
                                        </select>
                                    </td>
                                    <td>
                                        <input type="text" name="content[{{ $index }}][col-4]"
                                            class="form-control total-risk"
                                            value="{{ ($row['col-2'] ?? 0) * ($row['col-3'] ?? 0) }}" readonly>
                                    </td>
                                    <td>
                                        <textarea name="content[{{ $index }}][col-5]" class="form-control" placeholder="طريقة إدارة الخطر"
                                            rows="1">{{ $row['col-5'] ?? '' }}</textarea>
                                    </td>

                                    <td>
                                        <button type="button" class="btn btn-danger delete-risk-matrix-row">
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
                <button type="button" class="btn btn-info save-and-continue float-end"
                    data-next-tab="risk-matrix">حفظ واستمرار</button>
            </form>
        </div>
    </div>

    {{-- kpis --}}
    <div class="tab-pane fade" id="kpis" role="tabpanel" aria-labelledby="kpis-tab">
        <div class="row align-items-center pb-2">
            <h3 class="col">{{ __('kpis') }}</h3>
        </div>
        <div class="mb-3">
            <form id="kpis-form" data-tab-id="8">
                @csrf
                <table id="kpis-table" class="table text-center table-bordered">
                    <thead>
                        <tr>
                            <th> {{ __('Pointer') }}</th>
                            <th>{{ __('Description') }}</th>
                            <th >{{ __('Measurement Method') }}</th>
                            <th>{{ __('Goal') }}</th>
                            <th>
                                <button type="button" id="add-kpis-row" class="btn btn-success">
                                    <i class="ti ti-plus"></i>
                                </button>
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @if ($kpis)
                            @forelse ($kpis as $index => $row)
                                <tr>

                                    <td>
                                        <input type="text" name="content[{{ $index }}][col-0]"
                                            class="form-control" placeholder="{{ __('Pointer') }}"
                                            value="{{ $row['col-0'] ?? '' }}">
                                    </td>
                                    <td>
                                        <textarea name="content[{{ $index }}][col-1]" class="form-control" placeholder="{{ __('Description') }}"
                                            rows="1">{{ $row['col-1'] ?? '' }}</textarea>
                                    </td>
                                    <td>
                                        <input type="text" name="content[{{ $index }}][col-2]"
                                            class="form-control "
                                             placeholder="{{ __('Measurement Method') }}"
                                            value="{{ $row['col-2'] ?? '' }}">
                                    </td>
                                    <td>
                                        <input type="text" name="content[{{ $index }}][col-3]" class="form-control"
                                            value="{{ $row['col-3'] ?? '' }}">
                                    </td>

                                    <td>
                                        <button type="button" class="btn btn-danger delete-kpis-row">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center">
                                        {{ __('No Data') }}
                                    </td>
                                </tr>
                            @endforelse
                        @endif
                    </tbody>
                </table>
                <button type="button" class="btn btn-info save-and-continue float-end"
                    data-next-tab="references">{{ __('Save and Continue') }} </button>
            </form>
        </div>
    </div>

    {{-- references --}}
    <div class="tab-pane fade" id="references" role="tabpanel" aria-labelledby="references-tab">
        <h3>{{ __('References') }}</h3>
        <p>{{ __('This is the section for references. You can add details here.') }}</p>
    </div>


</div>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function() {

        const jobRoles = {!! json_encode($jobRoles) !!};
        
        // إضافة كود للكشف عن بنية البيانات jobRoles
        console.log('jobRoles type:', typeof jobRoles);
        console.log('jobRoles value:', jobRoles);
        
        if (typeof jobRoles === 'object') {
            console.log('jobRoles keys:', Object.keys(jobRoles));
            console.log('jobRoles values:', Object.values(jobRoles));
            
            if (Array.isArray(jobRoles)) {
                console.log('jobRoles is Array with length:', jobRoles.length);
            } else {
                console.log('jobRoles is Object with properties count:', Object.keys(jobRoles).length);
            }
            
            // التحقق من نوع العناصر الداخلية
            const firstItem = Array.isArray(jobRoles) 
                ? jobRoles[0] 
                : Object.values(jobRoles)[0];
            
            if (firstItem) {
                console.log('First item type:', typeof firstItem);
                console.log('First item value:', firstItem);
                
                if (typeof firstItem === 'object') {
                    console.log('First item properties:', Object.keys(firstItem));
                }
            }
        }

        initializeDynamicRows('purpose', {{ $purposes ? count($purposes) : 0 }},
            1, 'textarea', []);

        initializeDynamicRows('scope', {{ $scopes ? count($scopes) : 0 }},
            2, 'textarea', []);

        initializeDynamicRows('responsibility',
            {{ $responsibilities ? count($responsibilities) : 0 }},
            3, 'select', jobRoles);

        initializeDynamicRows('definitions', {{ $definitions ? count($definitions) : 0 }},
            4, 'textarea', []);


        function initializeDynamicRows(tabId, initialRowCount, index, inputType = 'textarea', options = []) {
            let rowCount = initialRowCount;

            $('#dynamic-table-' + tabId).on('click', '.add-row[data-tab="' + tabId + '"]', function() {
                let inputField;

                if (inputType === 'select') {
                    let optionsHtml = '';
                    
                    // التحقق من نوع options وتحويلها إلى خيارات
                    if (Array.isArray(options)) {
                        // إذا كانت مصفوفة بسيطة
                        optionsHtml = options.map(option => `<option value="${option}">${option}</option>`).join('');
                    } else if (typeof options === 'object' && options !== null) {
                        // إذا كان كائن من قاعدة البيانات (جدول positions)
                        if (Array.isArray(Object.values(options))) {
                            // إذا كانت قيم الكائن هي مصفوفة
                            optionsHtml = Object.values(options).map(position => {
                                // التحقق ما إذا كان position كائن يحتوي على id و name
                                if (typeof position === 'object' && position !== null && position.hasOwnProperty('name')) {
                                    const name = position.name;
                                    const id = position.hasOwnProperty('id') ? position.id : name;
                                    return `<option value="${id}">${name}</option>`;
                                } else {
                                    return `<option value="${position}">${position}</option>`;
                                }
                            }).join('');
                        } else {
                            // إذا كان كائن بسيط
                            optionsHtml = Object.keys(options).map(key => {
                                const value = options[key];
                                return `<option value="${key}">${value}</option>`;
                            }).join('');
                        }
                    }
                    
                    inputField = `
                        <select name="content[${rowCount}][value]" class="form-control showsearch">
                            <option value="">اختر وظيفة</option>
                            ${optionsHtml}
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

        $('.save-and-continue').on('click', function(event) {
            event.preventDefault();
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

        $('#add-procedure-row').on('click', function() {
            const rowCount = $('#procedures-table tbody tr').length;
            let jobRolesOptions = '';
            
            // التحقق من نوع jobRoles وتحويلها إلى خيارات
            if (Array.isArray(jobRoles)) {
                // إذا كانت مصفوفة بسيطة
                jobRolesOptions = jobRoles.map(option => `<option value="${option}">${option}</option>`).join('');
            } else if (typeof jobRoles === 'object' && jobRoles !== null) {
                // إذا كان كائن من قاعدة البيانات (جدول positions)
                if (Array.isArray(Object.values(jobRoles))) {
                    // إذا كانت قيم الكائن هي مصفوفة
                    jobRolesOptions = Object.values(jobRoles).map(position => {
                        // التحقق ما إذا كان position كائن يحتوي على id و name
                        if (typeof position === 'object' && position !== null && position.hasOwnProperty('name')) {
                            const name = position.name;
                            const id = position.hasOwnProperty('id') ? position.id : name;
                            return `<option value="${id}">${name}</option>`;
                        } else {
                            return `<option value="${position}">${position}</option>`;
                        }
                    }).join('');
                } else {
                    // إذا كان كائن بسيط
                    jobRolesOptions = Object.keys(jobRoles).map(key => {
                        const value = jobRoles[key];
                        return `<option value="${key}">${value}</option>`;
                    }).join('');
                }
            }
            
            jobRolesSelect = `
                        <select name="content[${rowCount}][col-1]" class="form-control showsearch">
                            <option value="">اختر وظيفة</option>
                            ${jobRolesOptions}
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

            const impactSelect = generateSelectOptions('impact', 5);
            const probabilitySelect = generateSelectOptions('probability', 5);

            const newRow = `
            <tr>
               
                <td>
                    <input type="text" name="content[${rowCount}][col-0]" class="form-control" 
                         placeholder="عامل المخاطر">
                </td>
                <td>
                    <textarea name="content[${rowCount}][col-1]" class="form-control" placeholder="وصف المخاطر" rows="1"></textarea>
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
                       placeholder="طريقة إدارة الخطر" rows="1"></textarea>
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

        $('#risk-matrix-table tbody').on('change', '.impact, .probability', function() {
            const row = $(this).closest('tr');
            const impact = parseInt(row.find('.impact').val()) || 0;
            const probability = parseInt(row.find('.probability').val()) || 0;
            const totalRisk = impact * probability;

            row.find('.total-risk').val(totalRisk);
        });

        // Add Row
        $('#add-kpis-row').on('click', function() {
            const rowCount = $('#kpis-table tbody tr').length;
            const newRow = `
        <tr>
            <td><input type="text" name="content[${rowCount}][col-0]" class="form-control" placeholder="{{ __('Pointer') }}"></td>
            <td><textarea name="content[${rowCount}][col-1]" class="form-control" placeholder="{{ __('Description') }}" rows="1"></textarea></td>
            <td><input type="text" name="content[${rowCount}][col-2]" class="form-control" placeholder="{{ __('Measurement Method') }}"></td>
            <td><input type="text" name="content[${rowCount}][col-3]" class="form-control" placeholder="{{ __('Goal') }}"></td>
            <td>
                <button type="button" class="btn btn-danger delete-kpis-row">
                    <i class="fas fa-trash"></i>
                </button>
            </td>
        </tr>
    `;
            $('#kpis-table tbody').append(newRow);
        });

        // Delete Row
        $('#kpis-table tbody').on('click', '.delete-kpis-row', function() {
            $(this).closest('tr').remove();
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



        // Function to collect all form data and create JSON
        window.collectAllFormData = function() {
            const data = {
                purpose: [],
                scope: [],
                responsibility: [],
                definitions: [],
                forms: [],
                procedures: [],
                risk_matrix: [],
                kpis: []
            };

            // Process purpose table
            if ($('#dynamic-table-purpose tbody tr').length > 0 && !$('#dynamic-table-purpose tbody tr td').hasClass('text-center')) {
                $('#dynamic-table-purpose tbody tr').each(function(index) {
                    const sequence = $(this).find('input[name^="content"][name$="[sequence]"]').val() || '';
                    const content = $(this).find('textarea[name^="content"][name$="[value]"], select[name^="content"][name$="[value]"]').val() || '';
                    
                    data.purpose.push({
                        sequence: sequence,
                        value: content
                    });
                });
            }

            // Process scope table
            if ($('#dynamic-table-scope tbody tr').length > 0 && !$('#dynamic-table-scope tbody tr td').hasClass('text-center')) {
                $('#dynamic-table-scope tbody tr').each(function(index) {
                    const sequence = $(this).find('input[name^="content"][name$="[sequence]"]').val() || '';
                    const content = $(this).find('textarea[name^="content"][name$="[value]"], select[name^="content"][name$="[value]"]').val() || '';
                    
                    data.scope.push({
                        sequence: sequence,
                        value: content
                    });
                });
            }

            // Process responsibility table
            if ($('#dynamic-table-responsibility tbody tr').length > 0 && !$('#dynamic-table-responsibility tbody tr td').hasClass('text-center')) {
                $('#dynamic-table-responsibility tbody tr').each(function(index) {
                    const sequence = $(this).find('input[name^="content"][name$="[sequence]"]').val() || '';
                    const content = $(this).find('textarea[name^="content"][name$="[value]"], select[name^="content"][name$="[value]"]').val() || '';
                    
                    data.responsibility.push({
                        sequence: sequence,
                        value: content
                    });
                });
            }

            // Process definition table
            if ($('#dynamic-table-definitions tbody tr').length > 0 && !$('#dynamic-table-definitions tbody tr td').hasClass('text-center')) {
                $('#dynamic-table-definitions tbody tr').each(function(index) {
                    const sequence = $(this).find('input[name^="content"][name$="[sequence]"]').val() || '';
                    const definition = $(this).find('textarea[name^="content"][name$="[value]"], select[name^="content"][name$="[value]"]').val() || '';
                    
                    data.definition.push({
                        sequence: sequence,
                        value: definition
                    });
                });
            }

            // Process all dynamic tables with fully dynamic structure
            function processFullyDynamicTable(tableSelector, dataArray) {
                if ($(tableSelector + ' tbody tr').length > 0 && !$(tableSelector + ' tbody tr td').hasClass('text-center')) {
                    $(tableSelector + ' tbody tr').each(function(index) {
                        const rowObj = {};
                        
                        // Use the name attribute to extract column index
                        $(this).find('input, textarea, select').each(function() {
                            const name = $(this).attr('name');
                            if (name) {
                                // Extract column index from name like "content[0][col-1]"
                                const colMatch = name.match(/\[col-(\d+)\]/);
                                if (colMatch && colMatch[1]) {
                                    const colIndex = colMatch[1];
                                    const colName = `col-${colIndex}`;
                                    const val = $(this).val();
                                    rowObj[colName] = val === '' ? null : val;
                                }
                            } else {
                                // For elements without name attribute (like selects with specific classes)
                                if ($(this).hasClass('impact')) {
                                    rowObj['col-2'] = $(this).val() === '' ? null : $(this).val();
                                } else if ($(this).hasClass('probability')) {
                                    rowObj['col-3'] = $(this).val() === '' ? null : $(this).val();
                                } else if ($(this).hasClass('total-risk')) {
                                    rowObj['col-4'] = $(this).val() === '' ? null : $(this).val();
                                }
                            }
                        });
                        
                        // Only push non-empty objects
                        if (Object.keys(rowObj).length > 0) {
                            dataArray.push(rowObj);
                        }
                    });
                }
            }

            // Process forms table with fully dynamic structure
            processFullyDynamicTable('#forms-table', data.forms);
            
            // Process procedure table with fully dynamic structure
            processFullyDynamicTable('#procedures-table', data.procedure);
            
            // Process risk matrix table with fully dynamic structure
            processFullyDynamicTable('#risk-matrix-table', data.risk_matrix);
            
            // Process KPIs table with fully dynamic structure
            processFullyDynamicTable('#kpis-table', data.kpis);

            return data;
        }

        // Function to send all form data
        function sendAllFormData() {
            const allData = collectAllFormData();
            
            $.ajax({
                url: '{{ route('iso_dic.procedures.saveConfigure', $procedure->id) }}',
                method: 'POST',
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content'),
                    data: allData
                },
                success: function(response) {
                    notifier.show('Success!', response.message, 'success', successImg, 4000);
                },
                error: function(xhr) {
                    notifier.show('Error!', xhr.responseJSON?.message || 'An unexpected error occurred.', 'error', errorImg, 4000);
                }
            });
        }

       

        // Event listener for the collect all data button
        $('#collect-all-data').on('click', function() {
            const allData = collectAllFormData();
            console.log('Collected Data:', allData);
            alert('تم جمع البيانات! انظر إلى console للتفاصيل.');
        });
    });
</script>