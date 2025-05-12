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
    {{-- detials-tab --}}
    <li class="nav-item" role="presentation">
        <button class="nav-link" id="detials-tab" data-bs-toggle="tab" data-bs-target="#detials" type="button"
            role="tab" aria-controls="detials" aria-selected="false">
            {{ __('Document Detials') }}
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
            


        </form>
    </div>

    {{-- scope --}}
    <div class="tab-pane fade" id="scope" role="tabpanel" aria-labelledby="scope-tab">
        <form id="form-scope">
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
                                        @forelse ($jobRoles as $item)
                                                <option value="{{ $item->id }}"
                                                    {{ $row['value'] == $item->id ? 'selected' : '' }}>
                                                    {{ $item->title }}
                                                </option>
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
                                    <select name="content[{{ $index }}][col-3]" class="form-control">
                                        <option value="">اختر مكان الحفظ</option>
                                        @foreach ($departments as $department)
                                            <option value="{{ $department->id }}"
                                                {{ isset($row['col-3']) && $row['col-3'] == $department->id ? 'selected' : '' }}>
                                                {{ $department->name }}
                                            </option>
                                        @endforeach
                                    </select>
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
                            <th>النموذج المستخدم</th>
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
                                            class="form-control" placeholder="الإجراءات">
                                    </td>
                                    <td>
                                        <select name="content[{{ $index }}][col-1]" class="form-control ">
                                            <option value="Customer" {{ isset($row['col-1']) && $row['col-1'] == 'Customer' ? 'selected' : '' }}>{{ __('Customer') }}</option>
                                            <option value="Supplier" {{ isset($row['col-1']) && $row['col-1'] == 'Supplier' ? 'selected' : '' }}>{{ __('Supplier') }}</option>
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
                                        <select name="content[{{ $index }}][col-4]" class="form-control">
                                            <option value="">اختر المسؤول</option>
                                            @foreach ($jobRoles as $jobRole)
                                                <option value="{{ $jobRole->id }}"
                                                    {{ isset($row['col-4']) && $row['col-4'] == $jobRole->id ? 'selected' : '' }}>
                                                    {{ $jobRole->name }}
                                                </option>
                                            @endforeach
                                        </select>
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
                            <th>{{ __('Measurement Method') }}</th>
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
                                            class="form-control " placeholder="{{ __('Measurement Method') }}"
                                            value="{{ $row['col-2'] ?? '' }}">
                                    </td>
                                    <td>
                                        <input type="text" name="content[{{ $index }}][col-3]"
                                            class="form-control" value="{{ $row['col-3'] ?? '' }}">
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
            </form>
        </div>
    </div>

    {{-- references --}}
    <div class="tab-pane fade" id="references" role="tabpanel" aria-labelledby="references-tab">
        <h3>{{ __('References') }}</h3>
        <p>{{ __('This is the section for references. You can add details here.') }}</p>
    </div>

    {{-- detials --}}
    <div class="tab-pane fade" id="detials" role="tabpanel" aria-labelledby="detials-tab">
        <form id="document-detials">
            @csrf
            <div class="row align-items-center pb-2">
                {{-- <h3 class="col">{{ __('Detials') }}</h3> --}}
            </div>
            <div class="mb-3">
                
                <div class="col-md-6 mb-3">
                    <label class="form-label">{{ __('Department') }} <span
                            class="text-danger">*</span></label>
                    <select name="department"
                        class="form-select @error('department') is-invalid @enderror" required>
                        <option value="">{{ __('Select Department') }}</option>
                        @foreach ($departments as $department)
                            <option value="{{ $department->id }}"
                                @if (old('department') == $department->id) selected @endif>
                                {{ __($department->name) }}
                            </option>
                        @endforeach
                    </select>
                    @error('department')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <button type="button" class="btn btn-info save-and-continue float-end"
                    data-next-tab="references">{{ __('Save and Continue') }} </button>
            </div>
        </form>
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
            const firstItem = Array.isArray(jobRoles) ?
                jobRoles[0] :
                Object.values(jobRoles)[0];

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
                    inputField = `
                        <select name="content[${rowCount}][value]" class="form-control showsearch">
                            <option value="">اختر وظيفة</option>
                            @foreach ($jobRoles as $option)
                                <option value="{{ $option->id }}">{{ $option->title }}</option>
                            @endforeach
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


        $('#add-form-row').on('click', function() {
            const rowCount = $('#forms-table tbody tr').length;
            const newRow = `
                    <tr>
                        <td>
                            <input type="text" name="content[${rowCount}][col-0]" class="form-control" placeholder="أدخل اسم النموذج">
                        </td>
                        <td><input type="text" name="content[${rowCount}][col-1]" class="form-control" placeholder="أدخل رقم النموذج"></td>
                        <td><input type="text" name="content[${rowCount}][col-2]" class="form-control" placeholder="أدخل فترة الحفظ" value="3 سنوات" readonly></td>
                        <td>
                            <select name="content[${rowCount}][col-3]" class="form-control">
                                <option value="">اختر مكان الحفظ</option>
                                @foreach ($departments as $department)
                                    <option value="{{ $department->id }}" {{ isset($row['col-3']) && $row['col-3'] == $department->id ? 'selected' : '' }}>
                                        {{ $department->name }}
                                    </option>
                                @endforeach
                            </select>
                        </td>
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

            reposabilitySelect = `
                        <select name="content[${rowCount}][col-1]" class="form-control showsearch">
                            <option value="Customer">{{ __('Customer') }}</option>
                            <option value="Supplier">{{ __('Supplier') }}</option>
                        </select>
                    `;
            const newRow = `
                    <tr>
                        <td>
                           <input type="text" name="content[${rowCount}][col-0]" class="form-control" placeholder="أدخل الإجراء">
                        </td>
                         <td>
                            ${reposabilitySelect}
                        </td>
                        <td><input type="text" name="content[${rowCount}][col-2]" class="form-control" placeholder="النموذج المستخدم"></td>
                        <td><input type="text" name="content[${rowCount}][col-3]" class="form-control" placeholder="التحديث"></td>
                        <td>
                            <select name="content[${rowCount}][col-4]" class="form-control">
                                <option value="">اختر المسؤولية</option>
                                @foreach ($jobRoles as $item)
                                    <option value="{{ $item->id }}" {{ isset($row['col-4']) && $row['col-4'] == $item->id ? 'selected' : '' }}>
                                        {{ $item->title }}
                                    </option>
                                @endforeach
                            </select>
                        </td>
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
            let impactSelect = '';
            let probabilitySelect = '';

            for (let i = 1; i <= 5; i++) {
                impactSelect += `<option value="${i}">${i}</option>`;
                probabilitySelect += `<option value="${i}">${i}</option>`;
            }

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
                    <select name="content[${rowCount}][col-2]" class="form-control impact ">
                        <option value="">اختر قيمة</option>
                        ${impactSelect}
                    </select>
                </td>
                <td>
                    <select name="content[${rowCount}][col-3]" class="form-control probability ">
                        <option value="">اختر قيمة</option>
                        ${probabilitySelect}
                    </select>
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
            <td><input type="text" name="content[${rowCount}][col-2]" class="form-control " placeholder="{{ __('Measurement Method') }}"></td>
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
            if ($('#dynamic-table-purpose tbody tr').length > 0 && !$('#dynamic-table-purpose tbody tr td')
                .hasClass('text-center')) {
                $('#dynamic-table-purpose tbody tr').each(function(index) {
                    const sequence = $(this).find('input[name^="content"][name$="[sequence]"]')
                        .val() || '';
                    const content = $(this).find(
                        'textarea[name^="content"][name$="[value]"], select[name^="content"][name$="[value]"]'
                    ).val() || '';

                    data.purpose.push({
                        sequence: sequence,
                        value: content
                    });
                });
            }

            // Process scope table
            if ($('#dynamic-table-scope tbody tr').length > 0 && !$('#dynamic-table-scope tbody tr td')
                .hasClass('text-center')) {
                $('#dynamic-table-scope tbody tr').each(function(index) {
                    const sequence = $(this).find('input[name^="content"][name$="[sequence]"]')
                        .val() || '';
                    const content = $(this).find(
                        'textarea[name^="content"][name$="[value]"], select[name^="content"][name$="[value]"]'
                    ).val() || '';

                    data.scope.push({
                        sequence: sequence,
                        value: content
                    });
                });
            }

            // Process responsibility table
            if ($('#dynamic-table-responsibility tbody tr').length > 0 && !$(
                    '#dynamic-table-responsibility tbody tr td').hasClass('text-center')) {
                $('#dynamic-table-responsibility tbody tr').each(function(index) {
                    const sequence = $(this).find('input[name^="content"][name$="[sequence]"]')
                        .val() || '';
                    const content = $(this).find(
                        'textarea[name^="content"][name$="[value]"], select[name^="content"][name$="[value]"]'
                    ).val() || '';

                    data.responsibility.push({
                        sequence: sequence,
                        value: content
                    });
                });
            }

            // Process definition table
            if ($('#dynamic-table-definitions tbody tr').length > 0 && !$(
                    '#dynamic-table-definitions tbody tr td').hasClass('text-center')) {
                $('#dynamic-table-definitions tbody tr').each(function(index) {
                    const sequence = $(this).find('input[name^="content"][name$="[sequence]"]')
                        .val() || '';
                    const definition = $(this).find(
                        'textarea[name^="content"][name$="[value]"], select[name^="content"][name$="[value]"]'
                    ).val() || '';

                    data.definitions.push({
                        sequence: sequence,
                        value: definition
                    });
                });
            }

            // Process all dynamic tables with fully dynamic structure
            function processFullyDynamicTable(tableSelector, dataArray) {
                if ($(tableSelector + ' tbody tr').length > 0 && !$(tableSelector + ' tbody tr td')
                    .hasClass('text-center')) {
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
                                    rowObj['col-2'] = $(this).val() === '' ? null : $(this)
                                        .val();
                                } else if ($(this).hasClass('probability')) {
                                    rowObj['col-3'] = $(this).val() === '' ? null : $(this)
                                        .val();
                                } else if ($(this).hasClass('total-risk')) {
                                    rowObj['col-4'] = $(this).val() === '' ? null : $(this)
                                        .val();
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
            processFullyDynamicTable('#procedures-table', data.procedures);

            // Process risk matrix table with fully dynamic structure
            processFullyDynamicTable('#risk-matrix-table', data.risk_matrix);

            // Process KPIs table with fully dynamic structure
            processFullyDynamicTable('#kpis-table', data.kpis);

            return data;
        }

        function sendAllFormData() {
            let formData = new FormData();

            if (typeof window.collectAllFormData === 'function') {
                try {
                    const allProcedureData = window.collectAllFormData();
                    formData.append('procedure_setup_data', JSON.stringify(allProcedureData));
                    formData.append('category_id', {{ $procedure->category_id }});
                    console.log('Added procedure setup data:', allProcedureData);
                } catch (error) {
                    console.error('Error collecting procedure setup data:', error);
                }
            } else {
                console.warn('collectAllFormData function not found in current context');
            }

            $.ajax({
                url: '{{ route('tenant.document.procedures.saveConfigure', $procedure->id) }}',
                method: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    notifier.show('Success!', response.message, 'success', successImg, 4000);
                },
                error: function(xhr) {
                    notifier.show('Error!', xhr.responseJSON?.message ||
                        'An unexpected error occurred.', 'error', errorImg, 4000);
                }
            });
        }


        $('.save-and-continue').on('click', function(event) {
            event.preventDefault();
            sendAllFormData();
        });

        // إضافة سلوك التحديث التلقائي لرقم النموذج عند اختيار نموذج من القائمة
        $(document).on('change', '.sample-select', function() {
            const selectedOption = $(this).find('option:selected');
            const rowIndex = $(this).data('index');

        });
    });
</script>

<script>
    $(document).ready(function() {
        //placeholder variables
        const variables = {
            companyName: 'أسdaf',
            isoSystemAndCodeAndVersion: 'ISO 9001:2015',
            companyFirstName: 'محمد',
            companyShortCode: "ASDF"
        };

        // وظيفة استبدال البليسهولدر
        function replacePlaceholders(text, vars) {
            if (typeof text !== 'string') return text;
            return text.replace(/\{\{(.*?)\}\}/g, function(match, key) {
                key = key.trim();
                return typeof vars[key] !== 'undefined' ? vars[key] : match;
            });
        }

        // Replace placeholder
        $('#replacePlaceholdersBtn').on('click', function() {

            // Replace placeholders in all text fields or textareas
            $('input[type="text"], textarea').each(function() {
                const currentValue = $(this).val();
                const newValue = replacePlaceholders(currentValue, variables);
                $(this).val(newValue);
            });
        });

    });
</script>
