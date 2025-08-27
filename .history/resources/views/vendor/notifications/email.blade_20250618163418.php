<x-mail::message>
{{-- Custom Header --}}
<div style="text-align:center; margin-bottom: 24px;">
    <span style="
        display: inline-block;
        font-size: 2rem;
        font-weight: bold;
        letter-spacing: 2px;
        color: #2563eb;
        font-family: Arial, Helvetica, sans-serif;
        text-transform: uppercase;
        border-bottom: 2px solid #2563eb;
        padding-bottom: 8px;
    ">
        CITY ENGINEERING OFFICE
    </span>
</div>

{{-- Custom Greeting --}}
# Hi, {{ $user->name ?? 'User' }}!

{{-- Custom Intro --}}
<div style="font-size: 1.1rem; color: #374151; margin-bottom: 20px;">
    Thank you for registering with <b>CityWorks</b>. To get started and access all features, please verify your email address by clicking the button below.
</div>

{{-- Action Button --}}
@isset($actionText)
<?php
    $color = match ($level) {
        'success', 'error' => $level,
        default => 'primary',
    };
?>
<x-mail::button :url="$actionUrl" :color="$color" style="font-size:1.1rem; padding: 12px 32px;">
{{ $actionText }}
</x-mail::button>
@endisset

{{-- Custom Outro --}}
<div style="margin-top: 24px; color: #374151;">
    If you did not create an account, no further action is required.<br><br>
    This verification link will expire in 60 minutes.<br><br>
    Best regards,<br>
    <span style="font-weight: bold; color: #2563eb;">City Engineering Office</span>
</div>

{{-- Subcopy --}}
@isset($actionText)
<x-slot:subcopy>
<span style="color: #6b7280;">
@lang(
    "If you're having trouble clicking the \":actionText\" button, copy and paste the URL below\n".
    'into your web browser:',
    [
        'actionText' => $actionText,
    ]
)
</span>
<br>
<span class="break-all" style="color: #2563eb;">[{{ $displayableActionUrl }}]({{ $actionUrl }})</span>
</x-slot:subcopy>
@endisset
</x-mail::message>

{{-- Custom Footer --}}
<x-slot:footer>
<div style="text-align: center; margin-top: 24px; font-size: 12px; color: #999;">
    &copy; {{ date('Y') }} City Engineering Office. All rights reserved.
</div>
</x-slot:footer>