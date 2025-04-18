@php
    $admin_logo = getSettingsValByName('company_logo');
    // $ids = parentId();
    // $authUser = \App\Models\User::find($ids);
    // $subscription = \App\Models\Subscription::find($authUser->subscription);
    $routeName = \Request::route()->getName();
    // $pricing_feature_settings = getSettingsValByIdName(1, 'pricing_feature');
@endphp
<nav class="pc-sidebar">
    <div class="navbar-wrapper">
        <div class="m-header">
            <a href="#" class="b-brand text-primary">
                <img src="{{ asset(Storage::url('upload/logo/')) . '/' . (isset($admin_logo) && !empty($admin_logo) ? $admin_logo : 'logo.png') }}"
                    alt="" class="logo logo-lg" />
            </a>
        </div>
        <div class="navbar-content">
            <ul class="pc-navbar">
                
                <li class="pc-item {{ in_array($routeName, ['tenant.dashboard', 'home', '']) ? 'active' : '' }}">
                    <a href="{{ route('tenant.dashboard') }}" class="pc-link">
                        <span class="pc-micon"><i class="ti ti-dashboard"></i></span>
                        <span class="pc-mtext">{{ __('Dashboard') }}</span>
                    </a>
                </li>

                <!-- Document Control -->
                <li
                    class="pc-item pc-hasmenu {{ in_array($routeName, ['document.index', 'document.show']) ? 'active' : '' }}">
                    <a href="#!" class="pc-link">
                        <span class="pc-micon"><i class="ti ti-file-text"></i></span>
                        <span class="pc-mtext">{{ __('Document Control') }}</span>
                        <span class="pc-arrow"><i data-feather="chevron-right"></i></span>
                    </a>
                    <ul class="pc-submenu">
                        <li class="pc-item {{ $routeName == 'document.index' ? 'active' : '' }}">
                            <a href="{{ route('tenant.document.index') }}" class="pc-link">
                                <span class="pc-mtext">{{ __('All Documents') }}</span>
                            </a>
                        </li>
                        <li class="pc-item {{ $routeName == 'document.create' ? 'active' : '' }}">
                            <a href="{{ route('tenant.document.create') }}" class="pc-link">
                                <span class="pc-mtext">{{ __('Create New Document') }}</span>
                            </a>
                        </li>
                        <li class="pc-item {{ $routeName == 'document.versions' ? 'active' : '' }}">
                            <a href="" class="pc-link">
                                <span class="pc-mtext">{{ __('Version Control') }}</span>
                            </a>
                        </li>
                        <li class="pc-item {{ $routeName == 'document.history' ? 'active' : '' }}">
                            <a href="" class="pc-link">
                                <span class="pc-mtext">{{ __('Change History') }}</span>
                            </a>
                        </li>
                        <li class="pc-item {{ $routeName == 'document.workflow' ? 'active' : '' }}">
                            <a href="" class="pc-link">
                                <span class="pc-mtext">{{ __('Approval Workflow') }}</span>
                            </a>
                        </li>
                        <li class="pc-item {{ $routeName == 'document.notifications' ? 'active' : '' }}">
                            <a href="" class="pc-link">
                                <span class="pc-mtext">{{ __('Review Notifications') }}</span>
                            </a>
                        </li>
                        <li class="pc-item {{ $routeName == 'document.categories' ? 'active' : '' }}">
                            <a href="" class="pc-link">
                                <span class="pc-mtext">{{ __('Document Categories') }}</span>
                            </a>
                        </li>
                        <li class="pc-item {{ $routeName == 'document.permissions' ? 'active' : '' }}">
                            <a href="" class="pc-link">
                                <span class="pc-mtext">{{ __('Access Permissions') }}</span>
                            </a>
                        </li>
                        <li class="pc-item {{ $routeName == 'document.archive' ? 'active' : '' }}">
                            <a href="" class="pc-link">
                                <span class="pc-mtext">{{ __('Document Archive') }}</span>
                            </a>
                        </li>
                    </ul>
                </li>

                <!-- Audit Management -->
                <li
                    class="pc-item pc-hasmenu {{ in_array($routeName, ['audit.index', 'audit.show']) ? 'active' : '' }}">
                    <a href="#!" class="pc-link">
                        <span class="pc-micon"><i class="ti ti-clipboard-check"></i></span>
                        <span class="pc-mtext">{{ __('Audit Management') }}</span>
                        <span class="pc-arrow"><i data-feather="chevron-right"></i></span>
                    </a>
                    <ul class="pc-submenu">
                        <li class="pc-item {{ $routeName == 'audit.index' ? 'active' : '' }}">
                            <a href="" class="pc-link">
                                <span class="pc-mtext">{{ __('All Audits') }}</span>
                            </a>
                        </li>
                    </ul>
                </li>

                <!-- Corrective Actions -->
                <li
                    class="pc-item pc-hasmenu {{ in_array($routeName, ['corrective_action.index', 'corrective_action.show']) ? 'active' : '' }}">
                    <a href="#!" class="pc-link">
                        <span class="pc-micon"><i class="ti ti-tool"></i></span>
                        <span class="pc-mtext">{{ __('Corrective Actions') }}</span>
                        <span class="pc-arrow"><i data-feather="chevron-right"></i></span>
                    </a>
                    <ul class="pc-submenu">
                        <li class="pc-item {{ $routeName == 'corrective_action.index' ? 'active' : '' }}">
                            <a href="" class="pc-link">
                                <span class="pc-mtext">{{ __('All Corrective Actions') }}</span>
                            </a>
                        </li>
                    </ul>
                </li>

                <!-- Non-Conformance -->
                <li
                    class="pc-item pc-hasmenu {{ in_array($routeName, ['nonconformance.index', 'nonconformance.show']) ? 'active' : '' }}">
                    <a href="#!" class="pc-link">
                        <span class="pc-micon"><i class="ti ti-alert-triangle"></i></span>
                        <span class="pc-mtext">{{ __('Non-Conformance Management') }}</span>
                        <span class="pc-arrow"><i data-feather="chevron-right"></i></span>
                    </a>
                    <ul class="pc-submenu">
                        <li class="pc-item {{ $routeName == 'nonconformance.index' ? 'active' : '' }}">
                            <a href="" class="pc-link">
                                <span class="pc-mtext">{{ __('All Non-Conformances') }}</span>
                            </a>
                        </li>
                    </ul>
                </li>

                <!-- Training Management -->
                <li
                    class="pc-item pc-hasmenu {{ in_array($routeName, ['training.index', 'training.show']) ? 'active' : '' }}">
                    <a href="#!" class="pc-link">
                        <span class="pc-micon"><i class="ti ti-certificate"></i></span>
                        <span class="pc-mtext">{{ __('Training Management') }}</span>
                        <span class="pc-arrow"><i data-feather="chevron-right"></i></span>
                    </a>
                    <ul class="pc-submenu">
                        <li class="pc-item {{ $routeName == 'training.index' ? 'active' : '' }}">
                            <a href="" class="pc-link">
                                <span class="pc-mtext">{{ __('All Training') }}</span>
                            </a>
                        </li>
                    </ul>
                </li>

                <!-- Risk Management -->
                <li
                    class="pc-item pc-hasmenu {{ in_array($routeName, ['risk.index', 'risk.show']) ? 'active' : '' }}">
                    <a href="#!" class="pc-link">
                        <span class="pc-micon"><i class="ti ti-alert-circle"></i></span>
                        <span class="pc-mtext">{{ __('Risk Management') }}</span>
                        <span class="pc-arrow"><i data-feather="chevron-right"></i></span>
                    </a>
                    <ul class="pc-submenu">
                        <li class="pc-item {{ $routeName == 'risk.index' ? 'active' : '' }}">
                            <a href="" class="pc-link">
                                <span class="pc-mtext">{{ __('All Risks') }}</span>
                            </a>
                        </li>
                    </ul>
                </li>

                <!-- Management Review -->
                <li
                    class="pc-item pc-hasmenu {{ in_array($routeName, ['management_review.index', 'management_review.show']) ? 'active' : '' }}">
                    <a href="#!" class="pc-link">
                        <span class="pc-micon"><i class="ti ti-calendar-stats"></i></span>
                        <span class="pc-mtext">{{ __('Management Review') }}</span>
                        <span class="pc-arrow"><i data-feather="chevron-right"></i></span>
                    </a>
                    <ul class="pc-submenu">
                        <li class="pc-item {{ $routeName == 'management_review.index' ? 'active' : '' }}">
                            <a href="" class="pc-link">
                                <span class="pc-mtext">{{ __('All Reviews') }}</span>
                            </a>
                        </li>
                    </ul>
                </li>

                <!-- Compliance Management -->
                <li
                    class="pc-item pc-hasmenu {{ in_array($routeName, ['compliance.index', 'compliance.show']) ? 'active' : '' }}">
                    <a href="#!" class="pc-link">
                        <span class="pc-micon"><i class="ti ti-shield-check"></i></span>
                        <span class="pc-mtext">{{ __('Compliance Management') }}</span>
                        <span class="pc-arrow"><i data-feather="chevron-right"></i></span>
                    </a>
                    <ul class="pc-submenu">
                        <li class="pc-item {{ $routeName == 'compliance.index' ? 'active' : '' }}">
                            <a href="" class="pc-link">
                                <span class="pc-mtext">{{ __('All Compliance') }}</span>
                            </a>
                        </li>
                    </ul>
                </li>

                <!-- Complaints Management -->
                <li
                    class="pc-item pc-hasmenu {{ in_array($routeName, ['complaints.index', 'complaints.show']) ? 'active' : '' }}">
                    <a href="#!" class="pc-link">
                        <span class="pc-micon"><i class="ti ti-message-report"></i></span>
                        <span class="pc-mtext">{{ __('Complaints Management') }}</span>
                        <span class="pc-arrow"><i data-feather="chevron-right"></i></span>
                    </a>
                    <ul class="pc-submenu">
                        <li class="pc-item {{ $routeName == 'complaints.index' ? 'active' : '' }}">
                            <a href="" class="pc-link">
                                <span class="pc-mtext">{{ __('All Complaints') }}</span>
                            </a>
                        </li>
                    </ul>
                </li>
                <!-- Reports -->
                <li
                    class="pc-item pc-hasmenu {{ in_array($routeName, ['reports.index', 'reports.show']) ? 'active' : '' }}">
                    <a href="#!" class="pc-link">
                        <span class="pc-micon"><i class="ti ti-chart-line"></i></span>
                        <span class="pc-mtext">{{ __('Reports & Analytics') }}</span>
                        <span class="pc-arrow"><i data-feather="chevron-right"></i></span>
                    </a>
                    <ul class="pc-submenu">
                        <li class="pc-item {{ $routeName == 'reports.index' ? 'active' : '' }}">
                            <a href="" class="pc-link">
                                <span class="pc-mtext">{{ __('All Reports') }}</span>
                            </a>
                        </li>
                    </ul>
                </li>

                <!-- Roles & Permissions -->
                <li
                    class="pc-item pc-hasmenu {{ in_array($routeName, ['roles.index', 'roles.show']) ? 'active' : '' }}">
                    <a href="#!" class="pc-link">
                        <span class="pc-micon"><i class="ti ti-lock"></i></span>
                        <span class="pc-mtext">{{ __('Roles & Permissions') }}</span>
                        <span class="pc-arrow"><i data-feather="chevron-right"></i></span>
                    </a>
                    <ul class="pc-submenu">
                        <li class="pc-item {{ $routeName == 'roles.index' ? 'active' : '' }}">
                            <a href="" class="pc-link">
                                <span class="pc-mtext">{{ __('All Roles') }}</span>
                            </a>
                        </li>
                    </ul>
                </li>




                <!-- Settings -->
                <li
                    class="pc-item pc-hasmenu {{ in_array($routeName, ['settings.index', 'settings.organization', 'settings.general', 'settings.other', 'settings.consultants', 'settings.users', 'settings.backup']) ? 'active' : '' }}">
                    <a href="#!" class="pc-link">
                        <span class="pc-micon"><i class="ti ti-settings"></i></span>
                        <span class="pc-mtext">{{ __('Settings') }}</span>
                        <span class="pc-arrow"><i data-feather="chevron-right"></i></span>
                    </a>
                    <ul class="pc-submenu">
                        <li class="pc-item {{ $routeName == 'settings.index' ? 'active' : '' }}">
                            <a href="#" class="pc-link">
                                <span class="pc-mtext">{{ __('Company Profile') }}</span>
                            </a>
                        </li>
                        <li class="pc-item {{ $routeName == 'settings.organization' ? 'active' : '' }}">
                            <a href="#" class="pc-link">
                                <span class="pc-mtext">{{ __('Organization Structure') }}</span>
                            </a>
                        </li>
                        <li class="pc-item {{ $routeName == 'settings.general' ? 'active' : '' }}">
                            <a href="#" class="pc-link">
                                <span class="pc-mtext">{{ __('General Setting') }}</span>
                            </a>
                        </li>
                        <li class="pc-item {{ $routeName == 'settings.other' ? 'active' : '' }}">
                            <a href="#" class="pc-link">
                                <span class="pc-mtext">{{ __('Other Setting') }}</span>
                            </a>
                        </li>
                        <li class="pc-item {{ $routeName == 'settings.consultants' ? 'active' : '' }}">
                            <a href="#" class="pc-link">
                                <span class="pc-mtext">{{ __('Consultants') }}</span>
                            </a>
                        </li>
                        <li class="pc-item {{ $routeName == 'settings.users' ? 'active' : '' }}">
                            <a href="#" class="pc-link">
                                <span class="pc-mtext">{{ __('Users') }}</span>
                            </a>
                        </li>
                        <li class="pc-item {{ $routeName == 'settings.backup' ? 'active' : '' }}">
                            <a href="#" class="pc-link">
                                <span class="pc-mtext">{{ __('Backup') }}</span>
                            </a>
                        </li>
                    </ul>
                </li>



                
            </ul>
        </div>
    </div>
</nav>
