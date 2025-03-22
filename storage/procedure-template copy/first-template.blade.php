<!DOCTYPE html>
<html lang="" dir="">

<head>
    <title>Advance Example</title>
    @include('procedure-template.pdf-styles-advance')
    {{-- <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous"> --}}

</head>
    
<body>
    {{--    Note we include header once time , outside loop --}}
    @include('procedure-template.header')
    
    <main>

        {{-- <h1 >الاعتمادات</h1> --}}
        <p dir="rtl" style="text-align:center; font-size:20pt;">
            <strong>الاعتمادات</strong>
        </p>


        <div style="padding: 0 12px;">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th class="text-center font-advertising">اعتماد</th>
                        <th class="text-center font-advertising">مراجعة</th>
                        <th class="text-center font-advertising">إعداد</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class=" text-startt px-2" style="line-height:11px;">
                            <p class="top-info"><span>رياض الغيلي</span><strong>الاسـم:</strong></p>
                            <p class="top-info"><span>الرئيس التنفيذي</span><strong>الوظيفة:</strong> </p>
                            <p class="top-info"><span>............................</span><strong>التوقيـع:</strong> </p>
                        </td>
                        <td class="text-startt " style="line-height:11px;">
                            <p class="top-info"><span>محمود غنيم</span><strong>الاسـم:</strong> </p>
                            <p class="top-info"><span>مدير الجودة</span><strong>الوظيفة:</strong> </p>
                            <p class="top-info"><span>.............................</span><strong>التوقيـع:</strong>
                            </p>
                        </td>
                        <td class="text-startt px-2" style="line-height:11px;">
                            <p class="top-info"> <span>غزة حسين</span><strong>الاسـم:</strong></p>
                            <p class="top-info"><span>مساعد إستشاري</span><strong>الوظيفة:</strong> </p>
                            <p class="top-info"> <span>..........................</span><strong>التوقيـع:</strong></p>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        {{--  التوزيعات --}}

        <p dir="rtl" style="text-align:center; font-size:20pt;">
            <strong>التوزيعات</strong>
        </p>

        <div dir="rtl" style="padding: 0 12px;">
            <table>
                <thead>
                    <tr>
                        <th>جهات التوزيع</th>
                        <th>كود الإدارة</th>
                        <th>عدد النسخ</th>
                        <th>م</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>الإدارة العامة</td>
                        <td><strong>GM</strong></td>
                        <td>1</td>
                        <td>1</td>
                    </tr>
                    <tr>
                        <td>قسم الجودة</td>
                        <td><strong>IMS</strong></td>
                        <td>أصل الوثيقة</td>
                        <td>2</td>
                    </tr>
                    <tr>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>3</td>
                    </tr>
                    <tr>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>4</td>
                    </tr>
                    <tr>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>5</td>
                    </tr>
                </tbody>
            </table>
        </div>
        {{-- سجلات التعديل --}}


        <p dir="rtl" style="text-align:center; font-size:20pt;">
            <strong>سجل التعديلات</strong>
        </p>

        <div style="padding: 0 12px;">
            <table>
                <thead>
                    <tr>
                        <th>القائم بالتعديل</th>
                        <th>وصف التعديل</th>
                        <th>تاريخ الإصدار</th>
                        <th>رقم الإصدار</th>
                        <th>م</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>01</td>
                    </tr>
                    <tr>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>02</td>
                    </tr>
                    <tr>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>03</td>
                    </tr>
                    <tr>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>04</td>
                    </tr>
                    <tr>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>05</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </main>

   @php
       $text=".الغرض من هذه الوثيقة هو تقديم عملية لتحديد وتوثيق السياق الداخلي والخارجي لشركة أسداف لخدمات الأعمال";
   @endphp
    {{-- @include('procedure-template.footer') --}}

    <div style="page-break-before: always;"></div>
    <p  style="height: 100px;"></p>

    <main class="tabs" >
        <p  style="text-align:justify; font-size:14pt;"></p>
        <div >
            <table>
                <tbody>
                    <tr>
                        <td class="text-startt" colspan="2">
                            الغرض
                        </td>
                        <td class="number">1</td>
                    </tr>
                    <tr>
                        <td class="description"  >
                            {{$text}}
                        </td>
                        <td class="sub-number">1-1</td>
                        <td class="number"></td>
                    </tr>
                    <tr>
                        <td class="description">
                            <p dir="ltr">ISO 9001: 2015  تحديد احتياجات وتوقعات الأطراف المهتمة بنظام إدارة الجودة  </p>
                        </td>
                        <td class="sub-number">1-2</td>
                        <td class="number"></td>
                    </tr>
                    <tr>
                        <td class="text-startt" colspan="2">
                            مجال التطبيق
                        </td>
                        <td class="number">2</td>
                    </tr>
                    <tr>
                        <td class="description">
                            <p> تطبق هذه الوثيقة على جميع عمليات وخدمات شركة أسداف لخدمات الأعمال </p> 
                        </td>
                        <td class="sub-number">2-1</td>
                        <td class="number"></td>
                    </tr>
                    <tr>
                        <td class="description"><p class="text-justify"></p></td>
                        <td class="sub-number">2-2</td>
                        <td class="number"></td>

                    </tr>
                    <tr>
                        <td class="text-startt" colspan="2">
                            المسؤولية
                        </td>
                        
                        <td class="number">3</td>
                    </tr>
                    <tr>
                        <td class="description">
                            <p> مدير إدارة  </p>
                        </td>
                        <td class="sub-number">3-1</td>
                        <td class="number"></td>
                    </tr>
                    <tr>
                        <td class="description">
                            <p >رئيس لجنة الجودة.</p>
                        </td>
                        <td class="sub-number">3-2</td>
                        <td class="number"></td>

                    </tr>
                    <tr>
                        <td class="description">
                            <p ></p>
                        </td>
                        <td class="sub-number">3-3</td>
                        <td class="number"></td>
                    </tr>
                  
                </tbody>
            </table>
        </div>
    </main>

    <div style="page-break-before: always;"></div>

    <main>
        <h1>Title of the Third Page</h1>
        <p>This is the content for the third page.</p>
    </main>
    
    @include('procedure-template.footer')


    {{-- <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous">
    </script> --}}

</body>

</html>
