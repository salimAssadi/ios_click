<!DOCTYPE html>
@php
    use App\Models\AuthPage;

    $settings = settings();
    $titles =  [];
    $descriptions =  [];
@endphp
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">

<head>
    <!-- Required meta tags-->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ env('APP_NAME') }} - @yield('tab-title')</title>
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />

    <meta name="author" content="{{ !empty($settings['app_name']) ? $settings['app_name'] : env('APP_NAME') }}">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ !empty($settings['app_name']) ? $settings['app_name'] : env('APP_NAME') }} - @yield('page-title') </title>

    <meta name="title" content="{{ $settings['meta_seo_title'] }}">
    <meta name="keywords" content="{{ $settings['meta_seo_keyword'] }}">
    <meta name="description" content="{{ $settings['meta_seo_description'] }}">


    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ env('APP_URL') }}">
    <meta property="og:title" content="{{ $settings['meta_seo_title'] }}">
    <meta property="og:description" content="{{ $settings['meta_seo_description'] }}">
    <meta property="og:image" content="{{ asset(Storage::url('upload/seo')) . '/' . $settings['meta_seo_image'] }}">

    <meta property="twitter:card" content="summary_large_image">
    <meta property="twitter:url" content="{{ env('APP_URL') }}">
    <meta property="twitter:title" content="{{ $settings['meta_seo_title'] }}">
    <meta property="twitter:description" content="{{ $settings['meta_seo_description'] }}">
    <meta property="twitter:image"
        content="{{ asset(Storage::url('upload/seo')) . '/' . $settings['meta_seo_image'] }}">

    <!-- shortcut icon-->
    <link rel="icon" href="{{ asset('assets/images/favicon.png') }}"
        type="image/x-icon">
    <link rel="shortcut icon" href="{{ asset('assets/images/favicon.png') }}"
        type="image/x-icon">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap"
        id="main-font-link" />
    <link rel="stylesheet" href="{{ asset('assets/fonts/phosphor/duotone/style.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/fonts/tabler-icons.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/fonts/feather.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/fonts/fontawesome.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/fonts/material.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}" id="main-style-link" />
    <link rel="stylesheet" href="{{ asset('assets/css/style-preset.css') }}" />
    <link href="{{ asset('css/custom.css') }} " rel="stylesheet">
    <style>
    /* خلفية ديناميكية عصرية مع صورة وتدرجات */
    body, .auth-main {
      min-height: 100vh;
      background:
        linear-gradient(120deg, rgba(80,106,178,0.12) 0%, rgba(212,83,83,0.10) 100%),
        url('https://images.unsplash.com/photo-1506744038136-46273834b3fb?auto=format&fit=crop&w=1500&q=80') center center / cover no-repeat fixed;
      position: relative;
    }
    /* أشكال زخرفية عصرية */
    .decor-shape {
      position: absolute;
      z-index: 1;
      pointer-events: none;
      transition: opacity 0.5s;
    }
    .decor-shape.blue {
      width: 360px; height: 360px;
      top: -100px; left: -120px;
      background: radial-gradient(circle, #506ab2 60%, rgba(80,106,178,0.1) 100%);
      opacity: 0.45;
      filter: blur(5px);
      border-radius: 50%;
    }
    .decor-shape.red {
      width: 220px; height: 220px;
      bottom: -60px; right: -100px;
      background: radial-gradient(circle, #d45353 50%, rgba(212,83,83,0.13) 100%);
      opacity: 0.28;
      filter: blur(4px);
      border-radius: 50%;
    }
    .decor-shape.white {
      width: 140px; height: 140px;
      top: 40%; left: 60%;
      background: radial-gradient(circle, #fff 80%, rgba(255,255,255,0.1) 100%);
      opacity: 0.18;
      filter: blur(2px);
      border-radius: 50%;
    }
    /* تأثير زجاجي عصري للنموذج */
    .auth-form {
      position: relative;
      z-index: 10;
      background: rgba(255,255,255,0.85);
      border-radius: 1.5rem;
      box-shadow: 0 8px 32px rgba(80,106,178,0.09);
      backdrop-filter: blur(10px) saturate(140%);
      padding: 2.5rem 2rem;
      margin: 2rem 0;
      border: 1px solid rgba(80,106,178,0.08);
    }
    /* خطوط وأزرار متناسقة مع الهوية */
    .auth-form label, .auth-form .form-check-label {
      color: #506ab2;
      font-weight: 500;
    }
    .auth-form .btn-primary, .auth-form .btn-light.text-primary {
      background: #506ab2;
      color: #fff;
      border: none;
      border-radius: 0.75rem;
      font-weight: bold;
      transition: background 0.2s, color 0.2s;
    }
    .auth-form .btn-primary:hover, .auth-form .btn-light.text-primary:hover {
      background: #d45353;
      color: #fff;
    }
    .auth-form .form-control {
      border-radius: 0.75rem;
      min-height: 48px;
      border: 1px solid #e3eafc;
      font-size: 1.05rem;
    }
    .auth-form .form-control:focus {
      border-color: #506ab2;
      box-shadow: 0 0 0 0.1rem rgba(80,106,178,0.12);
    }
    /* دعم RTL */
    [dir="rtl"] .auth-form {
      text-align: right;
    }
    [dir="rtl"] .decor-shape.blue {
      left: auto; right: -120px;
    }
    [dir="rtl"] .decor-shape.red {
      right: auto; left: -100px;
    }
    [dir="rtl"] .decor-shape.white {
      left: 10%; right: auto;
    }
    @media (max-width: 991.98px) {
      .decor-shape.blue, .decor-shape.red, .decor-shape.white {
        opacity: 0.15;
        width: 120px; height: 120px;
      }
    }
     

    #radius-shape-2 {
      border-radius: 38% 62% 63% 37% / 70% 33% 67% 30%;
      bottom: -60px;
      right: -110px;
      width: 300px;
      height: 300px;
      background: radial-gradient(circle at 60% 40%, rgba(212,83,83,0.18) 50%, rgba(80,106,178,0.18) 100%);
      box-shadow: 0 8px 32px rgba(212, 83, 83, 0.08);
      overflow: hidden;
      opacity: 0.8;
    }

    .bg-glass {
      background-color: hsla(0, 0%, 100%, 0.92) !important;
      backdrop-filter: saturate(200%) blur(25px);
    }
</style>
</head>

<body data-pc-preset="{{ $settings['accent_color'] }}" data-pc-sidebar-theme="light"
    data-pc-sidebar-caption="{{ $settings['sidebar_caption'] }}" data-pc-direction="{{ $settings['theme_layout'] }}"
    data-pc-theme="{{ $settings['theme_mode'] }}">
    <div class="loader-bg">
        <div class="loader-track">
            <div class="loader-fill"></div>
        </div>
    </div>
    <div class="auth-main login-bg background-radial-gradient">
        <div class="auth-wrapper v2">
            <div class="auth-form">
                @yield('content')
            </div>
        </div>
    </div>
    <script src="{{ asset('js/jquery.js') }}"></script>
    <script src="{{ asset('assets/js/plugins/popper.min.js') }}"></script>
    <script src="{{ asset('assets/js/plugins/simplebar.min.js') }}"></script>
    <script src="{{ asset('assets/js/plugins/bootstrap.min.js') }}"></script>
    <script src="{{ asset('assets/js/fonts/custom-font.js') }}"></script>
    <script src="{{ asset('assets/js/pcoded.js') }}"></script>
    <script src="{{ asset('assets/js/plugins/feather.min.js') }}"></script>

    @stack('script-page')
    <script>
        font_change('Cairo');
    </script>
</body>

</html>
