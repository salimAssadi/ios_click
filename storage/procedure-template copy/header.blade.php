@php
$imagePath = 'storage/upload/logo/logo.png'; // Replace with your image path
$type = pathinfo($imagePath, PATHINFO_EXTENSION);
// $base64 = 'data:image/' . $type . ';base64,' . base64_encode($imagePath);
$base64 = "data:@image/png;base64,".base64_encode(file_get_contents($imagePath));

// dd($base64);
@endphp

<header>
    <table class="table">
        <tbody>
        <tr>
            <td width="30%">
                <img class="top--company-logo" src="http://127.0.0.1:8000/storage/upload/logo//logo.png"  alt="company logo">
            </td>
            <td  width="40%" rowspan="2"> 
                <p>إصدار / مراجعة: 1/0</p>
                <p>تاريـخ الإصـدار: 01/02/2025</p>
                <p>تاريـخ المراجعة:</p>
                <p><span class="page-number"></span> <span>صفحـــة رقـــم:</span> </p>
            </td>
            <td width="30%" class="text-center">
                <p class="top-info">إجراء</p>
                <p class="top-info"> سياق شركة والأطراف المهتمة</p>
                <p class="top-info"> ASD-P-QMS-01</p>
            </td>
        </tr>
        <tr>
            
            <td width="30%" class="text-center">
                ISO     9001: 2015
            </td>
            <td width="30%" class="text-center">
                <p class="top-info">الادارة العليا</p>
                <p class="top-info">Higher Management</p>          
            </td>
        </tr>
        </tbody>
    </table>
</header>