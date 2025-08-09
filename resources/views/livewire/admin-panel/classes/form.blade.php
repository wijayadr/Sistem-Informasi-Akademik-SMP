<div>
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <form wire:submit.prevent="{{ $editing ? 'edit' : 'save' }}" class="tablelist-form" autocomplete="off">
                        <div class="row g-3">
                            <div class="col-lg-6">
                                <x-input-label for="class_name" value="Nama Kelas" required/>
                                <x-text-input wire:model="form.class_name" type="text" id="class_name" placeholder="Nama Kelas" :error="$errors->get('form.class_name')" />
                                <x-input-error :messages="$errors->get('form.class_name')"/>
                            </div>

                            <div class="col-lg-6">
                                <x-input-label for="grade_level" value="Tingkat Kelas" required/>
                                <x-select-list class="w-full" id="grade_level" name="grade_level" :options="$this->listsForFields['grade_levels']" wire:model="form.grade_level" data-placeholder="-- Pilih Tingkat Kelas --"/>
                                <x-input-error :messages="$errors->get('form.grade_level')"/>
                            </div>

                            <div class="col-lg-6">
                                <x-input-label for="academic_year_id" value="Tahun Ajaran" required/>
                                <x-select-list class="w-full" id="academic_year_id" name="academic_year_id" :options="$this->listsForFields['academic_years']" wire:model="form.academic_year_id" data-placeholder="-- Pilih Tahun Ajaran --"/>
                                <x-input-error :messages="$errors->get('form.academic_year_id')"/>
                            </div>

                            <div class="col-lg-6">
                                <x-input-label for="homeroom_teacher_id" value="Wali Kelas"/>
                                <x-select-list class="w-full" id="homeroom_teacher_id" name="homeroom_teacher_id" :options="$this->listsForFields['teachers']" wire:model="form.homeroom_teacher_id" data-placeholder="-- Pilih Wali Kelas --"/>
                                <x-input-error :messages="$errors->get('form.homeroom_teacher_id')"/>
                            </div>

                            <div class="col-lg-6">
                                <x-input-label for="capacity" value="Kapasitas" required/>
                                <x-text-input wire:model="form.capacity" type="number" id="capacity" placeholder="Kapasitas kelas" :error="$errors->get('form.capacity')" min="1" />
                                <x-input-error :messages="$errors->get('form.capacity')"/>
                            </div>

                            <div class="col-lg-6">
                                <x-input-label for="status" value="Status" required/>
                                <x-select-list class="w-full" id="status" name="status" :options="$this->listsForFields['statuses']" wire:model="form.status" data-placeholder="-- Pilih Status --"/>
                                <x-input-error :messages="$errors->get('form.status')"/>
                            </div>

                            <div class="col-lg-12">
                                <div class="text-end">
                                    <a href="{{ route('admin.classes.index') }}" class="btn btn-secondary me-2">Batal</a>
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
</div>
