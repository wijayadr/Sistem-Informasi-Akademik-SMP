@props(['error', 'disabled' => false])

@php
$classes = ($error ?? false)
            ? 'form-control is-invalid'
            : 'form-control';
@endphp

<input {{ $disabled ? 'disabled' : '' }} {!! $attributes->merge(['class' => $classes]) !!}>

@push('script')
    <script>
        document.addEventListener("livewire:initialized", () => {
            let el = $('#{{ $attributes['id'] }}')

            function initFlatpickr() {
                el.flatpickr({
                    altInput: true,
                    altFormat: "j F Y",
                    dateFormat: "Y-m-d",
                    locale: {
                        firstDayOfWeek: 1,
                        weekdays: {
                            shorthand: ["Min", "Sen", "Sel", "Rab", "Kam", "Jum", "Sab"],
                            longhand: [
                                "Minggu",
                                "Senin",
                                "Selasa",
                                "Rabu",
                                "Kamis",
                                "Jumat",
                                "Sabtu",
                            ],
                        },
                        months: {
                            shorthand: [
                                "Jan",
                                "Feb",
                                "Mar",
                                "Apr",
                                "Mei",
                                "Jun",
                                "Jul",
                                "Agu",
                                "Sep",
                                "Okt",
                                "Nov",
                                "Des",
                            ],
                            longhand: [
                                "Januari",
                                "Februari",
                                "Maret",
                                "April",
                                "Mei",
                                "Juni",
                                "Juli",
                                "Agustus",
                                "September",
                                "Oktober",
                                "November",
                                "Desember",
                            ],
                        },
                    },
                })
            }

            initFlatpickr()

            Livewire.hook('commit', ({ component, commit, respond, succeed, fail }) => {
                // Equivalent of 'message.sent'

                succeed(({ snapshot, effect }) => {
                    // Equivalent of 'message.received'

                    queueMicrotask(() => {
                        // Equivalent of 'message.processed'
                        initFlatpickr()
                    })
                })

                fail(() => {
                    // Equivalent of 'message.failed'
                })
            })
        })
    </script>
@endpush
