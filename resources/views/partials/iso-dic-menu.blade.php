@php
    $admin_logo = getSettingsValByName('company_logo');
    $ids = parentId();
    $authUser = \App\Models\User::find($ids);
    $routeName = \Request::route()->getName();
@endphp
<nav class="pc-sidebar">
    <div class="navbar-wrapper">
        <div class="m-header">
            <a href="#" class="b-brand text-primary">
                <img src="{{ asset('assets/images/logo.png') }}"
                    alt="" class="logo logo-lg" />
            </a>
        </div>
        <div class="navbar-content">

            <ul class="pc-navbar">
                <li class="pc-item {{ in_array($routeName, ['dashboard', 'home', '']) ? 'active' : '' }}">
                    <a href="{{ route('iso_dic.home') }}" class="pc-link">
                        <span class="pc-micon"><i class="ti ti-dashboard"></i></span>
                        <span class="pc-mtext">{{ __('Dashboard') }}</span>
                    </a>
                </li>

                <li
                    class="pc-item {{ in_array($routeName, ['iso_systems.index', 'iso_systems.show']) ? 'active' : '' }}">
                    <a href="{{ route('iso_dic.iso_systems.index') }}" class="pc-link">
                        <span class="pc-micon"><i class="ti ti-apps"></i></span>
                        <span class="pc-mtext">{{ __('ISO Systems') }}</span>
                    </a>
                </li>

                <li
                    class="pc-item {{ in_array($routeName, ['specification_items.index', 'specification_items.show']) ? 'active' : '' }}">
                    <a href="{{ route('iso_dic.specification_items.index') }}" class="pc-link">
                        <span class="pc-micon"><i class="ti ti-apps"></i></span>
                        <span class="pc-mtext">{{ __('ISO specification items') }}</span>
                    </a>
                </li>

                <li class="pc-item {{ in_array($routeName, ['iso_dic.filemanager']) ? 'active' : '' }}">
                    <a href="{{ route('iso_dic.filemanager') }}" class="pc-link">
                        <span class="pc-micon"><i class="ti ti-folder"></i></span>
                        <span class="pc-mtext">{{ __('File Manager') }}</span>
                    </a>
                </li>

                <li class="pc-item {{ in_array($routeName, ['procedures.index']) ? 'active' : '' }}">
                    <a href="{{ route('iso_dic.procedures.index') }}" class="pc-link">
                        <span class="pc-micon"><i class="ti ti-propeller"></i></span>
                        <span class="pc-mtext">{{ __('Procedures') }}</span>
                    </a>
                </li>

                <li class="pc-item {{ in_array($routeName, ['samples.index']) ? 'active' : '' }}">
                    <a href="{{ route('iso_dic.samples.index') }}" class="pc-link">
                        <span class="pc-micon"><i class="ti ti-squares-filled"></i></span>
                        <span class="pc-mtext">{{ __('Samples') }}</span>
                    </a>
                </li>

                <!-- References Menu Item -->
                <li class="pc-item {{ Request::route()->getName() == 'iso_dic.references.index' ? ' active' : '' }}">
                    <a href="{{ route('iso_dic.references.index') }}" class="pc-link">
                        <span class="pc-micon">
                            <i class="ti ti-book"></i>
                        </span>
                        <span class="pc-mtext">{{ __('References') }}</span>
                    </a>
                </li>

                <!-- Instructions Menu Item -->
                <li class="pc-item {{ Request::route()->getName() == 'iso_dic.instructions.index' ? ' active' : '' }}">
                    <a href="{{ route('iso_dic.instructions.index') }}" class="pc-link">
                        <span class="pc-micon">
                            <i class="ti ti-file-invoice"></i>
                        </span>
                        <span class="pc-mtext">{{ __('Instructions') }}</span>
                    </a>
                </li>

                <!-- Policies Menu Item -->
                <li class="pc-item {{ Request::route()->getName() == 'iso_dic.policies.index' ? ' active' : '' }}">
                    <a href="{{ route('iso_dic.policies.index') }}" class="pc-link">
                        <span class="pc-micon">
                            <i class="ti ti-file-certificate"></i>
                        </span>
                        <span class="pc-mtext">{{ __('Policies') }}</span>
                    </a>
                </li>

                <li class="pc-item {{ in_array($routeName, ['countries.index']) ? 'active' : '' }}">
                    <a href="{{ route('iso_dic.countries.index') }}" class="pc-link">
                        <span class="pc-micon"><i class="ti ti-squares-filled"></i></span>
                        <span class="pc-mtext">{{ __('Countries') }} & {{ __('Cities') }} </span>
                    </a>
                </li>
                <li class="pc-item {{ in_array($routeName, ['setting.index']) ? 'active' : '' }} ">
                    <a href="{{ route('iso_dic.setting.index') }}" class="pc-link">
                        <span class="pc-micon"><i class="ti ti-settings"></i></span>
                        <span class="pc-mtext">{{ __('Settings') }}</span>
                    </a>
                </li>
                {{-- <li class="pc-item pc-caption">
                    <label>{{ __('Business Management') }}</label>
                    <i class="ti ti-chart-arcs"></i>
                </li>

                <li
                    class="pc-item {{ Request::route()->getName() == 'iso_dic.document.index' || Request::route()->getName() == 'iso_dic.document.show' || Request::route()->getName() == 'iso_dic.document.comment' || Request::route()->getName() == 'iso_dic.document.reminder' || Request::route()->getName() == 'iso_dic.document.version.history' || Request::route()->getName() == 'iso_dic.document.share' || Request::route()->getName() == 'iso_dic.document.send.email' ? 'active' : '' }}">
                    <a href="{{ route('iso_dic.document.index') }}" class="pc-link">
                        <span class="pc-micon"><i data-feather="file-text"></i></span>
                        <span class="pc-mtext">{{ __('All Documents') }}</span>
                    </a>
                </li> --}}



                {{-- <li class="pc-item {{ Request::route()->getName() == 'my-reminder' ? 'active' : '' }}">
                    <a href="{{ route('my-reminder') }}" class="pc-link">
                        <span class="pc-micon"><i data-feather="aperture"></i></span>
                        <span class="pc-mtext">{{ __('My Reminders') }}</span>
                    </a>
                </li> --}}


                {{-- <li class="pc-item {{ Request::route()->getName() == 'document.history' ? 'active' : '' }}">
                    <a href="{{ route('document.history') }}" class="pc-link">
                        <span class="pc-micon"><i data-feather="wind"></i></span>
                        <span class="pc-mtext">{{ __('Document History') }}</span>
                    </a>
                </li>

                
                <li class="pc-item {{ Request::route()->getName() == 'category.index' ? 'active' : '' }}">
                    <a href="{{ route('category.index') }}" class="pc-link">
                        <span class="pc-micon"><i data-feather="list"></i></span>
                        <span class="pc-mtext">{{ __('Category') }}</span>
                    </a>
                </li>
                <li class="pc-item {{ Request::route()->getName() == 'sub-category.index' ? 'active' : '' }}">
                    <a href="{{ route('sub-category.index') }}" class="pc-link">
                        <span class="pc-micon"><i data-feather="sliders"></i></span>
                        <span class="pc-mtext">{{ __('Sub Category') }}</span>
                    </a>
                </li>

                <li class="pc-item {{ Request::route()->getName() == 'tag.index' ? 'active' : '' }}">
                    <a href="{{ route('tag.index') }}" class="pc-link">
                        <span class="pc-micon"><i data-feather="layers"></i></span>
                        <span class="pc-mtext">{{ __('Tags') }}</span>
                    </a>
                </li>

                <li class="pc-item pc-caption">
                    <label>{{ __('System Settings') }}</label>
                    <i class="ti ti-chart-arcs"></i>
                </li>

             
                <li class="pc-item {{ in_array($routeName, ['users.index', 'users.show']) ? 'active' : '' }}">
                    <a href="{{ route('users.index') }}" class="pc-link">
                        <span class="pc-micon"><i class="ti ti-user-plus"></i></span>
                        <span class="pc-mtext">{{ __('Users') }}</span>
                    </a>
                </li>

                <li class="pc-item  {{ in_array($routeName, ['logged.history']) ? 'active' : '' }}">
                    <a class="pc-link" href="{{ route('logged.history') }}">
                        <span class="pc-micon"><i class="ti ti-report"></i></span>
                        <span class="pc-mtext">{{ __('Logged History') }}</span>

                    </a>
                </li> --}}

            </ul>
            <div class="w-100 text-center">
                <div class="badge theme-version badge rounded-pill bg-light text-dark f-12"></div>
            </div>
        </div>
    </div>
</nav>
