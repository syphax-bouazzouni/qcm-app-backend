@component('mail::message')
# Introduction

Reset or change your password.

@component('mail::button', ['url' => env('CLIENT_URL').'/update-password?token='.$token])
    Change Password
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent
