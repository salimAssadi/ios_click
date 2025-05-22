<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <title>{{ $pageTitle }}</title>
    <meta charset='utf-8'>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    @include('template.partials.procedure.pdf-styles-advance')
    {{-- <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.rtl.min.css" integrity="sha384-dpuaG1suU0eT09tx5plTaGMLBsfDLzUCCUXOY2j/LSvXYuG6Bqs43ALlhIqAJVRb" crossorigin="anonymous"> --}}

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Cairo:wght@200..1000&family=Noto+Kufi+Arabic:wght@100..900&display=swap"
        rel="stylesheet">
</head>

<body>
    {{--    Note we include header once time , outside loop --}}
    @include('template.partials.procedure.header')

    <main>
        {{-- <h1 >الاعتمادات</h1> --}}
        <h2 dir="rtl" style="text-align:center;">
            <strong>{{ __('Certifications') }}</strong>
        </h2>

        <div style="padding: 0 12px;">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th class="text-center font-advertising">{{ __('Preparer') }}</th>
                        <th class="text-center font-advertising">{{ __('Reviewer') }}</th>
                        <th class="text-center font-advertising">{{ __('Approver') }}</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        {{-- Preparer --}}
                        <td class="text-start px-2" style="text-align: start;">
                            @if (!empty($preparers))
                                @foreach ($preparers as $index => $preparer)
                                        <div style="margin-bottom: 12px;">
                                        <p class="top-info"><strong>{{ __('Name') }}:</strong> <span>{{ $preparer->name ?? '-' }}</span></p>
                                        <p class="top-info"><strong>{{ __('Job') }}:</strong> <span>{{ $preparer->position->title ?? '-' }}</span></p>
                                        <p class="top-info" style="display: flex; align-items: center; gap: 5px;">
                                            <strong>{{ __('Signature') }}:</strong>
                                            @if ($preparer->signature_pad_data)
                                                <img src="{{ $preparer->signature_pad_data }}" alt="signature" style="height: 20px; object-fit: contain;">
                                            @else
                                                <span>..........................</span>
                                            @endif
                                        </p>
                                    </div>
                                    @if ($index !== count($preparers) - 1)
                                        <hr>
                                    @endif
                                @endforeach
                            @else
                                <p class="top-info"><strong>{{ __('Name') }}:</strong> <span>-</span></p>
                                <p class="top-info"><strong>{{ __('Job') }}:</strong> <span>-</span></p>
                                <p class="top-info"><strong>{{ __('Signature') }}:</strong> <span>..........................</span></p>
                            @endif
                        </td>
            
                        {{-- Reviewer --}}
                        <td class="text-start px-2" style="text-align: start;">
                            @if (!empty($reviewers))
                                @foreach ($reviewers as $index => $reviewer)
                                    <div style="margin-bottom: 12px;">
                                        <p class="top-info" style="text-align: start;"><strong>{{ __('Name') }}:</strong> <span>{{ $reviewer->name ?? '-' }}</span></p>
                                        <p class="top-info" style="text-align: start;"><strong>{{ __('Job') }}:</strong> <span>{{ $reviewer->position->title ?? '-' }}</span></p>
                                        <p class="top-info" style="display: flex; align-items: center; gap: 5px; text-align: start;">
                                            <strong>{{ __('Signature') }}:</strong>
                                            @if ($reviewer->signature_pad_data)
                                                <img src="{{ $reviewer->signature_pad_data }}" alt="signature" style="height: 20px; object-fit: contain;">
                                            @else
                                                <span>.............................</span>
                                            @endif
                                        </p>
                                    </div>
                                    @if ($index !== count($reviewers) - 1)
                                        <hr>
                                    @endif
                                @endforeach
                            @else
                                <p class="top-info"><strong>{{ __('Name') }}:</strong> <span>-</span></p>
                                <p class="top-info"><strong>{{ __('Job') }}:</strong> <span>-</span></p>
                                <p class="top-info"><strong>{{ __('Signature') }}:</strong> <span>.............................</span></p>
                            @endif
                        </td>
            
                        {{-- Approver --}}
                        <td class="text-start px-2" style="vertical-align: top; text-align: start;">
                            @if (!empty($approver))
                                <div style="text-align: start;">
                                    <p class="top-info" style="text-align: start;"><strong>{{ __('Name') }}:</strong> <span>{{ $approver->name ?? '-' }}</span></p>
                                    <p class="top-info" style="text-align: start;"><strong>{{ __('Job') }}:</strong> <span>{{ $approver->position->title ?? '-' }}</span></p>
                                    <p class="top-info" style="display: flex; align-items: center; gap: 5px; text-align: start;">
                                        <strong>{{ __('Signature') }}:</strong>
                                        @if ($approver->signature_pad_data)
                                            <img src="{{ $approver->signature_pad_data }}" alt="signature" style="height: 20px; object-fit: contain;">
                                        @else
                                            <span>............................</span>
                                        @endif
                                    </p>
                                </div>
                            @else
                                <p class="top-info"><strong>{{ __('Name') }}:</strong> <span>-</span></p>
                                <p class="top-info"><strong>{{ __('Job') }}:</strong> <span>-</span></p>
                                <p class="top-info"><strong>{{ __('Signature') }}:</strong> <span>............................</span></p>
                            @endif
                        </td>
                    </tr>
                </tbody>
            </table>
            
        </div>

        {{--  التوزيعات --}}


        <h2 dir="rtl" style="text-align:center;">
            <strong>التوزيعات</strong>
        </h2>
        <div dir="rtl" style="padding: 0 12px;">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th class="squen">م</th>
                        <th>عدد النسخ</th>
                        <th>جهات التوزيع</th>
                        <th>كود الإدارة</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>1</td>
                        <td>1</td>
                        <td>الإدارة العامة</td>
                        <td><strong>GM</strong></td>
                    </tr>
                    <tr>
                        <td>2</td>
                        <td>أصل الوثيقة</td>
                        <td>قسم الجودة</td>
                        <td><strong>IMS</strong></td>
                    </tr>
                    <tr>
                        <td>3</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                    </tr>
                    <tr>
                        <td>4</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                    </tr>
                    <tr>
                        <td>5</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                    </tr>
                </tbody>
            </table>
        </div>

        {{-- سجلات التعديل --}}



        <h2 dir="rtl" style="text-align:center;">
            <strong>سجل التعديلات</strong>
        </h2>

        <div style="padding: 0 12px;">
            <table>
                <thead>
                    <tr>
                        <th class="squen">م</th>
                        <th>رقم الإصدار</th>
                        <th>تاريخ الإصدار</th>
                        <th>وصف التعديل</th>
                        <th>القائم بالتعديل</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>01</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                    </tr>
                    <tr>
                        <td>02</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                    </tr>
                    <tr>
                        <td>03</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                    </tr>
                    <tr>
                        <td>04</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                    </tr>
                    <tr>
                        <td>05</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </main>


    @include('template.partials.procedure.footer')

    <div style="page-break-before: always;"></div>

    <main class="tabs">
        <div>
            <table>
                <tbody>
                    <tr>
                        <td class="number">1</td>
                        <td class="text-startt" colspan="2">
                            الغرض
                        </td>
                    </tr>
                    @forelse ($purposes as $index => $row)
                        <tr>
                            <td class="number"></td>
                            <td class="sub-number">{{ $row['sequence'] }}</td>
                            <td class="description">{{ $row['value'] }}</td>
                        </tr>
                    @empty
                    @endforelse
                    <tr>
                        <td class="number">2</td>
                        <td class="text-startt" colspan="2">
                            مجال التطبيق
                        </td>
                    </tr>
                    @forelse ($scopes as $index => $row)
                        <tr>
                            <td class="number"></td>
                            <td class="sub-number">{{ $row['sequence'] }}</td>
                            <td class="description">{{ $row['value'] }}</td>
                        </tr>
                    @empty
                    @endforelse
                    <tr>
                        <td class="number">3</td>
                        <td class="text-startt" colspan="2">
                            المسؤولية
                        </td>

                    </tr>
                    @forelse ($responsibilities as $index => $row)
                        <tr>
                            <td class="number"></td>
                            <td class="sub-number">{{ $row['sequence'] }}</td>
                            <td class="description">{{ $row['value'] }}</td>
                        </tr>
                    @empty
                    @endforelse
                    <tr>
                        <td class="number">4</td>
                        <td class="text-startt" colspan="2">
                            التعريفات
                        </td>
                    </tr>
                    @forelse ($definitions as $index => $row)
                        <tr>
                            <td class="number"></td>
                            <td class="sub-number">{{ $row['sequence'] }}</td>
                            <td class="description">{{ $row['value'] }}</td>
                        </tr>
                    @empty
                    @endforelse

                </tbody>
            </table>
        </div>
    </main>


    <div style="page-break-before: always;"></div>

    <main>
        {{-- <p style="text-align:right;">النماذج المستخدمة <span>-5</span></p> --}}
        <h3 dir="rtl" style="text-align:right;">
            <strong>{{ __('forms') }}</strong>
        </h3>
        <div>
            <table id="forms-table" class="table text-center table-bordered">
                <thead>
                    <tr>
                        <th class="squen">م</th>
                        <th>أسم النموذج</th>
                        <th>رقم النموذج</th>
                        <th style="width: 200px;">فترة الحفظ</th>
                        <th>مكان الحفظ</th>
                    </tr>
                </thead>
                <tbody>
                    @if ($forms)
                        @forelse ($forms as $index => $row)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $row['col-0'] ?? '' }}</td>
                                <td>{{ $row['col-1'] ?? '' }}</td>
                                <td>{{ $row['col-2'] ?? '' }}</td>
                                <td>
                                    @foreach ($departments as $dept)
                                        @if ($dept->id == $row['col-3'])
                                            {{ $dept->name }}
                                        @endif
                                    @endforeach
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center">لا توجد بيانات</td>
                            </tr>
                        @endforelse
                    @endif
                </tbody>
            </table>
        </div>
        {{-- procedure --}}
        <h3 dir="rtl" style="text-align:right;">
            <strong>{{ __('procedures') }}</strong>
        </h3>
        <div>
            <table id="forms-table" class="table text-center table-bordered">
                <thead>
                    <tr>
                        <th class="squen">م</th>
                        <th>الإجراء</th>
                        <th>المسؤولية</th>
                        <th>النموذج المستخدم</th>
                        <th>التحديث</th>
                        <th>مسؤولية التحديث</th>
                    </tr>
                </thead>
                <tbody>
                    @if ($procedures)
                        @forelse ($procedures as $index => $row)
                            <tr>

                                <td>{{ $index + 1 }}</td>
                                <td style="text-align:start;">{{ $row['col-0'] ?? '' }}</td>
                                <td>{{ __($row['col-1']) ?? '' }}</td>
                                <td>{{ $row['col-2'] ?? '' }}</td>
                                <td>{{ $row['col-3'] ?? '' }}</td>
                                <td>
                                    @foreach ($users as $user)
                                    @if ($user->user_id == $row['col-4'])
                                        {{ $user->name }}
                                    @endif
                                    @endforeach
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center">لا توجد بيانات</td>
                            </tr>
                        @endforelse
                    @endif
                </tbody>
            </table>
        </div>

         {{-- risk_matrix --}}
         <h3 dir="rtl" style="text-align:right;">
            <strong>{{ __('risk_matrix') }}</strong>
        </h3>
        <div>
            <table id="forms-table" class="table text-center table-bordered">
                <thead>
                    <tr>
                        <th class="squen">م</th>
                        <th>عامل المخاطر</th>
                        <th>وصف المخاطر</th>
                        <th>درجة التأثير (1 - 5)</th>
                        <th>درجة الاحتمالية(1-5)</th>
                        <th>درجة المخاطر </th>
                        <th>طريقة إدارة الخطر</th>
                        
                    </tr>
                </thead>
                <tbody>
                    @if ($risk_matrix)
                        @forelse ($risk_matrix as $index => $row)
                            <tr>

                                <td>{{ $index + 1 }}</td>
                                <td style="text-align:start;">{{ $row['col-0'] ?? '' }}</td>
                                <td>{{ __($row['col-1']) ?? '' }}</td>
                                <td>{{ $row['col-2'] ?? '' }}</td>
                                <td>{{ $row['col-3'] ?? '' }}</td>
                                <td>{{ $row['col-4'] ?? '' }}</td>
                                <td>{{ $row['col-5'] ?? '' }}</td>
                                
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center">لا توجد بيانات</td>
                            </tr>
                        @endforelse
                    @endif
                </tbody>
            </table>
        </div>

          {{-- kpis --}}
          <h3 dir="rtl" style="text-align:right;">
            <strong>{{ __('kpis') }}</strong>
        </h3>
        <div>
            <table id="forms-table" class="table text-center table-bordered">
                <thead>
                    <tr>
                        <th class="squen">م</th>
                        <th> {{ __('Pointer') }}</th>
                        <th>{{ __('Description') }}</th>
                        <th>{{ __('Measurement Method') }}</th>
                        <th>{{ __('Goal') }}</th>
                        
                    </tr>
                </thead>
                <tbody>
                    @if ($kpis)
                        @forelse ($kpis as $index => $row)
                            <tr>

                                <td>{{ $index + 1 }}</td>
                                <td style="text-align:start;">{{ $row['col-0'] ?? '' }}</td>
                                <td>{{ __($row['col-1']) ?? '' }}</td>
                                <td>{{ $row['col-2'] ?? '' }}</td>
                                <td>{{ $row['col-3'] ?? '' }}</td>
                                
                            </tr>
                        @empty
                            <tr>
                                <td colspan="2" class="text-center">لا توجد بيانات</td>
                            </tr>
                        @endforelse
                    @endif
                </tbody>
            </table>
        </div>

         {{-- refernces --}}
         <h3 dir="rtl" style="text-align:right;">
            <strong>9-{{ __('References') }}</strong>
        </h3>
        <div>
            <table id="forms-table" class="table text-center table-bordered">
                <thead>
                    <tr>
                        <th class="squen">م</th>
                        <th> {{ __('References') }}</th>
                    
                        
                    </tr>
                </thead>
                <tbody>
                    @if ($references)
                        @forelse ($references as $index => $row)
                            <tr>

                                <td>{{ $index + 1 }}</td>
                                <td style="text-align:start;">{{ $row['value'] ?? '' }}</td>
                                
                            </tr>
                        @empty
                            <tr>
                                <td colspan="2" class="text-center">لا توجد بيانات</td>
                            </tr>
                        @endforelse
                    @endif
                </tbody>
            </table>
        </div>

    </main>

    {{-- <div style="page-break-before: always;"></div> --}}



    @include('template.partials.procedure.footer')

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous">
    </script>

</body>

</html>
