<div class="app-menu navbar-menu">
    <!-- LOGO -->
    <div class="navbar-brand-box">
        <!-- Dark Logo-->
        <a href="{{ route('admin.dashboard') }}" class="logo logo-dark">
            <span class="logo-sm">
                <img src="{{ asset('assets/images/logo-sm.png') }}" alt="" height="22">
            </span>
            <span class="logo-lg">
                <h3 style="line-height: 65px; color: white; font-family: 'IBM Plex Sans', sans-serif;">SIMK</h3>
            </span>
        </a>
        <!-- Light Logo-->
        <a href="{{ route('admin.dashboard') }}" class="logo logo-light">
            <span class="logo-sm">
                <img src="{{ asset('assets/images/logo-dark.png') }}" alt="" height="22">
            </span>
            <span class="logo-lg">
                <h3 style="line-height: 65px; color: white; font-family: 'IBM Plex Sans', sans-serif;">SIMK</h3>
            </span>
        </a>
        <button type="button" class="btn btn-sm fs-20 header-item float-end btn-vertical-sm-hover p-0" id="vertical-hover">
            <i class="ri-record-circle-line"></i>
        </button>
    </div>

    <div id="scrollbar">
        <div class="container-fluid">
            <div id="two-column-menu">
            </div>
            <ul class="navbar-nav" id="navbar-nav">
                <li class="menu-title"><span data-key="t-menu">Menu</span></li>

                @if (auth()->user()->role_id == 1)
                    <x-nav-link :href="route('admin.dashboard')" :active="Request::routeIs('admin.dashboard')" icon="las la-tachometer-alt">Dashboard</x-nav-link>

                    <x-nav-link dropdown="newsMenu" icon="las la-book-reader" :active="Request::routeIs('admin.categories.index') || Request::routeIs('admin.news.index') || Request::routeIs('admin.news.create') || Request::routeIs('admin.news.edit')">
                        Informasi
                        <x-slot name="content">
                            <x-dropdown id="newsMenu">
                                <x-nav-link :href="route('admin.news.index')">Informasi</x-nav-link>
                                <x-nav-link :href="route('admin.categories.index')">Kategori</x-nav-link>
                            </x-dropdown>
                        </x-slot>
                    </x-nav-link>

                    <x-nav-link dropdown="penggunaMenu" icon="lar la-user-circle" :active="Request::routeIs('admin.roles.index') || Request::routeIs('admin.users.index') || Request::routeIs('admin.users.create') || Request::routeIs('admin.users.edit')">
                        Users
                        <x-slot name="content">
                            <x-dropdown id="penggunaMenu" :active="Request::routeIs('admin.roles.index') || Request::routeIs('admin.users.index') || Request::routeIs('admin.users.create') || Request::routeIs('admin.users.edit')">
                                <x-nav-link :href="route('admin.roles.index')" :active="Request::routeIs('admin.roles.index')">Role</x-nav-link>
                                <x-nav-link :href="route('admin.users.index')" :active="Request::routeIs('admin.users.index') || Request::routeIs('admin.users.create') || Request::routeIs('admin.users.edit')">Pengguna</x-nav-link>
                            </x-dropdown>
                        </x-slot>
                    </x-nav-link>
                @elseif(auth()->user()->role_id == 2)
                @elseif(auth()->user()->role_id == 3)
                @endif
            </ul>
        </div>
        <!-- Sidebar -->
    </div>

    <div class="sidebar-background"></div>
</div>
