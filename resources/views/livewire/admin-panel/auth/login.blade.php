<!-- auth-page wrapper -->
<div class="auth-page-wrapper auth-bg-cover py-5 d-flex justify-content-center align-items-center min-vh-100">
    <div class="bg-overlay"></div>
    <!-- auth-page content -->
    <div class="auth-page-content overflow-hidden pt-lg-5">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="card overflow-hidden">
                        <div class="row g-0">
                            <div class="col-lg-6">
                                <div class="p-lg-5 p-4 auth-one-bg h-100">
                                    <div class="bg-overlay"></div>
                                    <div class="position-relative h-100 d-flex flex-column">
                                        <div class="mt-auto">
                                            <div id="qoutescarouselIndicators" class="carousel slide" data-bs-ride="carousel">
                                                <div class="carousel-indicators">
                                                    <button type="button" data-bs-target="#qoutescarouselIndicators" data-bs-slide-to="0" class="active" aria-current="true" aria-label="Slide 1"></button>
                                                </div>
                                                <div class="carousel-inner text-center text-white pb-5">
                                                    <div class="carousel-item active">
                                                        <p class="fs-15 fst-uppercase">"SISTEM INFORMASI MAINTENANCE KENDARAAN"</p>
                                                    </div>
                                                </div>
                                            </div>
                                            <!-- end carousel -->
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- end col -->

                            <div class="col-lg-6">
                                <div class="p-lg-5 p-4">
                                    <div>
                                        <h5 class="text-primary">Selamat Datang!</h5>
                                        <p class="text-muted">Silahkan masuk ke Sistem Informasi Maintenance Kendaraan</p>
                                    </div>

                                    <div class="mt-4">
                                        <form wire:submit.prevent="login">
                                            @csrf

                                            <div class="mb-3">
                                                <x-input-label for="username" value="Username" required />
                                                <x-text-input wire:model="username" type="text" id="username" placeholder="Username" :error="$errors->get('username')" />
                                                <x-input-error :messages="$errors->get('username')"/>
                                            </div>

                                            <div class="mb-3">
                                                <x-input-label for="password" value="Password" required/>
                                                <div class="position-relative auth-pass-inputgroup mb-3">
                                                    <x-text-input wire:model="password" type="password" class="pe-5 password-input"  placeholder="Password" id="password-input" :error="$errors->get('password')"/>
                                                    <button class="btn btn-link position-absolute end-0 top-0 text-decoration-none text-muted password-addon" type="button" id="password-addon"><i class="ri-eye-fill align-middle"></i></button>
                                                    <x-input-error :messages="$errors->get('password')"/>
                                                </div>
                                            </div>

                                            <div class="mt-4">
                                                <x-primary-button class="w-100" type="submit">
                                                    Login
                                                </x-primary-button>
                                            </div>
                                        </form>
                                    </div>

                                    <div class="mt-5 text-center">
                                        <p class="mb-0">Silahkan hubungi admin untuk melakukan pendaftaran jika belum memiliki akun </p>
                                    </div>
                                </div>
                            </div>
                            <!-- end col -->
                        </div>
                        <!-- end row -->
                    </div>
                    <!-- end card -->
                </div>
                <!-- end col -->

            </div>
            <!-- end row -->
        </div>
        <!-- end container -->
    </div>
    <!-- end auth page content -->

    <!-- footer -->
    <footer class="footer start-0">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="text-center">
                        <p class="mb-0">&copy;
                            <script>document.write(new Date().getFullYear())</script> SIMK. Crafted with <i class="mdi mdi-heart text-danger"></i> by SIMK Team
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </footer>
    <!-- end Footer -->
</div>
<!-- end auth-page-wrapper -->
