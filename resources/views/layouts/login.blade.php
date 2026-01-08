<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-layout="horizontal" data-layout-style="" data-layout-position="fixed" data-topbar="light">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta content="Sistem Informasi Maintenance Kendaraan" name="description" />
        <meta content="SIAKAD" name="author" />

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

        <style>
            :root {
                --bs-primary: #3b82f6;
                --bs-primary-rgb: 59, 130, 246;
                --bs-success: #10b981;
                --bs-danger: #ef4444;
                --bs-info: #06b6d4;
            }

            body {
                font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                min-height: 100vh;
                overflow-x: hidden;
            }

            .auth-page-wrapper {
                position: relative;
                min-height: 100vh;
                display: flex;
                align-items: center;
            }

            .auth-bg-particles {
                position: absolute;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background:
                    radial-gradient(circle at 20% 50%, rgba(120, 119, 198, 0.3) 0%, transparent 50%),
                    radial-gradient(circle at 80% 20%, rgba(255, 118, 117, 0.3) 0%, transparent 50%),
                    radial-gradient(circle at 40% 80%, rgba(59, 130, 246, 0.3) 0%, transparent 50%);
                animation: float 6s ease-in-out infinite;
            }

            @keyframes float {
                0%, 100% { transform: translateY(0px); }
                50% { transform: translateY(-20px); }
            }

            .floating-shapes {
                position: absolute;
                width: 100%;
                height: 100%;
                overflow: hidden;
                z-index: 1;
            }

            .shape {
                position: absolute;
                border-radius: 50%;
                background: rgba(255, 255, 255, 0.1);
                animation: float-shapes 20s infinite linear;
            }

            .shape:nth-child(1) {
                width: 80px;
                height: 80px;
                top: 20%;
                left: 10%;
                animation-delay: 0s;
            }

            .shape:nth-child(2) {
                width: 120px;
                height: 120px;
                top: 60%;
                right: 10%;
                animation-delay: -5s;
            }

            .shape:nth-child(3) {
                width: 60px;
                height: 60px;
                bottom: 20%;
                left: 20%;
                animation-delay: -10s;
            }

            @keyframes float-shapes {
                0% { transform: translateY(0px) rotate(0deg); }
                33% { transform: translateY(-30px) rotate(120deg); }
                66% { transform: translateY(30px) rotate(240deg); }
                100% { transform: translateY(0px) rotate(360deg); }
            }

            .auth-card {
                backdrop-filter: blur(20px);
                background: rgba(255, 255, 255, 0.95);
                border: 1px solid rgba(255, 255, 255, 0.2);
                border-radius: 20px;
                box-shadow:
                    0 25px 50px -12px rgba(0, 0, 0, 0.25),
                    0 0 0 1px rgba(255, 255, 255, 0.05);
                position: relative;
                z-index: 10;
                overflow: hidden;
                transition: transform 0.3s ease, box-shadow 0.3s ease;
            }

            .auth-card:hover {
                transform: translateY(-5px);
                box-shadow:
                    0 35px 60px -12px rgba(0, 0, 0, 0.3),
                    0 0 0 1px rgba(255, 255, 255, 0.1);
            }

            .auth-card::before {
                content: '';
                position: absolute;
                top: 0;
                left: 0;
                right: 0;
                height: 4px;
                background: linear-gradient(90deg, var(--bs-primary), var(--bs-info), var(--bs-success));
            }

            .auth-logo {
                display: inline-flex;
                align-items: center;
                padding: 12px 24px;
                background: rgba(59, 130, 246, 0.1);
                border-radius: 12px;
                text-decoration: none;
                transition: all 0.3s ease;
            }

            .auth-logo:hover {
                transform: scale(1.05);
                background: rgba(59, 130, 246, 0.2);
            }

            .logo-icon {
                width: 32px;
                height: 32px;
                background: linear-gradient(135deg, var(--bs-primary), var(--bs-info));
                border-radius: 8px;
                display: flex;
                align-items: center;
                justify-content: center;
                color: white;
                font-size: 18px;
                margin-right: 10px;
            }

            .form-control {
                border: 2px solid rgba(0, 0, 0, 0.1);
                border-radius: 12px;
                padding: 14px 16px;
                font-size: 16px;
                transition: all 0.3s ease;
                background: rgba(255, 255, 255, 0.8);
            }

            .form-control:focus {
                border-color: var(--bs-primary);
                box-shadow: 0 0 0 0.2rem rgba(59, 130, 246, 0.1);
                background: rgba(255, 255, 255, 1);
            }

            .btn-primary {
                background: linear-gradient(135deg, var(--bs-primary) 0%, #6366f1 100%);
                border: none;
                border-radius: 12px;
                padding: 14px 24px;
                font-weight: 600;
                font-size: 16px;
                transition: all 0.3s ease;
                position: relative;
                overflow: hidden;
            }

            .btn-primary:hover {
                transform: translateY(-2px);
                box-shadow: 0 10px 20px rgba(59, 130, 246, 0.4);
            }

            .btn-primary::before {
                content: '';
                position: absolute;
                top: 0;
                left: -100%;
                width: 100%;
                height: 100%;
                background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent);
                transition: left 0.6s;
            }

            .btn-primary:hover::before {
                left: 100%;
            }

            .social-btn {
                width: 50px;
                height: 50px;
                border-radius: 12px;
                border: 2px solid rgba(0, 0, 0, 0.1);
                display: inline-flex;
                align-items: center;
                justify-content: center;
                margin: 0 8px;
                transition: all 0.3s ease;
                background: white;
            }

            .social-btn:hover {
                transform: translateY(-3px);
                border-color: transparent;
            }

            .social-btn.facebook:hover {
                background: #1877f2;
                color: white;
                box-shadow: 0 8px 20px rgba(24, 119, 242, 0.4);
            }

            .social-btn.google:hover {
                background: #ea4335;
                color: white;
                box-shadow: 0 8px 20px rgba(234, 67, 53, 0.4);
            }

            .social-btn.github:hover {
                background: #333;
                color: white;
                box-shadow: 0 8px 20px rgba(51, 51, 51, 0.4);
            }

            .social-btn.twitter:hover {
                background: #1da1f2;
                color: white;
                box-shadow: 0 8px 20px rgba(29, 161, 242, 0.4);
            }

            .password-addon {
                border: none;
                background: transparent;
                color: #6b7280;
                transition: color 0.3s ease;
            }

            .password-addon:hover {
                color: var(--bs-primary);
            }

            .welcome-text {
                background: linear-gradient(135deg, #333, #666);
                background-clip: text;
                -webkit-background-clip: text;
                -webkit-text-fill-color: transparent;
                font-weight: 700;
                margin-bottom: 8px;
            }

            .divider {
                position: relative;
                text-align: center;
                margin: 32px 0;
            }

            .divider::before {
                content: '';
                position: absolute;
                top: 50%;
                left: 0;
                right: 0;
                height: 1px;
                background: linear-gradient(90deg, transparent, rgba(0, 0, 0, 0.1), transparent);
            }

            .divider span {
                background: white;
                padding: 0 20px;
                color: #6b7280;
                font-size: 14px;
                font-weight: 500;
            }

            .footer-link {
                color: rgba(255, 255, 255, 0.8);
                text-decoration: none;
                transition: color 0.3s ease;
            }

            .footer-link:hover {
                color: rgba(255, 255, 255, 1);
            }

            @media (max-width: 768px) {
                .auth-card {
                    margin: 20px;
                    border-radius: 16px;
                }

                .social-btn {
                    width: 45px;
                    height: 45px;
                    margin: 0 4px;
                }
            }

            .animate-in {
                animation: slideInUp 0.8s ease-out;
            }

            @keyframes slideInUp {
                from {
                    opacity: 0;
                    transform: translateY(30px);
                }
                to {
                    opacity: 1;
                    transform: translateY(0);
                }
            }
        </style>

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
