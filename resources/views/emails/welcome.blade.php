@component('mail::message')
# Welcome to {{ config('app.name') }}

Hi {{ $email }},

Thanks for registering. We are excited to have you on board.

Thanks,<br>
{{ config('app.name') }}
@endcomponent
