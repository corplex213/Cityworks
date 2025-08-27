<x-mail::message>
{{-- Custom Logo or Header --}}
<div style="text-align:center; margin-bottom: 24px;">
    <img src="{{ asset('frontend/img/baguio-logo.png')}}" alt="CityWorks Logo" style="height: 60px;">
</div>

{{-- Custom Greeting --}}
# Hi Engineer, {{ $user->name ?? 'User' }}!

{{-- Custom Intro --}}
Thank you for registering with **CityWorks**. To get started and access all features, please verify your email address by clicking the button below.

{{-- Action Button --}}
@isset($actionText)
<?php
    $color = match ($level) {
        'success', 'error' => $level,
        default => 'primary',
    };
?>
<x-mail::button :url="$actionUrl" :color="$color">
{{ $actionText }}
</x-mail::button>
@endisset

{{-- Custom Outro --}}
If you did not create an account, no further action is required.

This verification link will expire in 60 minutes.

Best regards,  
City Engineering Office Team

{{-- Subcopy --}}
@isset($actionText)
<x-slot:subcopy>
@lang(
    "If you're having trouble clicking the \":actionText\" button, copy and paste the URL below\n".
    'into your web browser:',
    [
        'actionText' => $actionText,
    ]
) <span class="break-all">[{{ $displayableActionUrl }}]({{ $actionUrl }})</span>
</x-slot:subcopy>
@endisset
</x-mail::message>