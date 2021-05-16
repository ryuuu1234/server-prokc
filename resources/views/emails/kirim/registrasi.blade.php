@component('mail::message')
# {{$content['title']}}

Terimakasih Telah registrasi pada Aplikasi Kami, <br>
Silahkan Masukkan Code yang tertera di bawah ini

@component('mail::button', ['url' => ''])
{{$content['otp']}}
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent
