@props(['buttonType' => 'primary'])

<button {{ $attributes->merge(['type' => 'button', 'class' => 'btn btn-' . $buttonType . ' w-100 waves-effect waves-light']) }}>
    {{ $slot }}
</button>
