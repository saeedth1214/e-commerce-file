@component('mail::message')
# Introduction

Please click on button To Redirect to Verify Page.

@component('mail::button', ['url' => 'http://localhost:3000/authenticate/reset-password/?token='.$token.'&email='.$email])
Button Text
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent
