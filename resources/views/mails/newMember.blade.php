@component('mail::message')

Beste {{ $participant->firstName }},

Welkom bij Studievereniging Salve Mundi!<br>
We hopen dat je een fantastische introductie week hebt gehad.

Bij je introductie inschrijving ontvang je een jaar lang gratis lidmaatschap bij de vereniging!
Deze moet nog wel geactiveerd worden. Hier onder dus ook de stappen om je account te activeren!

Stap 1: Ga naar https://salvemundi.nl<br>
Stap 2: Login met je account waarvan de login gegevens in deze mail staan.<br>
Stap 3: Ga naar "mijn account" via de navigatie balk.<br>
Stap 4: Voer de coupon code in die onderaan de mail staat!<br>
Stap 5: Maak de betaling van 1 cent. <br>
Stap 6: Je account is geactiveerd, verifieer dit door na de betaling nog maal naar mijn account te gaan.

Nu heb je toegang tot leden korting, de whatsapp groepen en veel meer!

Jouw inloggegevens:<br>
Gebruikersnaam: {{ $upn }}<br>
Wachtwoord: {{ $randomPass }}

Coupon code: {{ $coupon }}

Heb je vragen over je account of lukt het niet helemaal? Aarzel niet om contact op te nemen met ict@salvemundi.nl of bestuur@salvemundi.nl

Met vriendelijke groet,

Salve Mundi <br>
Rachelsmolen 1 <br>
5612 MA Eindhoven<br>
intro@salvemundi.nl<br>
+31 6 24827777
@endcomponent
