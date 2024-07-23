---
description: >-
  Dit document biedt een stapsgewijze handleiding voor het opzetten van een
  Nextcloud-instance met de OpenCatalogi-app. We geven je een overzicht van de
  benodigde kennis, systeemeisen, etc.
---

# Installatie via Demo/Test-omgeving via Docker

### Wat Je Moet Weten

Voordat je begint, is het handig als je een beetje bekend bent met:

* Docker en Docker Compose
* Command line interface (CLI)
* Basisbegrip van webapplicaties en API's

### Wat Je Nodig Hebt

Om alles soepel te laten draaien, heb je het volgende nodig:

* Docker: [Download en installatie](https://docker.com)
* WSL2 (Windows Subsystem for Linux) als je Windows gebruikt
* Een systeem met minimaal 4 GB RAM en 2 CPU's

### Frontend Aanpassingen

Wil je de frontend aanpassen? Bewerk dan de inhoud in de `.vue`-bestanden. Na elke wijziging moet je `npm run dev` draaien. Een handige truc is om `npm run watch` te gebruiken; dit commando houdt veranderingen in de `.vue`-bestanden in de gaten en past ze direct toe. Vergeet niet om de pagina te verversen na elke wijziging.

### De Repository Draaien

#### Snel Aan de Slag

Deze repository heeft een snelle start met een Docker Compose-bestand. Hiermee kun je de applicatie in één keer opstarten. Je hebt hiervoor [Docker](https://docker.com) nodig.

Volg deze stappen om de applicatie te starten:

1. Installeer Docker als je dat nog niet hebt gedaan.
2. Voer in de hoofdmap van de repository het commando `docker compose up` uit.
3. Wacht tot de Nextcloud-container aangeeft dat deze klaar is (`[core:notice] [pid 1] AH00094: Command line: 'apache2 -D FOREGROUND'`), en ga dan naar [http://localhost:8080](http://localhost:8080).
4. Maak een account aan en log in.&#x20;
5. Schakel de OpenCatalogi-app in via het gebruikersmenu: 'apps' -> 'jouw apps' -> 'opencatalogi' -> 'inschakelen'.&#x20;
6. Open daarna de OpenCatalogi-app via het hoofdmenu en vul bij 'instellingen' -> 'configuratie' de benodigde gegevens in:
   * Een MongoDB API met sleutel en clusternaam
   * Voor het activeren van het zoekendpoint: een Elasticsearch met API key en index

En dat is het! Volg deze stappen om de OpenCatalogi-app snel en soepel op te zetten.
