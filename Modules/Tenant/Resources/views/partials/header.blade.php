@php
    $users = auth('tenant')->user();
    $languages = \App\Models\Custom::languages();
    $userLang = auth('tenant')->user()->lang;

    $profile = asset('assets/images/avatar.png');
@endphp

<header class="pc-header">
    <div class="header-wrapper"><!-- [Mobile Media Block] start -->
        <div class="me-auto pc-mob-drp">
            <ul class="list-unstyled">
                <li class="pc-h-item header-mobile-collapse">
                    <a href="#" class="pc-head-link head-link-secondary ms-0" id="sidebar-hide">
                        <i class="ti ti-menu-2"></i>
                    </a>
                </li>
                <li class="pc-h-item pc-sidebar-popup">
                    <a href="#" class="pc-head-link head-link-secondary ms-0" id="mobile-collapse">
                        <i class="ti ti-menu-2"></i>
                    </a>
                </li>

            </ul>
        </div>
        <!-- [Mobile Media Block end] -->
        <div class="ms-auto">
            <ul class="list-unstyled">

                <li class="dropdown pc-h-item" data-bs-toggle="tooltip" data-bs-original-title="{{__('Language')}}" data-bs-placement="bottom">
                    <a class="pc-head-link head-link-primary dropdown-toggle arrow-none me-0" data-bs-toggle="dropdown"
                        href="#" role="button" aria-haspopup="false" aria-expanded="false" >
                        <i class="ti ti-language"></i>
                    </a>
                    <div class="dropdown-menu dropdown-menu-end pc-h-dropdown">
                        @foreach($languages as $language)
                            @if($language!='en')
                                <a href="{{route('tenant.setting.language.change',$language)}}" class="dropdown-item {{ $userLang==$language?'active':'' }}">
                                    <span class="align-middle">{{ucfirst( $language)}}</span>
                                </a>
                            @endif
                        @endforeach


                    </div>
                </li>
                <div class="pc-head-right">
                    <!-- Notifications dropdown -->
                    <div class="dropdown pc-h-item">
                        <a class="pc-head-link dropdown-toggle arrow-none me-0 notification-bell" data-bs-toggle="dropdown" href="#" role="button" aria-haspopup="false" aria-expanded="false">
                            <i class="ti ti-bell"></i>
                            <span class="badge bg-danger pc-h-badge notifications-count pulse-badge" style="display: none;"></span>
                        </a>
                        <div class="dropdown-menu dropdown-menu-end pc-h-dropdown notification-dropdown">
                            <div class="dropdown-header d-flex align-items-center justify-content-between">
                                <h5 class="text-overflow m-0"><span>{{ __('Notifications') }}</span></h5>
                                <button class="btn btn-sm btn-light-secondary mark-all-read">
                                    <i class="ti ti-check me-1"></i>{{ __('Mark all read') }}
                                </button>
                            </div>
                            <div class="notification-divider"></div>
                            <div class="notifications-list" style="max-height: 300px; overflow-y: auto;">
                                <!-- Notifications will be loaded here -->
                                <div class="notification-empty text-center p-3 d-none">
                                    <div class="empty-icon mb-2">
                                        <i class="ti ti-bell-off fs-1 text-muted"></i>
                                    </div>
                                    <p class="text-muted">{{ __('No new notifications') }}</p>
                                </div>
                            </div>
                            <div class="notification-divider"></div>
                            <a href="{{ route('tenant.notifications.index') }}" class="dropdown-item text-center fw-medium">
                                <i class="ti ti-list me-1"></i>{{ __('View all') }}
                            </a>
                        </div>
                    </div>
                    <!-- User dropdown -->
                </div>
                @if (auth('tenant')->user()->type == 'super admin' || auth('tenant')->user()->type == 'owner')
                    <li class="dropdown pc-h-item pc-mega-menu" data-bs-toggle="tooltip" data-bs-original-title="{{__('Theme Settings')}}" data-bs-placement="bottom">
                        <a href="#" class="pc-head-link head-link-secondary dropdown-toggle arrow-none me-0"
                            data-bs-toggle="offcanvas" data-bs-target="#offcanvas_pc_layout">
                            <i class="ti ti-settings"></i>
                        </a>
                    </li>
                @endif
                <li class="dropdown pc-h-item header-user-profile">
                    <a class="pc-head-link head-link-primary dropdown-toggle arrow-none me-0" data-bs-toggle="dropdown"
                        href="#" role="button" aria-haspopup="false" aria-expanded="false">
                        <img src="{{$profile}}" alt="user-image" class="user-avtar" />
                        <span>
                            <i class="ti ti-user-check"></i>
                        </span>
                    </a>
                    <div class="dropdown-menu dropdown-user-profile dropdown-menu-end pc-h-dropdown">
                        <div class="dropdown-header">
                            <div class="d-flex align-items-center mb-3">
                                <div class="flex-shrink-0">
                                    <img src="{{$profile}}" alt="user-image" class="img-fluid rounded-circle" style="width: 50px;" />
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h5 class="mb-1">{{auth('tenant')->user()->name}}</h5>
                                    <p class="text-muted small mb-0">{{auth('tenant')->user()->type}}</p>
                                </div>
                            </div>

                            <div class="profile-notification-scroll position-relative" style="max-height: calc(100vh - 280px)">
                                <div class="list-group list-group-flush">
                                    <!-- User Profile Link -->
                                    <a href="" class="list-group-item list-group-item-action">
                                        <div class="d-flex align-items-center">
                                            <i class="ti ti-user-circle me-2 text-primary"></i>
                                            <span>{{ __('My Profile') }}</span>
                                        </div>
                                    </a>
                                    
                                    <!-- My Reminders Link -->
                                    <a href="{{ route('tenant.reminder.my') }}" class="list-group-item list-group-item-action">
                                        <div class="d-flex align-items-center">
                                            <i class="ti ti-bell me-2 text-warning"></i>
                                            <span>{{ __('My Reminders') }}</span>
                                        </div>
                                    </a>
                                    
                                    <!-- Logout Link -->
                                    <a href="{{ route('tenant.logout') }}" class="list-group-item list-group-item-action" onclick="event.preventDefault(); document.getElementById('frm-logout').submit();">
                                        <div class="d-flex align-items-center">
                                            <i class="ti ti-logout me-2 text-danger"></i>
                                            <span>{{ __('Logout') }}</span>
                                        </div>
                                    </a>
                                    <form id="frm-logout" action="{{ route('tenant.logout') }}" method="POST" class="d-none">
                                        {{ csrf_field() }}
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </li>
            </ul>
        </div>
    </div>
</header>
