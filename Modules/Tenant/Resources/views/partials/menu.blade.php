@php
    $admin_logo = getSettingsValByName('company_logo');
    // $ids = parentId();
    // $authUser = \App\Models\User::find($ids);
    // $subscription = \App\Models\Subscription::find($authUser->subscription);
    $authUser = Auth::guard('tenant')->user();
    $routeName = \Request::route()->getName();
    // $pricing_feature_settings = getSettingsValByIdName(1, 'pricing_feature');
@endphp
<nav class="pc-sidebar">
    <div class="navbar-wrapper">
        <div class="m-header">
            <a href="#" class="b-brand text-primary">
                <img src="{{ route('tenant.setting.file',getSettingsValByName('company_logo')) }}"
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
                
                  
                
                @if(Gate::check('Manage Documents') || Gate::check('Create New Document') || Gate::check('Change History') || Gate::check('Approval Workflow') || Gate::check('Review Notifications') || Gate::check('Document Categories') || Gate::check('Access Permissions') || Gate::check('Document Archive') )
                    <li
                        class="pc-item pc-hasmenu {{ in_array($routeName, ['document.index', 'document.show', 'document.requests.index','document.procedures.main','document.procedures.public','document.procedures.private', 'document.requests.create', 'document.requests.my']) ? 'active' : '' }}">
                        <a href="#!" class="pc-link">
                            <span class="pc-micon"><i class="ti ti-file-text"></i></span>
                            <span class="pc-mtext">{{ __('Document Control') }}</span>
                            <span class="pc-arrow"><i data-feather="chevron-right"></i></span>
                        </a>
                        <ul class="pc-submenu">
                            @if(Gate::check('View Documents'))
                            <li class="pc-item pc-hasmenu {{ $routeName == 'document.index' ? 'active' : '' }}">
                                <a href="#!" class="pc-link">
                                    <span class="pc-mtext">{{ __('All Documents') }}</span>
                                    <span class="pc-arrow"><i data-feather="chevron-right"></i></span>
                                </a>
                                <ul class="pc-submenu">
                                    <li class="pc-item {{ $routeName == 'document.procedures.main' ? 'active' : '' }}">
                                        <a href="{{ route('tenant.document.procedures.main') }}" class="pc-link">
                                            <span class="pc-mtext">{{ __('Main Procedures') }}</span>
                                        </a>
                                    </li>
                                    <li class="pc-item {{ $routeName == 'document.procedures.public' ? 'active' : '' }}">
                                        <a href="{{ route('tenant.document.procedures.public') }}" class="pc-link">
                                            <span class="pc-mtext">{{ __('Public Procedures') }}</span>
                                        </a>
                                    </li>
                                    <li class="pc-item {{ $routeName == 'document.procedures.private' ? 'active' : '' }}">
                                        <a href="{{ route('tenant.document.procedures.private') }}" class="pc-link">
                                            <span class="pc-mtext">{{ __('Private Procedures') }}</span>
                                        </a>
                                    </li>
                                    <li class="pc-item {{ $routeName == 'document.supporting-documents' ? 'active' : '' }}">
                                        <a href="{{ route('tenant.document.supporting-documents.index') }}" class="pc-link">
                                            <span class="pc-mtext">{{ __('Supporting Documents') }}</span>
                                        </a>
                                    </li>
                                  
                                </ul>
                            </li>
                            @endif

                            {{-- @if(Gate::check('Create Documents'))
                            <li class="pc-item {{ $routeName == 'document.create' ? 'active' : '' }}">
                                <a href="{{ route('tenant.document.create') }}" class="pc-link">
                                    <span class="pc-mtext">{{ __('Create New Document') }}</span>
                                </a>
                            </li>
                            @endif --}}
                            @if(Gate::check('Manage Document Versions'))
                            <li class="pc-item {{ $routeName == 'document.versions' ? 'active' : '' }}">
                                <a href="" class="pc-link">
                                    <span class="pc-mtext">{{ __('Version Control') }}</span>
                                </a>
                            </li>
                            @endif
                        
                            @if(Gate::check('Manage Document Workflows'))
                            <li class="pc-item {{ $routeName == 'document.workflow' ? 'active' : '' }}">
                                <a href="{{ route('tenant.document.workflow.index') }}" class="pc-link">
                                    <span class="pc-mtext">{{ __('Approval Workflow') }}</span>
                                </a>
                            </li>
                            @endif
                            @if(Gate::check('View Document Review Notifications'))
                            <li
                                class="pc-item {{ in_array($routeName, ['tenant.notifications.index']) ? 'active' : '' }}">
                                <a href="{{ route('tenant.notifications.index') }}" class="pc-link">
                                    <span class="pc-mtext">{{ __('Review Notifications') }}</span>
                                </a>
                            </li>
                            @endif
                            @if(Gate::check('Manage Document Categories'))
                            <li class="pc-item {{ $routeName == 'document.categories.index' ? 'active' : '' }}">
                                <a href="{{ route('tenant.document.categories.index') }}" class="pc-link">
                                    <span class="pc-mtext">{{ __('Document Categories') }}</span>
                                </a>                                                    
                            </li>
                            @endif
                            @if(Gate::check('Manage Requests'))
                            <li class="pc-item {{ in_array($routeName, ['tenant.document.requests.index', 'document.requests.create']) ? 'active' : '' }}">
                                <a href="{{ route('tenant.document.requests.index') }}" class="pc-link">
                                    <span class="pc-mtext">{{ __('Manage Requests') }}</span>
                                </a>
                            </li>
                            @endif
                            @if(Gate::check('Manage Document Archive'))
                            <li class="pc-item {{ $routeName == 'document.archive' ? 'active' : '' }}">
                                <a href="" class="pc-link">
                                    <span class="pc-mtext">{{ __('Document Archive') }}</span>
                                </a>
                            </li>
                            @endif
                            @if(Gate::check('View Document Change History'))
                            <li class="pc-item {{ $routeName == 'document.history' ? 'active' : '' }}">
                                <a href="{{ route('tenant.document.history.index') }}" class="pc-link">
                                    <span class="pc-mtext">{{ __('Change History') }}</span>
                                </a>
                            </li>
                            @endif
                        </ul>
                    </li>
                @endif
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
                        <li class="pc-item {{ $routeName == 'tenant.role.users.index' ? 'active' : '' }}">
                            <a href="{{ route('tenant.role.users.index') }}" class="pc-link">
                                <span class="pc-mtext">{{ __('Users') }}</span>
                            </a>
                        </li>
                        <li class="pc-item {{ $routeName == 'tenant.role.roles.index' ? 'active' : '' }}">
                            <a href="{{ route('tenant.role.roles.index') }}" class="pc-link">
                                <span class="pc-mtext">{{ __('Roles') }}</span>
                            </a>
                        </li>
                        <li class="pc-item {{ $routeName == 'tenant.role.permissions.index' ? 'active' : '' }}">
                            <a href="{{ route('tenant.role.permissions.index') }}" class="pc-link">
                                <span class="pc-mtext">{{ __('Permissions') }}</span>
                            </a>
                        </li>
                    </ul>
                </li>




                <!-- Settings -->




                <!-- Settings -->
                <li
                    class="pc-item pc-hasmenu {{ in_array($routeName, ['setting.index', 'setting.organization', 'setting.general', 'setting.other', 'setting.consultants', 'setting.users', 'setting.backup']) ? 'active' : '' }}">
                    <a href="#!" class="pc-link">
                        <span class="pc-micon"><i class="ti ti-settings"></i></span>
                        <span class="pc-mtext">{{ __('Settings') }}</span>
                        <span class="pc-arrow"><i data-feather="chevron-right"></i></span>
                    </a>
                    <ul class="pc-submenu">
                        <li class="pc-item {{ $routeName == 'tenant.setting.index' ? 'active' : '' }}">
                            <a href="{{ route('tenant.setting.index') }}" class="pc-link">
                                <span class="pc-mtext">{{ __('Settings') }}</span>
                            </a>
                        </li>
                        <li
                            class="pc-item {{ Str::startsWith($routeName, 'tenant.setting.organization.') ? 'active' : '' }}">
                            <a href="{{ route('tenant.setting.organization.index') }}" class="pc-link">
                                <span class="pc-mtext">{{ __('Organization Structure') }}</span>
                            </a>

                        </li>
                        <li
                            class="pc-item {{ Str::startsWith($routeName, 'tenant.setting.company-seals.') ? 'active' : '' }}">
                            <a href="{{ route('tenant.setting.company-seals.index') }}" class="pc-link">
                                <span class="pc-mtext">{{ __('Company Seals') }}</span>
                            </a>
                        </li>
                        <li class="pc-item {{ $routeName == 'tenant.setting.consultants' ? 'active' : '' }}">
                            <a href="{{ route('tenant.setting.consultants') }}" class="pc-link">
                                <span class="pc-mtext">{{ __('Consultants') }}</span>
                            </a>
                        </li>
                        <li class="pc-item {{ $routeName == 'tenant.setting.backup' ? 'active' : '' }}">
                            <a href="{{ route('tenant.setting.backup') }}" class="pc-link">
                                <span class="pc-mtext">{{ __('Backup') }}</span>
                            </a>
                        </li>
                    </ul>
                </li>




            </ul>
        </div>
    </div>
</nav>
