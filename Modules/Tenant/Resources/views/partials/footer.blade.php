@php
    // $DefaultCustomPage = DefaultCustomPage();
    $admin_logo = getSettingsValByName('company_logo');
    $lightLogo = getSettingsValByName('light_logo');
    $currentISO = getSettingsValByName('current_iso_system');
    $ISOSystem = \Modules\Document\Entities\ISOSystem::get();
@endphp
<footer class="pc-footer">
    <div class="footer-wrapper container-fluid">
        <div class="row">
            <div class="col-sm-6 my-1">
                <p class="m-0">
                    {{ __('Copyright') }} {{ date('Y') }} {{ env('APP_NAME') }} {{ __('All rights reserved') }}.
                </p>
            </div>
            {{-- <div class="col-sm-6 ms-auto my-1">
                <ul class="list-inline footer-link mb-0 justify-content-sm-end d-flex">
                    @foreach ($DefaultCustomPage as $item)
                        <li class="list-inline-item"><a href="{{ route('page', $item->slug) }}"
                                target="_blank">{{ $item->title }}</a></li>
                    @endforeach
                </ul>
            </div> --}}
        </div>
    </div>
</footer>
<script src="{{ asset('js/jquery.js') }}"></script>
<!-- Required Js -->
<script src="{{ asset('assets/js/plugins/popper.min.js') }}"></script>
<script src="{{ asset('assets/js/plugins/simplebar.min.js') }}"></script>
<script src="{{ asset('assets/js/plugins/bootstrap.min.js') }}"></script>
<script src="{{ asset('assets/js/fonts/custom-font.js') }}"></script>
<script>
    var lightLogo = "{{ asset(Storage::url('upload/logo')) . '/' . $lightLogo }}";
    var logo = "{{ asset(Storage::url('upload/logo')) . '/' . $admin_logo }}";
</script>
<script src="{{ asset('assets/js/pcoded.js') }}"></script>
<script src="{{ asset('assets/js/plugins/feather.min.js') }}"></script>
<!-- Laravel Reverb للإشعارات في الوقت الحقيقي -->
{{-- <script src="https://cdn.jsdelivr.net/npm/laravel-echo/dist/echo.iife.js"></script> --}}
<script src="https://cdnjs.cloudflare.com/ajax/libs/laravel-echo/1.19.0/echo.iife.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pusher/8.4.0/pusher.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // تأكد من تحميل الصفحة بالكامل قبل إعداد Echo
        window.Echo = new Echo({
            broadcaster: 'reverb',
            key: '{{ config("broadcasting.connections.reverb.key") }}',
            wsHost: window.location.hostname,
            wsPort: {{ config("broadcasting.connections.reverb.port") }},
            forceTLS: false,
            disableStats: true,
            enabledTransports: ['ws', 'wss'],
            cluster: 'eu',
            authEndpoint: '/broadcasting/auth',
            auth: {
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            }
        });

        const userId = {{ auth()->id() }};
        
        // بدء معالجة الإشعارات
        if (typeof initNotifications === 'function') {
            initNotifications();
        }

        // الاستماع إلى قناة الإشعارات الخاصة
        try {
            console.log('محاولة الاشتراك في قناة الإشعارات:', `notifications.${userId}`);
            window.Echo.private(`notifications.${userId}`)
                .listen('.document.notification', (notification) => {
                    console.log('تم استلام إشعار جديد من Reverb:', notification);
                    if (typeof playNotificationSound === 'function') {
                        playNotificationSound();
                    }
                    if (typeof createNotificationItem === 'function') {
                        $('.notifications-list').prepend(createNotificationItem(notification.notification));
                        $('.notification-empty').addClass('d-none');
                    }

                    const count = parseInt($('.notifications-count').text() || '0') + 1;
                    $('.notifications-count').text(count).show();

                    if (typeof toastr !== 'undefined') {
                        const data = notification.notification.data;
                        toastr.info(data.message, data.title || 'تنبيه');
                    }
                });
                
            console.log('تم الاشتراك في قناة الإشعارات:', `notifications.${userId}`);
        } catch (error) {
            console.error('خطأ في الاشتراك بقناة الإشعارات:', error);
        }
    });
</script>
<script src="{{ asset('modules/document/js/notifications.js') }}"></script>

<!-- datatable Js -->
<script src="{{ asset('assets/js/plugins/dataTables.min.js') }}"></script>
<script src="{{ asset('assets/js/plugins/dataTables.bootstrap5.min.js') }}"></script>
{{-- <script src="{{ asset('assets/js/plugins/buttons.colVis.min.js') }}"></script> --}}
{{-- <script src="{{ asset('assets/js/plugins/buttons.print.min.js') }}"></script> --}}
<script src="{{ asset('assets/js/plugins/pdfmake.min.js') }}"></script>
<script src="{{ asset('assets/js/plugins/jszip.min.js') }}"></script>
<script src="{{ asset('assets/js/plugins/dataTables.buttons.min.js') }}"></script>
<script src="{{ asset('assets/js/plugins/vfs_fonts.js') }}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.30.1/moment.min.js"></script>

{{-- <script src="{{ asset('assets/js/plugins/buttons.html5.min.js') }}"></script> --}}
{{-- <script src="{{ asset('assets/js/plugins/buttons.bootstrap5.min.js') }}"></script> --}}
<script>
    font_change("{{ $settings['layout_font'] }}");
</script>

<script>
    change_box_container("{{ $settings['layout_width'] }}");
</script>

<!-- [Page Specific JS] start -->
<!-- bootstrap-datepicker -->
<script src="{{ asset('assets/js/plugins/datepicker-full.min.js') }}"></script>
<script src="{{ asset('assets/js/plugins/apexcharts.min.js') }}"></script>
<script src="{{ asset('assets/js/plugins/peity-vanilla.min.js') }}"></script>
<script src="{{ asset('assets/js/plugins/sweetalert2.all.min.js') }}"></script>
<script src="{{ asset('assets/js/plugins/notifier.js') }}"></script>
<script src="{{ asset('assets/js/plugins/choices.min.js') }}"></script>
<script src="{{ asset('assets/js/plugins/ckeditor/classic/ckeditor.js') }}"></script>
<script src="{{ asset('assets/js/jstree.min.js') }}"></script>
@push('css-page')
<style>
    .iso-item {
        transition: border-color 0.2s, box-shadow 0.2s;
    }

    .iso-item:hover {
        border-color: #0d6efd;
        box-shadow: 0 0 5px rgba(13, 110, 253, 0.3);
    }

    .cursor-pointer {
        cursor: pointer;
    }

    .btn-check:checked + .check-label .checkmark {
        content: "✔️";
        font-size: 1.2em;
    }
    
    /* Notification styles */
    .notification-bell {
        position: relative;
    }
    
    .pulse-badge {
        animation: pulse 1.5s infinite;
    }
    
    @keyframes pulse {
        0% {
            transform: scale(0.95);
            box-shadow: 0 0 0 0 rgba(220, 53, 69, 0.7);
        }
        
        70% {
            transform: scale(1);
            box-shadow: 0 0 0 8px rgba(220, 53, 69, 0);
        }
        
        100% {
            transform: scale(0.95);
            box-shadow: 0 0 0 0 rgba(220, 53, 69, 0);
        }
    }
    
    .notification-dropdown {
        width: 320px;
        padding: 0;
    }
    
    .notification-divider {
        height: 1px;
        margin: 0;
        background-color: rgba(0,0,0,0.1);
    }
    
    .dropdown-item.notification-item {
        padding: 10px 15px;
        border-left: 3px solid transparent;
        transition: all 0.2s;
    }
    
    .dropdown-item.notification-item:hover {
        border-left: 3px solid var(--bs-primary);
        background-color: rgba(var(--bs-primary-rgb), 0.1);
    }
    
    .dropdown-item.notification-item.unread {
        background-color: rgba(var(--bs-primary-rgb), 0.08);
    }
    
    .notification-icon {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 40px;
        height: 40px;
        border-radius: 50%;
    }
    
    .notification-time {
        font-size: 11px;
        color: rgba(0,0,0,0.5);
    }
    
    .mark-all-read {
        padding: 2px 8px;
        font-size: 12px;
    }
</style>
@endpush

<!-- [Page Specific JS] end -->
<form method="post" action="{{ route('tenant.setting.updateSetting')}}">
    {{ csrf_field() }}
    <input type="hidden" name="theme_mode" id="theme_mode" value="{{ $settings['theme_mode'] }}">
    <input type="hidden" name="layout_font" id="layout_font" value="{{ $settings['layout_font'] }}">
    <input type="hidden" name="accent_color" id="accent_color" value="{{ $settings['accent_color'] }}">
    <input type="hidden" name="sidebar_caption" id="sidebar_caption" value="{{ $settings['sidebar_caption'] }}">
    <input type="hidden" name="theme_layout" id="theme_layout" value="{{ $settings['theme_layout'] }}">
    <input type="hidden" name="layout_width" id="layout_width" value="{{ $settings['layout_width'] }}">
    <div class="offcanvas border-0 pct-offcanvas offcanvas-end" tabindex="-1" id="offcanvas_pc_layout">
        <div class="offcanvas-header justify-content-between">
            <h5 class="offcanvas-title">{{ __('Theme Settings') }}</h5>
            <div class="d-inline-flex align-items-center gap-2">

                <a type="button" class="avtar avtar-s btn-link-danger btn-pc-default" data-bs-dismiss="offcanvas"
                    aria-label="Close">
                    <i class="ti ti-x f-20"></i>
                </a>
            </div>
        </div>
        <ul class="nav nav-tabs nav-fill pct-tabs" id="myTab" role="tablist">

            <li class="nav-item" role="presentation" data-bs-toggle="tooltip" title="{{ __('ISO Systems') }}">
                <button class="nav-link active" id="pct-2-tab" data-bs-toggle="tab" data-bs-target="#pct-2-tab-pane"
                    type="button" role="tab" aria-controls="pct-2-tab-pane" aria-selected="true">
                    <span style="font-size: small">{{ __('ISO Systems') }}</span>
                </button>
            </li>

            <li class="nav-item" role="presentation" data-bs-toggle="tooltip" title="{{ __('Layout Settings') }}">
                <button class="nav-link" id="pct-1-tab" data-bs-toggle="tab" data-bs-target="#pct-1-tab-pane"
                    type="button" role="tab" aria-controls="pct-1-tab-pane" aria-selected="false">
                    <span style="font-size: small">{{ __('Layout Settings') }}</span>

                </button>
            </li>
           
        </ul>
        <div class="pct-body customizer-body">
            <div class="offcanvas-body p-0">
                <div class="tab-content" id="myTabContent">
                    <div class="tab-pane fade " id="pct-1-tab-pane" role="tabpanel"
                        aria-labelledby="pct-1-tab" tabindex="0">
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item">
                                <div class="pc-dark">
                                    <div class="d-flex align-items-center">
                                        <div class="flex-grow-1 me-3">
                                            <h5 class="mb-1">{{ __('Theme Mode') }}</h5>
                                            <p class="text-muted text-sm mb-0">{{ __('Light / Dark / System') }}</p>
                                        </div>
                                        <div class="flex-shrink-0">
                                            <div class="row g-2 theme-color theme-layout">
                                                <div class="col-4">
                                                    <div class="d-grid">
                                                        <button type="button"
                                                            class="preset-btn btn {{ $settings['theme_mode'] == 'light' ? 'active' : '' }}"
                                                            data-value="true" onclick="layout_change('light');"
                                                            data-bs-toggle="tooltip" title="Light">
                                                            <span class="pc-lay-icon">
                                                                <span></span>
                                                                <span></span>
                                                                <span></span>
                                                                <span></span>
                                                            </span>
                                                        </button>
                                                    </div>
                                                </div>
                                                <div class="col-4">
                                                    <div class="d-grid">
                                                        <button type="button"
                                                            class="preset-btn btn {{ $settings['theme_mode'] == 'dark' ? 'active' : '' }}"
                                                            data-value="false" onclick="layout_change('dark');"
                                                            data-bs-toggle="tooltip" title="Dark">
                                                            <span class="pc-lay-icon">
                                                                <span></span>
                                                                <span></span>
                                                                <span></span>
                                                                <span></span>
                                                            </span>
                                                        </button>
                                                    </div>
                                                </div>
                                                <div class="col-4">
                                                    <div class="d-grid">
                                                        <button type="button" class="preset-btn btn "
                                                            data-value="default" onclick="layout_change_default();"
                                                            data-bs-toggle="tooltip"
                                                            title="{{ __("Automatically sets the theme based on user's operating system's color scheme.") }}">
                                                            <span
                                                                class="pc-lay-icon d-flex align-items-center justify-content-center">
                                                                <i class="ph-duotone ph-cpu"></i>
                                                            </span>
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </li>
                            <li class="list-group-item">
                                <h5 class="mb-1">{{ __('Accent color') }}</h5>
                                <p class="text-muted text-sm mb-2">{{ __('Choose your primary theme color') }}</p>
                                <div class="theme-color preset-color">
                                    <a href="#!" data-value="preset-1"
                                        class="{{ $settings['accent_color'] == 'preset-1' ? 'active' : '' }}"><i
                                            class="ti ti-check"></i></a>
                                    <a href="#!" data-value="preset-2"
                                        class="{{ $settings['accent_color'] == 'preset-2' ? 'active' : '' }}"><i
                                            class="ti ti-check"></i></a>
                                    <a href="#!" data-value="preset-3"
                                        class="{{ $settings['accent_color'] == 'preset-3' ? 'active' : '' }}"><i
                                            class="ti ti-check"></i></a>
                                    <a href="#!" data-value="preset-4"
                                        class="{{ $settings['accent_color'] == 'preset-4' ? 'active' : '' }}"><i
                                            class="ti ti-check"></i></a>
                                    <a href="#!" data-value="preset-5"
                                        class="{{ $settings['accent_color'] == 'preset-5' ? 'active' : '' }}"><i
                                            class="ti ti-check"></i></a>
                                    <a href="#!" data-value="preset-6"
                                        class="{{ $settings['accent_color'] == 'preset-6' ? 'active' : '' }}"><i
                                            class="ti ti-check"></i></a>
                                    <a href="#!" data-value="preset-7"
                                        class="{{ $settings['accent_color'] == 'preset-7' ? 'active' : '' }}"><i
                                            class="ti ti-check"></i></a>
                                </div>
                            </li>
                            <li class="list-group-item">
                                <div class="d-flex align-items-center">
                                    <div class="flex-grow-1 me-3">
                                        <h5 class="mb-1">{{ __('Sidebar Caption') }}</h5>
                                        <p class="text-muted text-sm mb-0">{{ __('Caption Hide / Show') }}</p>
                                    </div>
                                    <div class="flex-shrink-0">
                                        <div class="row g-2 theme-color theme-nav-caption">
                                            <div class="col-6">
                                                <div class="d-grid">
                                                    <button type="button"
                                                        class="preset-btn btn {{ $settings['sidebar_caption'] == 'true' ? 'active' : '' }}"
                                                        data-value="true" onclick="layout_caption_change('true');"
                                                        data-bs-toggle="tooltip" title="Caption Show">
                                                        <span class="pc-lay-icon">
                                                            <span></span>
                                                            <span></span>
                                                            <span>
                                                                <span></span>
                                                                <span></span>
                                                            </span>
                                                            <span></span>
                                                        </span>
                                                    </button>
                                                </div>
                                            </div>
                                            <div class="col-6">
                                                <div class="d-grid">
                                                    <button type="button"
                                                        class="preset-btn btn {{ $settings['sidebar_caption'] == 'false' ? 'active' : '' }}"
                                                        data-value="false" onclick="layout_caption_change('false');"
                                                        data-bs-toggle="tooltip" title="Caption Hide">
                                                        <span class="pc-lay-icon">
                                                            <span></span>
                                                            <span></span>
                                                            <span>
                                                                <span></span>
                                                                <span></span>
                                                            </span>
                                                            <span></span>
                                                        </span>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </li>
                            <li class="list-group-item">
                                <div class="pc-rtl">
                                    <div class="d-flex align-items-center">
                                        <div class="flex-grow-1 me-3">
                                            <h5 class="mb-1">{{ __('Theme Layout') }}</h5>
                                            <p class="text-muted text-sm">{{ __('LTR/RTL') }}</p>
                                        </div>
                                        <div class="flex-shrink-0">
                                            <div class="row g-2 theme-color theme-direction">
                                                <div class="col-6">
                                                    <div class="d-grid">
                                                        <button type="button"
                                                            class="preset-btn btn {{ $settings['theme_layout'] == 'ltr' ? 'active' : '' }}"
                                                            data-value="false" onclick="layout_rtl_change('false');"
                                                            data-bs-toggle="tooltip" title="LTR">
                                                            <span class="pc-lay-icon">
                                                                <span></span>
                                                                <span></span>
                                                                <span></span>
                                                                <span></span>
                                                            </span>
                                                        </button>
                                                    </div>
                                                </div>
                                                <div class="col-6">
                                                    <div class="d-grid">
                                                        <button type="button"
                                                            class="preset-btn btn {{ $settings['theme_layout'] == 'rtl' ? 'active' : '' }}"
                                                            data-value="true" onclick="layout_rtl_change('true');"
                                                            data-bs-toggle="tooltip" title="RTL">
                                                            <span class="pc-lay-icon">
                                                                <span></span>
                                                                <span></span>
                                                                <span></span>
                                                                <span></span>
                                                            </span>
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </li>
                            <li class="list-group-item">
                                <div class="pc-container-width">
                                    <div class="d-flex align-items-center">
                                        <div class="flex-grow-1 me-3">
                                            <h5 class="mb-1">{{ __('Layout Width') }}</h5>
                                            <p class="text-muted text-sm">{{ __('Full / Fixed width') }}</p>
                                        </div>
                                        <div class="flex-shrink-0">
                                            <div class="row g-2 theme-color theme-container">
                                                <div class="col-6">
                                                    <div class="d-grid">
                                                        <button type="button"
                                                            class="preset-btn btn {{ $settings['layout_width'] == 'false' ? 'active' : '' }}"
                                                            data-value="false" onclick="change_box_container('false')"
                                                            data-bs-toggle="tooltip" title="Full Width">
                                                            <span class="pc-lay-icon">
                                                                <span></span>
                                                                <span></span>
                                                                <span></span>
                                                                <span><span></span></span>
                                                            </span>
                                                        </button>
                                                    </div>
                                                </div>
                                                <div class="col-6">
                                                    <div class="d-grid">
                                                        <button type="button"
                                                            class="preset-btn btn {{ $settings['layout_width'] == 'true' ? 'active' : '' }}"
                                                            data-value="true" onclick="change_box_container('true')"
                                                            data-bs-toggle="tooltip" title="Fixed Width">
                                                            <span class="pc-lay-icon">
                                                                <span></span>
                                                                <span></span>
                                                                <span></span>
                                                                <span>
                                                                    <span></span>
                                                                </span>
                                                            </span>
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </li>
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item">
                                    <h5 class="mb-1">{{ __('Font Style') }}</h5>
                                    <p class="text-muted text-sm">{{ __('Choose theme font') }}</p>
                                    <div class="theme-color theme-font-style">
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="layout_font"
                                                id="layoutfontRoboto"
                                                {{ $settings['layout_font'] == 'Roboto' ? 'checked' : '' }}
                                                value="Roboto" onclick="font_change('Roboto')" />
                                            <label class="form-check-label"
                                                for="layoutfontRoboto">{{ __('Roboto') }}</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="layout_font"
                                                id="layoutfontPoppins"
                                                {{ $settings['layout_font'] == 'Poppins' ? 'checked' : '' }}
                                                value="Poppins" onclick="font_change('Poppins')" />
                                            <label class="form-check-label"
                                                for="layoutfontPoppins">{{ __('Poppins') }}</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="layout_font"
                                                id="layoutfontCairo"
                                                {{ $settings['layout_font'] == 'Cairo' ? 'checked' : '' }}
                                                value="Cairo" onclick="font_change('Cairo')" />
                                            <label class="form-check-label"
                                                for="layoutfontCairo">{{ __('Cairo') }}</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="layout_font"
                                                id="layoutfontArial"
                                                {{ $settings['layout_font'] == 'Arial' ? 'checked' : '' }}
                                                value="Arial" onclick="font_change('Arial')" />
                                            <label class="form-check-label"
                                                for="layoutfontArial">{{ __('Arial') }}</label>
                                        </div>

                                    </div>
                                </li>
                            </ul>
                        </ul>
                    </div>
                    <div class="tab-pane fade show active" id="pct-2-tab-pane" role="tabpanel" aria-labelledby="pct-2-tab"
                        tabindex="0">

                        @foreach ($ISOSystem as $system)
                        <label for="iso_{{ $system->id }}"
                               class="iso-item d-flex align-items-center my-2 p-3 mx-2 border rounded cursor-pointer w-100">
                            <div class="flex-grow-1 me-3 d-flex align-items-center">
                                <img class="img-radius img-fluid wid-40 me-3"
                                     src="{{ getISOImage(getFilePath('isoIcon') . '/' . $system->image) }}"
                                     alt="@lang('Image')">
                                <div>
                                    <h5 class="mb-1">{{ $system->name }}</h5>
                                    <p class="text-muted text-sm mb-0">{{ $system->description }}</p>
                                </div>
                            </div>
                        
                            <div class="flex-shrink-0">
                                <input type="radio" class="btn-check iso-radio" name="current_iso_system"
                                       id="iso_{{ $system->id }}" value="{{ $system->id }}"
                                       autocomplete="off"
                                       {{ $settings['current_iso_system'] == $system->id ? 'checked' : '' }}>
                                <span class="btn btn-outline-light d-flex align-items-center justify-content-center check-label">
                                    <span class="checkmark">{{ $settings['current_iso_system'] == $system->id ? '✔️' : '' }}</span>
                                </span>
                            </div>
                        </label>
                        @endforeach
                        

                    </div>
                </div>

            </div>
            <div class="d-grid">
                <button class="btn btn-secondary">{{ __('Save Settings') }}</button>
            </div>
        </div>
    </div>
</form>

@stack('script-page')
<script>
    var successImg = '{{ asset('assets/images/notification/ok-48.png') }}';
    var errorImg = '{{ asset('assets/images/notification/high_priority-48.png') }}';
</script>
<script>
    document.querySelectorAll('.iso-radio').forEach(radio => {
        radio.addEventListener('change', function () {
            document.querySelectorAll('.checkmark').forEach(span => span.textContent = '');
            const label = this.nextElementSibling.querySelector('.checkmark');
            label.textContent = '✔️';

        });
    });
</script>
<script src="{{ asset('js/custom.js') }}"></script>
@if ($statusMessage = Session::get('success'))
    <script>
        notifier.show('Success!', '{!! $statusMessage !!}', 'success',
            successImg, 4000);
    </script>
@endif
@if ($statusMessage = Session::get('error'))
    <script>
        notifier.show('Error!', '{!! $statusMessage !!}', 'error',
            errorImg, 4000);
    </script>
@endif
