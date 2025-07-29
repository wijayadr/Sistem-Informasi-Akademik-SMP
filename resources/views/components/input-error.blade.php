@props(['messages'])

@if ($messages)
    <small {{ $attributes->merge(['class' => 'text-danger']) }}>
        @foreach ((array) $messages as $message)
            {{ $message }}
        @endforeach
    </small>
@endif
