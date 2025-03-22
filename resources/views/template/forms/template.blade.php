<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <title>{{$data['pageTitle']}}</title>
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
    @include('template.partials.form.header')
    <main>
        {!! $data['sample']->content !!}
    </main>
    @include('template.partials.form.footer')
   

</body>

</html>
