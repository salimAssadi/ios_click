<!DOCTYPE html>
<html dir="rtl" lang="ar">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>إجراء رئيسي - {{ $document_number }}</title>
    <style>
        body {
            font-family: 'cairo', sans-serif;
            line-height: 1.6;
            color: #333;
            margin: 0;
            padding: 0;
        }
        .container {
            width: 100%;
            margin: 0 auto;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            padding: 10px;
            border-bottom: 2px solid #ddd;
        }
        .logo {
            max-width: 150px;
            height: auto;
            margin-bottom: 10px;
        }
        .document-info {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .document-info th, .document-info td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: right;
        }
        .document-info th {
            background-color: #f2f2f2;
        }
        .section {
            margin-bottom: 15px;
        }
        .section-title {
            font-weight: bold;
            border-bottom: 1px solid #ddd;
            padding-bottom: 5px;
            margin-bottom: 10px;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            font-size: 12px;
            border-top: 1px solid #ddd;
            padding-top: 10px;
        }
        .page-break {
            page-break-after: always;
        }
        .signature-box {
            border: 1px solid #ddd;
            padding: 15px;
            margin-top: 20px;
            min-height: 80px;
        }
        .row {
            display: block;
            width: 100%;
            clear: both;
            margin-bottom: 10px;
        }
        .col-6 {
            width: 50%;
            float: right;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            @if(isset($company_logo))
                <img src="{{ $company_logo }}" alt="شعار الشركة" class="logo">
            @endif
            <h2>إجراء رئيسي</h2>
            <h3>{{ $document_number }}</h3>
        </div>
        
        <table class="document-info">
            <tr>
                <th style="width: 25%;">رقم الوثيقة</th>
                <td>{{ $document_number }}</td>
                <th style="width: 25%;">تاريخ الإصدار</th>
                <td>{{ date('Y-m-d') }}</td>
            </tr>
            <tr>
                <th>عنوان الإجراء</th>
                <td colspan="3">{{ $data['title'] ?? 'إجراء رئيسي' }}</td>
            </tr>
            <tr>
                <th>رقم الإصدار</th>
                <td>{{ $data['version'] ?? '1.0' }}</td>
                <th>حالة الوثيقة</th>
                <td>{{ $data['status'] ?? 'مفعلة' }}</td>
            </tr>
        </table>
        
        <div class="section">
            <h3 class="section-title">1. الغرض</h3>
            <div>{!! isset($data['purpose']) ? (is_string($data['purpose']) ? $data['purpose'] : json_encode($data['purpose'], JSON_UNESCAPED_UNICODE)) : 'لم يتم تحديد الغرض' !!}</div>
        </div>
        
        <div class="section">
            <h3 class="section-title">2. المجال</h3>
            <div>{!! isset($data['scope']) ? (is_string($data['scope']) ? $data['scope'] : json_encode($data['scope'], JSON_UNESCAPED_UNICODE)) : 'لم يتم تحديد المجال' !!}</div>
        </div>
        
        <div class="section">
            <h3 class="section-title">3. المسؤوليات</h3>
            <div>{!! isset($data['responsibilities']) ? (is_string($data['responsibilities']) ? $data['responsibilities'] : json_encode($data['responsibilities'], JSON_UNESCAPED_UNICODE)) : 'لم يتم تحديد المسؤوليات' !!}</div>
        </div>
        
        <div class="section">
            <h3 class="section-title">4. التعريفات</h3>
            <div>{!! isset($data['definitions']) ? (is_string($data['definitions']) ? $data['definitions'] : json_encode($data['definitions'], JSON_UNESCAPED_UNICODE)) : 'لم يتم تحديد التعريفات' !!}</div>
        </div>
        
        <div class="page-break"></div>
        
        <div class="section">
            <h3 class="section-title">5. الإجراءات</h3>
            <div>{!! isset($data['procedures']) ? (is_string($data['procedures']) ? $data['procedures'] : json_encode($data['procedures'], JSON_UNESCAPED_UNICODE)) : 'لم يتم تحديد الإجراءات' !!}</div>
        </div>
        
        <div class="section">
            <h3 class="section-title">6. المراجع</h3>
            <div>{!! isset($data['references']) ? (is_string($data['references']) ? $data['references'] : json_encode($data['references'], JSON_UNESCAPED_UNICODE)) : 'لم يتم تحديد المراجع' !!}</div>
        </div>
        
        <div class="section">
            <h3 class="section-title">7. المرفقات</h3>
            <div>{!! isset($data['attachments']) ? (is_string($data['attachments']) ? $data['attachments'] : json_encode($data['attachments'], JSON_UNESCAPED_UNICODE)) : 'لم يتم تحديد المرفقات' !!}</div>
        </div>
        
        <div class="section">
            <h3 class="section-title">اعتماد الوثيقة</h3>
            <div class="row">
                <div class="col-6">
                    <p><strong>الإعداد:</strong></p>
                    <p>الاسم: {{ $data['prepared_by'] ?? '_____________' }}</p>
                    <p>المنصب: {{ $data['preparer_position'] ?? '_____________' }}</p>
                    <div class="signature-box"></div>
                </div>
                <div class="col-6">
                    <p><strong>المراجعة:</strong></p>
                    <p>الاسم: {{ $data['reviewed_by'] ?? '_____________' }}</p>
                    <p>المنصب: {{ $data['reviewer_position'] ?? '_____________' }}</p>
                    <div class="signature-box"></div>
                </div>
            </div>
            <div class="row" style="margin-top: 20px;">
                <div class="col-6">
                    <p><strong>الاعتماد:</strong></p>
                    <p>الاسم: {{ $data['approved_by'] ?? '_____________' }}</p>
                    <p>المنصب: {{ $data['approver_position'] ?? '_____________' }}</p>
                    <div class="signature-box"></div>
                </div>
            </div>
        </div>
        
        <div class="footer">
            {{ $document_number }} - {{ date('Y-m-d') }} - الصفحة {PAGENO} من {nbpg}
        </div>
    </div>
</body>
</html>
