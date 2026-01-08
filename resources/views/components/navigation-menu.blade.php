<div class="app-menu navbar-menu">
    <!-- LOGO -->
    <div class="navbar-brand-box">
        <!-- Dark Logo-->
        <a href="{{ route('admin.dashboard') }}" class="logo logo-dark">
            <span class="logo-sm">
                <img src="{{ asset('assets/images/logo-sm.png') }}" alt="" height="22">
            </span>
            <span class="logo-lg">
                <h3 style="line-height: 65px; font-family: 'IBM Plex Sans', sans-serif;">SIAKAD</h3>
            </span>
        </a>
        <!-- Light Logo-->
        <a href="{{ route('admin.dashboard') }}" class="logo logo-light">
            <span class="logo-sm">
                <img src="{{ asset('assets/images/logo-dark.png') }}" alt="" height="22">
            </span>
            <span class="logo-lg">
                <h3 style="line-height: 65px; color: white; font-family: 'IBM Plex Sans', sans-serif;">SIAKAD</h3>
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

                {{-- ADMIN MENU --}}
                @if (auth()->user()->role_id == 1)
                    <x-nav-link href="{{ route('admin.dashboard') }}" icon="las la-tachometer-alt" :active="Request::routeIs('admin.dashboard')">Dashboard</x-nav-link>

                    <x-nav-link dropdown="masterDataMenu" icon="las la-database" :active="Request::routeIs('admin.academic-years.*') || Request::routeIs('admin.subjects.*') || Request::routeIs('admin.grade-components.*')">
                        Master Data
                        <x-slot name="content">
                            <x-dropdown id="masterDataMenu" :active="Request::routeIs('admin.academic-years.*') || Request::routeIs('admin.subjects.*') || Request::routeIs('admin.grade-components.*')">
                                <x-nav-link href="{{ route('admin.academic-years.index') }}" :active="Request::routeIs('admin.academic-years.*')">Tahun Akademik</x-nav-link>
                                <x-nav-link href="{{ route('admin.subjects.index') }}" :active="Request::routeIs('admin.subjects.*')">Mata Pelajaran</x-nav-link>
                                <x-nav-link href="{{ route('admin.grade-components.index') }}" :active="Request::routeIs('admin.grade-components.*')">Komponen Nilai</x-nav-link>
                            </x-dropdown>
                        </x-slot>
                    </x-nav-link>

                    <x-nav-link dropdown="penggunaMenu" icon="lar la-user-circle" :active="Request::routeIs('admin.teachers.*') || Request::routeIs('admin.students.*')">
                        Pengelolaan Pengguna
                        <x-slot name="content">
                            <x-dropdown id="penggunaMenu" :active="Request::routeIs('admin.teachers.*') || Request::routeIs('admin.students.*')">
                                <x-nav-link href="{{ route('admin.teachers.index') }}" :active="Request::routeIs('admin.teachers.*')">Data Guru</x-nav-link>
                                <x-nav-link href="{{ route('admin.students.index') }}" :active="Request::routeIs('admin.students.*')">Data Siswa</x-nav-link>
                            </x-dropdown>
                        </x-slot>
                    </x-nav-link>

                    <x-nav-link href="{{ route('admin.classes.index') }}" icon="las la-graduation-cap" :active="Request::routeIs('admin.classes.*')">Pengelolaan Akademik</x-nav-link>

                    <x-nav-link href="{{ route('admin.attendances.index') }}" icon="las la-calendar-check" :active="Request::routeIs('admin.attendances.*')">Absensi</x-nav-link>

                    <x-nav-link href="{{ route('admin.grades.index') }}" icon="las la-clipboard-list" :active="Request::routeIs('admin.grades.*')">Nilai</x-nav-link>
                {{-- GURU MENU --}}
                @elseif(auth()->user()->role_id == 2)
                    <x-nav-link href="{{ route('teacher.dashboard.index') }}" icon="las la-tachometer-alt" :active="Request::routeIs('teacher.dashboard.*')">Dashboard</x-nav-link>

                    <x-nav-link href="{{ route('teacher.schedules.index') }}" icon="las la-chalkboard-teacher" :active="Request::routeIs('teacher.schedules.*')">Jadwal Mengajar</x-nav-link>

                    <x-nav-link href="{{ route('teacher.attendances.index') }}" icon="las la-user-check" :active="Request::routeIs('teacher.attendances.*')">Kehadiran</x-nav-link>

                    <x-nav-link href="{{ route('teacher.grades.index') }}" icon="las la-clipboard-list" :active="Request::routeIs('teacher.grades.*')">Kelola Nilai</x-nav-link>

                    {{-- Menu khusus untuk Wali Kelas --}}
                    @if(auth()->user()->teacher && auth()->user()->teacher->homeroomClasses->count() > 0)
                    {{-- <x-nav-link dropdown="waliKelasMenu" icon="las la-users">
                        Wali Kelas
                        <x-slot name="content">
                            <x-dropdown id="waliKelasMenu">
                                <x-nav-link href="#">Data Siswa Kelas</x-nav-link>
                                <x-nav-link href="#">Rekap Absensi Kelas</x-nav-link>
                                <x-nav-link href="#">Rekap Nilai Kelas</x-nav-link>
                                <x-nav-link href="#">Laporan Perkembangan</x-nav-link>
                            </x-dropdown>
                        </x-slot>
                    </x-nav-link> --}}
                    @endif

                {{-- SISWA MENU --}}
                @elseif(auth()->user()->role_id == 3)
                    <x-nav-link href="{{ route('student.dashboard.index') }}" icon="las la-tachometer-alt" :active="Request::routeIs('student.dashboard.*')">Dashboard</x-nav-link>

                    <x-nav-link href="{{ route('student.schedules.index') }}" icon="las la-chalkboard-teacher" :active="Request::routeIs('student.schedules.*')">Jadwal Belajar</x-nav-link>

                    <x-nav-link href="{{ route('student.attendances.index') }}" icon="las la-user-check" :active="Request::routeIs('student.attendances.*')">Kehadiran</x-nav-link>

                    <x-nav-link href="{{ route('student.grades.index') }}" icon="las la-clipboard-list" :active="Request::routeIs('student.grades.*')">Nilai</x-nav-link>

                {{-- ORANG TUA MENU --}}
                @elseif(auth()->user()->role_id == 4)
                    <x-nav-link href="{{ route('parent.dashboard.index') }}" icon="las la-tachometer-alt" :active="Request::routeIs('parent.dashboard.*')">Dashboard</x-nav-link>

                    <x-nav-link href="{{ route('parent.attendances.index') }}" icon="las la-user-check" :active="Request::routeIs('parent.attendances.*')">Kehadiran Anak</x-nav-link>

                    <x-nav-link href="{{ route('parent.grades.index') }}" icon="las la-clipboard-list" :active="Request::routeIs('parent.grades.*')">Nilai Anak</x-nav-link>
                @endif
            </ul>
        </div>
        <!-- Sidebar -->
    </div>

    <div class="sidebar-background"></div>
</div>
