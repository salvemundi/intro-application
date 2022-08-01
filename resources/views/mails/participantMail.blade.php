@component('mail::message')

Beste {{ $participant->firstName }},

{!! nl2br($konttent) !!}

@if(isset($confirmationToken))
Betalingslink:

{{ env('APP_URL') }}inschrijven/betalen/{{ $confirmationToken->id }}
@endif

Met vriendelijke groet,

Salve Mundi <br>
Rachelsmolen 1 <br>
5612 MA Eindhoven
@endcomponent
