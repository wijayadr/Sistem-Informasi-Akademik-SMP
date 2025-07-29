<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-layout="horizontal" data-layout-style="" data-layout-position="fixed" data-topbar="light">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta content="Sistem Informasi Maintenance Kendaraan" name="description" />
        <meta content="SIMK" name="author" />

        <title>{{ $title ?? config('app.name') }}</title>

        <!-- App favicon -->
        <link rel="shortcut icon" href="{{ asset('assets/images/favicon.ico') }}"">
        <!-- Sweet Alert css-->
        <link href="{{ asset('assets/libs/sweetalert2/sweetalert2.min.css') }}" rel="stylesheet" type="text/css" />
        <!-- Layout config Js -->
        <script src="{{ asset('assets/js/layout.js') }}""></script>
        <!-- Bootstrap Css -->
        <link href="{{ asset('assets/css/bootstrap.min.css') }}" rel="stylesheet" type="text/css" />
        <!-- Icons Css -->
        <link href="{{ asset('assets/css/icons.min.css') }}" rel="stylesheet" type="text/css" />
        <!-- App Css-->
        <link href="{{ asset('assets/css/app.min.css') }}" rel="stylesheet" type="text/css" />
        <!-- custom Css-->
        <link href="{{ asset('assets/css/custom.css') }}" rel="stylesheet" type="text/css" />

        @livewireStyles
        @stack('style')
    </head>
    <body>
        {{ $slot }}

        <!-- JAVASCRIPT -->
        <script src="{{ asset('assets/libs/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
        <script src="{{ asset('assets/libs/simplebar/simplebar.min.js') }}"></script>
        <script src="{{ asset('assets/libs/node-waves/waves.min.js') }}"></script>
        <script src="{{ asset('assets/libs/feather-icons/feather.min.js') }}"></script>
        <script src="{{ asset('assets/js/pages/plugins/lord-icon-2.1.0.js') }}"></script>
        {{-- <script src="{{ asset('assets/js/plugins.js') }}"></script> --}}
        <script src="{{ asset('assets/libs/sweetalert2/sweetalert2.min.js') }}"></script>

        <!-- particles js -->
        {{-- <script src="{{ asset('assets/libs/particles.js') }}/particles.js') }}"></script> --}}
        <!-- particles app js -->
        {{-- <script src="{{ asset('assets/js/pages/particles.app.js') }}"></script> --}}
        <!-- password-addon init -->
        <script src="{{ asset('assets/js/pages/password-addon.init.js') }}"></script>

        @livewireScripts
        @stack('script')
    </body>
</html>
