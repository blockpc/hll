{{-- resources/views/mail/resend-user-email-verification.blade.php --}}

@component('mail::message')
{{ __('system.users.mails.new_user_created_subject', ['name' => $name]) }}

{{ __('system.users.mails.resend.verification_email_message') }}

@component('mail::button', ['url' => $verificationUrl])
{{ __('system.users.mails.new_user_verify_button') }}
@endcomponent

{{ __('system.users.mails.resend.verification_recommendation') }}

{{ __('system.users.mails.resend.verification_email_farewell') }}<br>
{{ __('system.users.mails.resend.verification_email_disclaimer') }}<br>
{{ __('system.users.mails.resend.verification_email_signature', ['app' => config('app.name')]) }}
@endcomponent
