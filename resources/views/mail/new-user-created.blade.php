{{-- resources/views/mail/new-user-created.blade.php --}}

@component('mail::message')
{{ __('system.users.mails.resend.verification_email_greeting', ['name' => $name]) }}

{{ __('system.users.mails.new_user_created_message') }}

{{ __('system.users.mails.new_user_verify_message') }}

@component('mail::button', ['url' => $verificationUrl])
{{ __('system.users.mails.new_user_verify_button') }}
@endcomponent

{{ __('system.users.mails.new_user_created_recommendation') }}

{{ __('system.users.mails.new_user_created_farewell') }}<br>
{{ __('system.users.mails.new_user_created_disclaimer') }}<br>
{{ __('system.users.mails.new_user_created_signature', ['app' => config('app.name')]) }}
@endcomponent
