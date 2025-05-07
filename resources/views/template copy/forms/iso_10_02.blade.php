<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <title>{{$pageTitle}}</title>
    <meta charset='utf-8'>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    @include('template.partials.form.pdf-styles-advance')
    {{-- <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.rtl.min.css" integrity="sha384-dpuaG1suU0eT09tx5plTaGMLBsfDLzUCCUXOY2j/LSvXYuG6Bqs43ALlhIqAJVRb" crossorigin="anonymous"> --}}

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Cairo:wght@200..1000&family=Noto+Kufi+Arabic:wght@100..900&display=swap"
        rel="stylesheet">

</head>

<body>
    {{--    Note we include header once time , outside loop --}}
    @include('template.partials.form.header')

    <main>
        {{-- <h1 >الاعتمادات</h1> --}}
        <h2 dir="rtl" style="text-align:center;">
            <strong>الاعتمادات</strong>
        </h2>

        <div style="padding: 0 12px;">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th class="text-center font-advertising">إعداد</th>
                        <th class="text-center font-advertising">مراجعة</th>
                        <th class="text-center font-advertising">اعتماد</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="text-startt px-2">
                            <p class="top-info"><strong>الاسـم:</strong> <span>غزة حسين</span></p>
                            <p class="top-info"><strong>الوظيفة:</strong> <span>مساعد إستشاري</span></p>
                            <p class="top-info"><strong>التوقيـع:</strong> <span>..........................</span></p>
                        </td>

                        <td class="text-startt">
                            <p class="top-info"><strong>الاسـم:</strong> <span>محمود غنيم</span></p>
                            <p class="top-info"><strong>الوظيفة:</strong> <span>مدير الجودة</span></p>
                            <p class="top-info"><strong>التوقيـع:</strong> <span>.............................</span>
                            </p>
                        </td>

                        <td class="text-startt px-2">
                            <p class="top-info"><strong>الاسـم:</strong> <span>رياض الغيلي</span></p>
                            <p class="top-info"><strong>الوظيفة:</strong> <span>الرئيس التنفيذي</span></p>
                            <p class="top-info"><strong>التوقيـع:</strong> <span>............................</span></p>
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


    @include('template.partials.form.footer')

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
                    @forelse ($purposes->content as $index => $row)
                        <tr>
                            <td class="number"></td>
                            <td class="sub-number">{{ $row['sequence'] }}</td>
                            <td class="description">{{ $row['value'] }}</td>
                        </tr>
                    @endforeach
                    <tr>
                        <td class="number">2</td>
                        <td class="text-startt" colspan="2">
                            مجال التطبيق
                        </td>
                    </tr>
                    @forelse ($scopes->content as $index => $row)
                        <tr>
                            <td class="number"></td>
                            <td class="sub-number">{{ $row['sequence'] }}</td>
                            <td class="description">{{ $row['value'] }}</td>
                        </tr>
                    @endforeach
                    <tr>
                        <td class="number">3</td>
                        <td class="text-startt" colspan="2">
                            المسؤولية
                        </td>

                    </tr>
                    @forelse ($responsibilities->content as $index => $row)
                        <tr>
                            <td class="number"></td>
                            <td class="sub-number">{{ $row['sequence'] }}</td>
                            <td class="description">{{ $row['value'] }}</td>
                        </tr>
                    @endforeach
                    <tr>
                        <td class="number">4</td>
                        <td class="text-startt" colspan="2">
                            التعريفات
                        </td>
                    </tr>
                    @forelse ($definitions->content as $index => $row)
                        <tr>
                            <td class="number"></td>
                            <td class="sub-number">{{ $row['sequence'] }}</td>
                            <td class="description">{{ $row['value'] }}</td>
                        </tr>
                    @endforeach

                </tbody>
            </table>
        </div>
    </main>


    <div style="page-break-before: always;"></div>

    <main>
        {{-- <p style="text-align:right;">النماذج المستخدمة <span>-5</span></p> --}}
        <h3 dir="rtl" style="text-align:right;">
            <strong>{{__('forms')}}</strong>
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
                        @forelse ($forms->content as $index => $row)
                            <tr>
                                <td>{{ $index +1 }}</td>
                                <td>{{ $row['col-0'] ?? '' }}</td>
                                <td>{{ $row['col-1'] ?? '' }}</td>
                                <td>{{ $row['col-2'] ?? '' }}</td>
                                <td>{{ $row['col-3'] ?? '' }}</td>
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
            <strong>{{__('procedures')}}</strong>
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
                        @forelse ($procedures->content as $index => $row)
                            <tr>
                                
                                <td>{{ $index +1 }}</td>
                                <td>{{ $row['col-0'] ?? '' }}</td>
                                <td>{{ $row['col-1'] ?? '' }}</td>
                                <td>{{ $row['col-2'] ?? '' }}</td>
                                <td>{{ $row['col-3'] ?? '' }}</td>
                                <td>{{ $row['col-4'] ?? '' }}</td>
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
    </main>

    {{-- <div style="page-break-before: always;"></div> --}}



    @include('template.partials.form.footer')

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous">
    </script>

</body>

</html>
