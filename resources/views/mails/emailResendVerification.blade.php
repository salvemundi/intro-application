@component('mail::message')

Beste {{ $participant->firstName }},

Je hebt je email nog niet geverifieerd!

Wel vragen we je om je mail te verifiëren via de volgende link:

{{ env('APP_URL') }}inschrijven/verify/{{ $verificationToken->id }}

Alvast bedankt!

Met vriendelijke groet,

Salve Mundi <br>
RachelsMolen 1 <br>
5612 MA Eindhoven
@endcomponent