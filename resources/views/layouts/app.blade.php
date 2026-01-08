<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-layout="vertical" data-topbar="light" data-sidebar="light" data-sidebar-size="lg" data-sidebar-image="img-3" data-preloader="disable" data-layout-mode="light" data-layout-width="fluid" data-layout-position="fixed" data-layout-style="default">
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
        <!-- Select2-->
        <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
        <!-- Select2-->
        <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
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
        <div id="layout-wrapper">
            {{-- Topbar in here --}}
            <x-topbar />

            {{-- Navigation Bar in here --}}
            <x-navigation-menu />

            <div class="vertical-overlay"></div>

            <div class="main-content">
                <div class="page-content">
                    <div class="container-fluid">
                        <x-page-title :title="$title" />

                        {{ $slot }}
                    </div>
                </div>

                <x-footer />
            </div>
        </div>

        @stack('modal')

        <!-- JAVASCRIPT -->
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js" integrity="sha512-v2CJ7UaYy4JwqLDIrZUI/4hqeoQieOmAZNXBeQyjo21dadnwR+8ZaIJVT8EE2iyI61OV8e6M8PP2/4hpQINQ/g==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
        <script src="{{ asset('assets/libs/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
        <script src="{{ asset('assets/libs/simplebar/simplebar.min.js') }}"></script>
        <script src="{{ asset('assets/libs/node-waves/waves.min.js') }}"></script>
        <script src="{{ asset('assets/libs/feather-icons/feather.min.js') }}"></script>
        <script src="{{ asset('assets/js/pages/plugins/lord-icon-2.1.0.js') }}"></script>
        <script src="{{ asset('assets/js/plugins.js') }}"></script>
        <!-- Sweet Alerts js -->
        <script src="{{ asset('assets/libs/sweetalert2/sweetalert2.min.js') }}"></script>
        <!-- Sweet alert init js-->
        <script src="{{ asset('assets/js/pages/sweetalerts.init.js') }}"></script>
        <!-- Form Plugins -->
        <script src="{{ asset('assets/libs/choices.js/public/assets/scripts/choices.min.js') }}"></script>
        <script src="{{ asset('assets/libs/flatpickr/flatpickr.min.js') }}"></script>
        <!-- glightbox js -->
        <script src="{{ asset('assets/libs/glightbox/js/glightbox.min.js') }}"></script>
        <!--Swiper slider js-->
        <script src="{{ asset('assets/libs/swiper/swiper-bundle.min.js') }}"></script>
        <!-- App js -->
        {{-- <script src="{{ asset('assets/js/app.js') }}"></script> --}}
        <!-- Custom js -->
        <script src="{{ asset('assets/js/custom.js') }}"></script>
        <!-- Toastify -->
        <script src="{{ asset('assets/libs/toastify-js/src/toastify.js') }}"></script>
        <!-- Select2 -->
        <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

        <script>
            const ShowToastify = (type, message) => {
                Toastify({
                    text: message,
                    duration: 3000,
                    close: true,
                    gravity: "top",
                    position: "center",
                    style: {
                        background: type == 'success' ? "#4dbd74" : type == 'error' ? "#f1556c" : type == 'warning' ? "#f7b84b" : "#299cdb",
                    },
                    stopOnFocus: true,
                }).showToast();
            }

            document.addEventListener('livewire:initialized', () => {
                Livewire.on('show:toastify', data => {
                    ShowToastify(data.type, data.message);
                });

                Livewire.on('swal:confirm', ({title, text, icon, confirmButtonText, cancelButtonText, method, params, callback}) => {
                    Swal.fire({
                        title,
                        text,
                        icon,
                        showCancelButton: true,
                        confirmButtonClass: "btn btn-danger w-xs me-2 mb-1",
                        cancelButtonClass: "btn btn-soft-success w-xs mb-1",
                        confirmButtonText,
                        cancelButtonText,
                        buttonsStyling: false,
                        showCloseButton: false
                    }).then((result) => {
                        if (result.isConfirmed) {
                            Livewire.dispatch(method, { id: params });
                        }
                    });
                });

                Livewire.on('closeModal', () => {
                    $('.modal').modal('hide');
                });
            });

            @if (session()->has('success'))
                ShowToastify('success', '{{ session('success') }}');
            @endif
        </script>

        @livewireScripts
        @stack('script')
    </body>
</html>
