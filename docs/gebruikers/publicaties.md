# Publicaties

> Publicaties zijn onderdeel van de (Open Catalogi Standaard)\[] en gebaseerd op het [publication object](https://conduction.stoplight.io/docs/open-catalogi/9bebd6bf4fe35-publication). Publicaties kennen eigenschappen zo als gedefineerd in een publicaite type en kunnen worden gekopeld aan bijlagen

Een publicatie representeerd iets wat je wilt publiceren, het beschrijft de handeling van publiceren en de spelregels waaronder iets gepubliceerd wordt.het is een soort "verpakking" of "omhulsel" dat zowel de kerngegevens (data) als aanvullende informatie over die gegevens (metadata) bevat.

Stel je voor dat je een foto hebt. De foto zelf is de data, terwijl de informatie zoals wanneer de foto is genomen, de resolutie van de foto, de camera-instellingen, en de locatie waar de foto is genomen, de metadata vormen. Een publicatie zou in dit geval zowel de foto als al deze aanvullende informatie samen in één pakketje verpakken, zodat je deze als een geheel kunt behandelen en doorzoeken.

Met andere woorden, een publiatie maakt het mogelijk om zowel de data als de bijbehorende metadata op een gestandaardiseerde manier te bewaren en te verwerken, zonder dat je deze informatie telkens apart hoeft te beheren. Dit is handig omdat je zo alle relevante informatie bij elkaar hebt, wat zorgt voor meer context en daarmee een betere interpretatie van de data.

Publicaties zijn altijd onderdeel van een collectie in de vorm van een [catalogus](../beheerders/catalogi.md) en behoren tot een [publicatie type](../beheerders/metadata.md) dit laatste zorgt ervoor dat ze voorspelbaar zijn. e.g. fotos hebben altijd een waarde resulutie.

## Publicaties toevoegen

Publicaties kunnen worden toegevoegd via:

* De publicatie toeveogen knop boven aan het hoofd menu (links)
* Een catalogus geselecteerd in het hoofd menu (via het hamburger menu achter de zoekbalk)
* Een catalogus detail pagina

Een publicatie leeft altijd binnen één catalogus en word gedefineerd door één publicatie type. Omdat catalogi bepalen welke publicatie typen beschickbaar zijn voor die catalogi moet er eerst een catalogus worden gekozen voordat er een metadata type kan worden gekozen. Daarmee word de volgorde bij het aanmaken van een publicatie:

1. Catalogus kiezen (indien niet opgestart vanuit een specifieke catalogus)
2. Publicatietype kiezen
3. Publicatie detalis aanvullen

Eigenschapen en bijlagen kunnen worden toegevoegd nadat de publicatie is toegevoegd.

## Publicaties beheren

De gebruikersbeheerinterface werkt intuïtief. Aan de linkerkant van de pagina bevindt zich een overzicht van catalogi. Met de blauwe knop bovenaan kun je een publicatie aanmaken. Dit opent een modal genaamd "Publicatie toevoegen".

Hieronder is een voorbeeld van een ingevulde modal voor het aanmaken van een Woo-publicatie.

<div align="left">

<figure><img src="../assets/oc_publicatie_toevoegen_form_1.png" alt="" width="371"><figcaption><p>Het eerste gedeelte - gegevens over de publicatie</p></figcaption></figure>

<figure><img src="../assets/Open-Catalogi-Nextcloud.png" alt="" width="374"><figcaption><p>het tweede gedeelte - het aanwijzen van de catalogi</p></figcaption></figure>

</div>

Na het opslaan van de publicatie, is deze zichtbaar onder de catalogi "Woo". Om de publicatie aan te passen, te depubliceren of andere acties uit te voeren, klik je op de blauwe "Actie"-knop rechtsboven bij de getoonde publicatie, of de drie puntjes rechts van de publicatie zelf.\\

Onder is een voorbeeld van een publicatie en de Actie-mogelijkheden.

<figure><img src="../.assets/oc_publicatie_acties.png" alt="" width="375"><figcaption></figcaption></figure>

![alt text](image-1.png)

## Acties

![alt text](image.png)

## Bijlagen

In het merendeel van de gevallen wordt een publicatie opgemaakt om bestanden te delen (bijvoorbeeld vanuit een woo verzoek). Deze bestanden vormen de informatie in de publicaite en worden aan een publicatie gekoppels als bijlagen. Een bijlage kan zowel onderdeel zijn van de publicatie (er in worden geupload) als elders staan (er wordt naar verwezen).

Naast een bestand kan een bijlage (per verwijzing) bijvoorbeeld ook een website of artikel op een website zijn.

![alt text](image-3.png)

## Eigenschappen

Een tweede manier om informatie op te nemen in een publicaite is via eigenschappen. Eigenschappen zijn voor gedefineerde opties (via [publicatie type](../beheerders/metadata.md)) waar een waarde aan kan worden toegekend.

![alt text](image-2.png)
