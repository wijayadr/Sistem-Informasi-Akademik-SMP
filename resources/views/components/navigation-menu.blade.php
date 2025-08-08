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

                    <x-nav-link dropdown="masterDataMenu" icon="las la-database">
                        Master Data
                        <x-slot name="content">
                            <x-dropdown id="masterDataMenu">
                                <x-nav-link href="#">Tahun Akademik</x-nav-link>
                                <x-nav-link href="#">Mata Pelajaran</x-nav-link>
                                <x-nav-link href="#">Komponen Penilaian</x-nav-link>
                            </x-dropdown>
                        </x-slot>
                    </x-nav-link>

                    <x-nav-link dropdown="penggunaMenu" icon="lar la-user-circle">
                        Pengelolaan Pengguna
                        <x-slot name="content">
                            <x-dropdown id="penggunaMenu">
                                <x-nav-link href="#">Role & Permission</x-nav-link>
                                <x-nav-link href="#">Data Admin</x-nav-link>
                                <x-nav-link href="#">Data Guru</x-nav-link>
                                <x-nav-link href="#">Data Siswa</x-nav-link>
                                <x-nav-link href="#">Data Orang Tua</x-nav-link>
                            </x-dropdown>
                        </x-slot>
                    </x-nav-link>

                    <x-nav-link dropdown="akademikMenu" icon="las la-graduation-cap">
                        Pengelolaan Akademik
                        <x-slot name="content">
                            <x-dropdown id="akademikMenu">
                                <x-nav-link href="#">Kelas per Tahun Akademik</x-nav-link>
                                <x-nav-link href="#">Pembagian Siswa ke Kelas</x-nav-link>
                                <x-nav-link href="#">Penugasan Guru-Mata Pelajaran</x-nav-link>
                                <x-nav-link href="#">Jadwal Pelajaran</x-nav-link>
                            </x-dropdown>
                        </x-slot>
                    </x-nav-link>

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

                    <x-nav-link dropdown="mengajarMenu" icon="las la-chalkboard-teacher">
                        Kegiatan Mengajar
                        <x-slot name="content">
                            <x-dropdown id="mengajarMenu">
                                <x-nav-link href="#">Jadwal Mengajar</x-nav-link>
                                <x-nav-link href="#">Daftar Kelas</x-nav-link>
                                <x-nav-link href="#">Mata Pelajaran</x-nav-link>
                            </x-dropdown>
                        </x-slot>
                    </x-nav-link>

                    <x-nav-link dropdown="absensiGuruMenu" icon="las la-user-check">
                        Absensi Siswa
                        <x-slot name="content">
                            <x-dropdown id="absensiGuruMenu">
                                <x-nav-link href="#">Input Kehadiran</x-nav-link>
                                <x-nav-link href="#">Rekap Kehadiran Kelas</x-nav-link>
                                <x-nav-link href="#">Laporan Kehadiran</x-nav-link>
                            </x-dropdown>
                        </x-slot>
                    </x-nav-link>

                    <x-nav-link dropdown="nilaiGuruMenu" icon="las la-clipboard-list">
                        Penilaian Siswa
                        <x-slot name="content">
                            <x-dropdown id="nilaiGuruMenu">
                                <x-nav-link href="#">Input Nilai Harian</x-nav-link>
                                <x-nav-link href="#">Input Nilai UTS</x-nav-link>
                                <x-nav-link href="#">Input Nilai UAS</x-nav-link>
                                <x-nav-link href="#">Input Nilai Tugas</x-nav-link>
                                <x-nav-link href="#">Rekap Nilai Siswa</x-nav-link>
                            </x-dropdown>
                        </x-slot>
                    </x-nav-link>

                    <x-nav-link dropdown="laporanGuruMenu" icon="las la-file-alt">
                        Laporan Kelas
                        <x-slot name="content">
                            <x-dropdown id="laporanGuruMenu">
                                <x-nav-link href="#">Laporan Nilai Kelas</x-nav-link>
                                <x-nav-link href="#">Laporan Kehadiran Kelas</x-nav-link>
                                <x-nav-link href="#">Progress Belajar Siswa</x-nav-link>
                            </x-dropdown>
                        </x-slot>
                    </x-nav-link>

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

                {{-- Menu umum untuk semua role --}}
                <li class="menu-title"><span data-key="t-account">Akun</span></li>
                <x-nav-link href="#" icon="las la-user-cog">Profil Saya</x-nav-link>
                <x-nav-link href="#" icon="las la-key">Ubah Password</x-nav-link>
                <x-nav-link href="#" icon="las la-sign-out-alt">Logout</x-nav-link>

            </ul>
        </div>
        <!-- Sidebar -->
    </div>

    <div class="sidebar-background"></div>
</div>
