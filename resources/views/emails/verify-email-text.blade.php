{{ __('auth.verify_email_title') }} - {{ config('app.name', 'Aura') }}

{{ __('auth.welcome') }}, {{ $user->name }}!

{{ __('auth.verify_email_message') }}

{{ __('auth.verify_email_action') }}

{{ __('auth.verify_email_button') }}: {{ $verificationUrl }}

{{ __('auth.security_note') }} {{ __('auth.verification_expire_notice') }}

{{ __('auth.manual_link_instruction') }}
{{ $verificationUrl }}

---
&copy; {{ date('Y') }} {{ config('app.name', 'Aura') }}. {{ __('auth.all_rights_reserved') }}

{{ __('auth.automated_email_notice') }}

@if(config('app.url'))
{{ __('auth.visit_website') }}: {{ config('app.url') }}
@endif
