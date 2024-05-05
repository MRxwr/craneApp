<!-- Sidebar user (optional) -->


<!-- Sidebar Menu -->
<nav class="mt-2">
    <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
        <!-- Add icons to the links using the .nav-icon class
               with font-awesome or any other icon font library -->
       <li class="nav-item">
            <a href="{{ url('dashboard') }}" class="nav-link">
                <i class="nav-icon fas fa-home"></i>
                <p>{{_lang('Dashboard')}}</p>
            </a>
        </li>      

        <li class="nav-item {{ request()->segment(1) == 'users' ? 'menu-open' : '' }}">
            <a href="#" class="nav-link {{ request()->segment(1) == 'users' ? 'active' : '' }}">
                <i class="nav-icon fas fa-users"></i>
                <p>
                {{_lang('Users')}}
                    <i class="right fas fa-angle-left"></i>
                </p>
            </a>
            <ul class="nav nav-treeview">
                @if (akses('view-users'))
                    <li class="nav-item">
                        <a href="{{ url('users/index') }}"
                            class="nav-link {{ request()->segment(1) == 'users' && request()->segment(2) == 'index' ? 'active' : '' }}">
                            <i class="far fa-circle nav-icon"></i>
                            <p>{{_lang('Manage Users')}}</p>
                        </a>
                    </li>
                @endif

                @if (akses('view-client'))
                    <li class="nav-item">
                        <a href="{{ url('users/clients') }}"
                            class="nav-link {{ request()->segment(1) == 'users' && request()->segment(2) == 'clients' ? 'active' : '' }}">
                            <i class="far fa-circle nav-icon"></i>
                            <p>{{_lang('Manage Clients')}}</p>
                        </a>
                    </li>
                @endif

                @if (akses('view-driver'))
                    <li class="nav-item">
                        <a href="{{ url('users/drivers') }}"
                            class="nav-link {{ request()->segment(1) == 'users' && request()->segment(2) == 'drivers' ? 'active' : '' }}">
                            <i class="far fa-circle nav-icon"></i>
                            <p>{{_lang('Manage Drivers')}}</p>
                        </a>
                    </li>
                @endif

                @if (akses('view-roles'))
                    <li class="nav-item">
                        <a href="{{ url('users/roles') }}"
                            class="nav-link {{ request()->segment(1) == 'users' && request()->segment(2) == 'roles' ? 'active' : '' }}">
                            <i class="far fa-circle nav-icon"></i>
                            <p>{{_lang('Manage Role')}}</p>
                        </a>
                    </li>
                @endif
            </ul>
        </li>
        @if (akses('view-service'))
          <li class="nav-item">
             <a href="{{ url('services/index') }}" class="nav-link">
                <i class="nav-icon fas fa-th"></i>
                <p>{{_lang('Services')}}</p>
             </a>
          </li> 
        @endif
        <li class="nav-item {{ in_array(request()->segment(1),['pages','faqs']) ? 'menu-open' : '' }}">
            <a href="#" class="nav-link {{ request()->segment(1) == 'users' ? 'active' : '' }}">
            <i class="nav-icon fas fa-file"></i>
                <p>
                {{_lang('CMS')}}
                    <i class="right fas fa-angle-left"></i>
                </p>
            </a>
            <ul class="nav nav-treeview">
            @if (akses('view-pages'))
            <li class="nav-item">
                <a href="{{ url('pages/index') }}" class="nav-link">
                <i class="nav-icon fas fa-file"></i>
                    <!-- <i class="nav-icon fas fa-file"></i> -->
                    <p>{{_lang('Pages')}}</p>
                </a>
            </li> 
            @endif
            @if (akses('view-faqs'))
            <li class="nav-item">
                <a href="{{ url('faqs/index') }}" class="nav-link">
                    <i class="nav-icon fa-regular fa-circle-question"></i>
                    <p>{{_lang('Faqs')}}</p>
                </a>
            </li> 
            @endif
        </ul>
        </li>
        <!-- Settings -->

        <li class="nav-item {{ request()->segment(1) == 'settings' ? 'menu-open' : '' }}">
            <a href="#" class="nav-link {{ request()->segment(1) == 'settings' ? 'active' : '' }}">
                <i class="nav-icon fas fa-cog "></i>
                <p>{{_lang('Settings')}}
                    <i class="right fas fa-angle-left"></i>
                </p>
            </a>

            <ul class="nav nav-treeview">
              @if (akses('view-setting'))
                    <li class="nav-item">
                        <a href="{{ route('settings.index',['rowId'=>1]) }}"
                            class="nav-link {{ request()->segment(1) == 'settings' && request()->segment(2) == 'index' ? 'active' : '' }}">
                            <i class="far fa-circle nav-icon"></i>
                            <p>{{_lang('Manage Settings')}}</p>
                        </a>
                    </li>
                @endif
                @if (akses('view-language'))
                    <li class="nav-item">
                        <a href="{{ route('languages.index') }}"
                            class="nav-link {{ request()->segment(1) == 'settings' && request()->segment(2) == 'languages' ? 'active' : '' }}">
                            <i class="far fa-circle nav-icon"></i>
                            <p>{{_lang('Manage Languages')}}</p>
                        </a>
                    </li>
                @endif

                @if (akses('view-locale'))
                    <li class="nav-item">
                        <a href="{{ route('locales.index') }}"
                            class="nav-link {{ request()->segment(1) == 'settings' && request()->segment(2) == 'locales' ? 'active' : '' }}">
                            <i class="far fa-circle nav-icon"></i>
                            <p>{{_lang('Manage locales')}}</p>
                        </a>
                    </li>
                @endif
            </ul>
        </li>

        <li class="nav-item">
            <a href="{{ url('logout') }}" class="nav-link">
                <i class="nav-icon fas fa-columns"></i>
                <p>{{_lang('Logout')}}</p>
            </a>
        </li>

    </ul>
</nav>
<!-- /.sidebar-menu -->
