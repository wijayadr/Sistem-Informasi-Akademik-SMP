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

                {{-- ADMIN MENU --}}
                @if (auth()->user()->role_id == 1)
                    <x-nav-link href="#" icon="las la-tachometer-alt">Dashboard</x-nav-link>

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

                    <x-nav-link dropdown="absensiMenu" icon="las la-calendar-check">
                        Pengelolaan Absensi
                        <x-slot name="content">
                            <x-dropdown id="absensiMenu">
                                <x-nav-link href="#">Absensi Harian</x-nav-link>
                                <x-nav-link href="#">Rekap Absensi Bulanan</x-nav-link>
                                <x-nav-link href="#">Laporan Kehadiran</x-nav-link>
                            </x-dropdown>
                        </x-slot>
                    </x-nav-link>

                    <x-nav-link dropdown="nilaiMenu" icon="las la-clipboard-list">
                        Pengelolaan Nilai
                        <x-slot name="content">
                            <x-dropdown id="nilaiMenu">
                                <x-nav-link href="#">Input Nilai</x-nav-link>
                                <x-nav-link href="#">Validasi Nilai</x-nav-link>
                                <x-nav-link href="#">Rapor Siswa</x-nav-link>
                            </x-dropdown>
                        </x-slot>
                    </x-nav-link>

                    <x-nav-link dropdown="laporanAdminMenu" icon="las la-file-alt">
                        Laporan & Cetak
                        <x-slot name="content">
                            <x-dropdown id="laporanAdminMenu">
                                <x-nav-link href="#">Laporan Siswa (Student Reports)</x-nav-link>
                                <x-nav-link href="#">Cetak Rapor (Report Cards)</x-nav-link>
                                <x-nav-link href="#">Rekap Absensi Bulanan</x-nav-link>
                                <x-nav-link href="#">Laporan Kinerja Akademik</x-nav-link>
                            </x-dropdown>
                        </x-slot>
                    </x-nav-link>

                    <x-nav-link dropdown="sistemMenu" icon="las la-cogs">
                        Pengaturan Sistem
                        <x-slot name="content">
                            <x-dropdown id="sistemMenu">
                                <x-nav-link href="#">Backup Data</x-nav-link>
                                <x-nav-link href="#">Restore Data</x-nav-link>
                                <x-nav-link href="#">Audit Log</x-nav-link>
                                <x-nav-link href="#">Pengaturan Aplikasi</x-nav-link>
                            </x-dropdown>
                        </x-slot>
                    </x-nav-link>

                {{-- GURU MENU --}}
                @elseif(auth()->user()->role_id == 2)
                    <x-nav-link href="#" icon="las la-tachometer-alt">Dashboard</x-nav-link>

                    <x-nav-link href="{{ route('teacher.schedules.index') }}" icon="las la-chalkboard-teacher" :active="Request::routeIs('teacher.schedules.*')">Jadwal Mengajar</x-nav-link>

                    <x-nav-link href="{{ route('teacher.attendances.index') }}" icon="las la-user-check" :active="Request::routeIs('teacher.attendances.*')">Kehadiran</x-nav-link>

                    <x-nav-link href="{{ route('teacher.grades.index') }}" icon="las la-clipboard-list" :active="Request::routeIs('teacher.grades.*')">Kelola Nilai</x-nav-link>

                    {{-- Menu khusus untuk Wali Kelas --}}
                    @if(auth()->user()->teacher && auth()->user()->teacher->homeroomClasses->count() > 0)
                    <x-nav-link dropdown="waliKelasMenu" icon="las la-users">
                        Wali Kelas
                        <x-slot name="content">
                            <x-dropdown id="waliKelasMenu">
                                <x-nav-link href="#">Data Siswa Kelas</x-nav-link>
                                <x-nav-link href="#">Rekap Absensi Kelas</x-nav-link>
                                <x-nav-link href="#">Rekap Nilai Kelas</x-nav-link>
                                <x-nav-link href="#">Laporan Perkembangan</x-nav-link>
                            </x-dropdown>
                        </x-slot>
                    </x-nav-link>
                    @endif

                {{-- SISWA MENU --}}
                @elseif(auth()->user()->role_id == 3)
                    <x-nav-link href="#" icon="las la-tachometer-alt">Dashboard</x-nav-link>

                    <x-nav-link dropdown="jadwalSiswaMenu" icon="las la-calendar">
                        Jadwal & Pembelajaran
                        <x-slot name="content">
                            <x-dropdown id="jadwalSiswaMenu">
                                <x-nav-link href="#">Jadwal Pelajaran</x-nav-link>
                                <x-nav-link href="#">Jadwal Ujian</x-nav-link>
                                <x-nav-link href="#">Mata Pelajaran</x-nav-link>
                            </x-dropdown>
                        </x-slot>
                    </x-nav-link>

                    <x-nav-link dropdown="nilaiSiswaMenu" icon="las la-chart-line">
                        Nilai & Prestasi
                        <x-slot name="content">
                            <x-dropdown id="nilaiSiswaMenu">
                                <x-nav-link href="#">Lihat Nilai</x-nav-link>
                                <x-nav-link href="#">Rekap Nilai</x-nav-link>
                                <x-nav-link href="#">Rapor Online</x-nav-link>
                                <x-nav-link href="#">Ranking Kelas</x-nav-link>
                            </x-dropdown>
                        </x-slot>
                    </x-nav-link>

                    <x-nav-link dropdown="kehadiranSiswaMenu" icon="las la-user-clock">
                        Kehadiran
                        <x-slot name="content">
                            <x-dropdown id="kehadiranSiswaMenu">
                                <x-nav-link href="#">Lihat Kehadiran</x-nav-link>
                                <x-nav-link href="#">Rekap Kehadiran</x-nav-link>
                                <x-nav-link href="#">History Absensi</x-nav-link>
                            </x-dropdown>
                        </x-slot>
                    </x-nav-link>

                    <x-nav-link dropdown="profilSiswaMenu" icon="las la-user">
                        Profil & Data
                        <x-slot name="content">
                            <x-dropdown id="profilSiswaMenu">
                                <x-nav-link href="#">Profil Siswa</x-nav-link>
                                <x-nav-link href="#">Data Kelas</x-nav-link>
                                <x-nav-link href="#">Biodata</x-nav-link>
                            </x-dropdown>
                        </x-slot>
                    </x-nav-link>

                {{-- ORANG TUA MENU --}}
                @elseif(auth()->user()->role_id == 4)
                    <x-nav-link href="#" icon="las la-tachometer-alt">Dashboard</x-nav-link>

                    <x-nav-link dropdown="anakMenu" icon="las la-child">
                        Data Anak
                        <x-slot name="content">
                            <x-dropdown id="anakMenu">
                                <x-nav-link href="#">Profil Anak</x-nav-link>
                                <x-nav-link href="#">Data Kelas</x-nav-link>
                                <x-nav-link href="#">Jadwal Pelajaran</x-nav-link>
                            </x-dropdown>
                        </x-slot>
                    </x-nav-link>

                    <x-nav-link dropdown="nilaiAnakMenu" icon="las la-graduation-cap">
                        Nilai Anak
                        <x-slot name="content">
                            <x-dropdown id="nilaiAnakMenu">
                                <x-nav-link href="#">Lihat Nilai Anak</x-nav-link>
                                <x-nav-link href="#">Perkembangan Nilai</x-nav-link>
                                <x-nav-link href="#">Rapor Online</x-nav-link>
                                <x-nav-link href="#">Ranking Kelas</x-nav-link>
                            </x-dropdown>
                        </x-slot>
                    </x-nav-link>

                    <x-nav-link dropdown="kehadiranAnakMenu" icon="las la-calendar-check">
                        Kehadiran Anak
                        <x-slot name="content">
                            <x-dropdown id="kehadiranAnakMenu">
                                <x-nav-link href="#">Lihat Kehadiran Anak</x-nav-link>
                                <x-nav-link href="#">Rekap Kehadiran</x-nav-link>
                                <x-nav-link href="#">Notifikasi Absensi</x-nav-link>
                            </x-dropdown>
                        </x-slot>
                    </x-nav-link>

                    <x-nav-link dropdown="laporanAnakMenu" icon="las la-file-alt">
                        Laporan Perkembangan
                        <x-slot name="content">
                            <x-dropdown id="laporanAnakMenu">
                                <x-nav-link href="#">Laporan Bulanan</x-nav-link>
                                <x-nav-link href="#">Laporan Semester</x-nav-link>
                                <x-nav-link href="#">Catatan Guru</x-nav-link>
                                <x-nav-link href="#">Progress Akademik</x-nav-link>
                            </x-dropdown>
                        </x-slot>
                    </x-nav-link>

                    <x-nav-link dropdown="komunikasiMenu" icon="las la-comments">
                        Komunikasi
                        <x-slot name="content">
                            <x-dropdown id="komunikasiMenu">
                                <x-nav-link href="#">Pesan dari Guru</x-nav-link>
                                <x-nav-link href="#">Pesan dari Sekolah</x-nav-link>
                                <x-nav-link href="#">Konsultasi Online</x-nav-link>
                            </x-dropdown>
                        </x-slot>
                    </x-nav-link>
                @endif
            </ul>
        </div>
        <!-- Sidebar -->
    </div>

    <div class="sidebar-background"></div>
</div>
