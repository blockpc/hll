{{-- resources/views/mail/new-user-created.blade.php --}}

@component('mail::message')
{{ __('system.users.mails.new_user_created_subject', ['name' => $name]) }}

{{ __('system.users.mails.new_user_created_message') }}

{{ __('system.users.mails.new_user_verify_message') }}

@component('mail::button', ['url' => $verificationUrl])
{{ __('system.users.mails.new_user_verify_button') }}
@endcomponent

@component('mail::button', ['url' => $loginUrl])
{{ __('system.users.mails.new_user_created_login_button') }}
@endcomponent

{{ __('system.users.mails.new_user_created_recommendation') }}

@component('mail::button', ['url' => $changePasswordUrl])
{{ __('system.users.mails.new_user_change_password') }}
@endcomponent

{{ __('system.users.mails.new_user_created_farewell') }}<br>
{{ __('system.users.mails.new_user_created_signature', ['app' => config('app.name')]) }}
@endcomponent
