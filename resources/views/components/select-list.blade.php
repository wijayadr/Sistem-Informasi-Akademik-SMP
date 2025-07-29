<div>
    <div wire:ignore>
        <select class="select2 w-full" {{ $attributes }}>
            @if(!isset($attributes['multiple']))
                <option></option>
            @endif
            @foreach($options as $key => $value)
                <option value="{{ $key }}">{{ $value }}</option>
            @endforeach
        </select>
    </div>
</div>

@push('script')
    <script>
        document.addEventListener("livewire:initialized", () => {
            let el = $('#{{ $attributes['id'] }}')

            function initSelect() {
                el.select2({
                    placeholder: '- Pilih -',
                    tags: {{ isset($attributes['tags']) ? 'true' : 'false' }},
                    minimumInputLength: {{ isset($attributes['minimumInputLength']) ? $attributes['minimumInputLength'] : 0 }},
                })
            }

            initSelect()

            Livewire.hook('commit', ({ component, commit, respond, succeed, fail }) => {
                // Equivalent of 'message.sent'

                succeed(({ snapshot, effect }) => {
                    // Equivalent of 'message.received'

                    queueMicrotask(() => {
                        // Equivalent of 'message.processed'
                        initSelect()
                    })
                })

                fail(() => {
                    // Equivalent of 'message.failed'
                })
            })

            el.on('change', function (e) {
                let data = $(this).select2("val")
                if (data === "") {
                    data = null
                }
                @this.set('{{ $attributes['wire:model'] ? $attributes['wire:model'] : $attributes['wire:model.live'] }}', data)
            });
        });
    </script>
@endpush
