@component('mail::message')

Beste {{ $participant->firstName }},

Je betaling is in goede orde ontvangen.
Bedankt voor het inschrijven voor de intro!

Bewaar de QR-code in de bijlagen goed!

Voor meer nieuws en updates: https://intro.salvemundi.nl/blogs

Infoboekje: https://intro.salvemundi.nl/pdf/informatieboekje.pdf

@if($introLocation)
Adres intro locatie:
{{$introLocation}}
@endif

Met vriendelijke groet,

Salve Mundi <br>
Rachelsmolen 1 <br>
5612 MA Eindhoven<br>
intro@salvemundi.nl<br>
+31 6 24827777
@endcomponent
