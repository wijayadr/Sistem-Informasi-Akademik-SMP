<div>
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <form wire:submit.prevent="{{ $editing ? 'edit' : 'save' }}" class="tablelist-form" autocomplete="off">
                        <div class="row g-3">
                            <div class="col-lg-12">
                                <div class="text-center">
                                    <div class="position-relative d-inline-block">
                                        <div class="position-absolute end-0 bottom-0">
                                            <label for="avatar-input" class="mb-0" data-bs-toggle="tooltip" data-bs-placement="right" title="Pilih Avatar">
                                                <div class="avatar-xs cursor-pointer">
                                                    <div class="avatar-title bg-light rounded-circle text-muted border">
                                                        <i class="ri-image-fill"></i>
                                                    </div>
                                                </div>
                                            </label>
                                            <input wire:ignore wire:model="form.avatar" class="form-control d-none" value="" id="avatar-input" type="file" accept="image/png, image/gif, image/jpeg">
                                        </div>
                                        <div class="avatar-lg p-1">
                                            <div class="avatar-title bg-light rounded-circle">
                                                <img wire:ignore src="{{ asset('assets/images/users/user-dummy-img.jpg') }}" id="avatar-img" class="avatar-md rounded-circle object-cover" />
                                            </div>
                                        </div>
                                    </div>
                                    <h5 class="fs-13 mt-3">Foto Profile</h5>
                                    <div wire:loading wire:target="form.avatar">Uploading...</div>
                                    <x-input-error :messages="$errors->get('form.avatar')" />
                                </div>
                            </div>

                            <div class="col-lg-12">
                                <x-input-label for="role" value="Role" required/>
                                <x-select-list class="w-full" id="role_id" name="role_id" :options="$this->listsForFields['roles']" wire:model="form.role_id" data-placeholder="-- Pilih --"/>
                                <x-input-error :messages="$errors->get('form.role_id')"/>
                            </div>

                            <div class="col-lg-12">
                                <x-input-label for="username" value="Pengguna" required/>
                                <x-text-input wire:model="form.username" type="text" id="username" placeholder="Username" :error="$errors->get('form.username')" />
                                <x-input-error :messages="$errors->get('form.username')"/>
                            </div>

                            <div class="col-lg-12">
                                <x-input-label for="name" value="Nama Lengkap" required/>
                                <x-text-input wire:model="form.name" type="text" id="name" placeholder="Nama Lengkap" :error="$errors->get('form.name')" />
                                <x-input-error :messages="$errors->get('form.name')"/>
                            </div>

                            <div class="col-lg-12">
                                <x-input-label for="email" value="Email" required/>
                                <x-text-input wire:model="form.email" type="email" id="email" placeholder="Email" :error="$errors->get('form.email')" />
                                <x-input-error :messages="$errors->get('form.email')"/>
                            </div>

                            <div class="col-lg-12">
                                <x-input-label for="password" value="Password" required/>
                                <x-text-input wire:model="form.password" type="password" id="password" placeholder="Password" :error="$errors->get('form.password')" />
                                <x-input-error :messages="$errors->get('form.password')"/>
                            </div>

                            <div class="col-lg-12">
                                <div class="text-end">
                                    <x-primary-button type="submit">
                                        Simpan
                                    </x-primary-button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
</div>

@push('script')
    <script>
        document.addEventListener("livewire:init", () => {
            $('#avatar-input').change(function(e) {
                let reader = new FileReader();
                reader.onload = (e) => {
                    $('#avatar-img').attr('src', e.target.result);
                }
                reader.readAsDataURL(this.files[0]);
            });

            Livewire.on('recentAvatar', (avatar) => {
                let recent_avatar = "{{ asset('storage/images/users/' . $form->avatar) }}";
                if (recent_avatar) {
                    $('#avatar-img').attr('src', recent_avatar);
                }
            });
        })
    </script>
@endpush
