---
description: >-
  Dit document biedt een stapsgewijze handleiding voor het opzetten van een
  Nextcloud-instance met de OpenCatalogi-app. We geven je een overzicht van de
  benodigde kennis, systeemeisen, installatie-instr
---

# Installatie via Docker

### Wat Je Moet Weten

Voordat je begint, is het handig als je een beetje bekend bent met:

* Docker en Docker Compose
* Visual Studio Code (VSCode)
* Vue.js framework
* Command line interface (CLI)
* Basisbegrip van webapplicaties en API's

### Wat Je Nodig Hebt

Om alles soepel te laten draaien, heb je het volgende nodig:

* Docker: [Download en installatie](https://docker.com)
* Visual Studio Code (VSCode): [Download en installatie](https://code.visualstudio.com/)
* Onze favoriete VSCode-extensies:
  * [Remote - WSL](https://marketplace.visualstudio.com/items?itemName=ms-vscode-remote.remote-wsl)
  * [Volar (voor Vue 3 ondersteuning)](https://marketplace.visualstudio.com/items?itemName=Vue.volar)
* WSL2 (Windows Subsystem for Linux) als je Windows gebruikt
* Een systeem met minimaal 4 GB RAM en 2 CPU's

### Handige Documentatie

Check deze links voor extra info en hulp:

* [Icons](https://pictogrammers.com/library/mdi/)
* [Layout](https://docs.nextcloud.com/server/latest/developer\_manual/design/layout.html)
* [Componenten](https://nextcloud-vue-components.netlify.app/)
* [Developen op WSL met Visual Studio](https://code.visualstudio.com/docs/remote/wsl)

### Wat Voor Ons Lekker Werkt

Wij gebruiken de volgende tools en extensies voor de beste ontwikkelervaring:

* [VSCode Remote - WSL](https://marketplace.visualstudio.com/items?itemName=ms-vscode-remote.remote-wsl)
* [Volar (Vue.js ondersteuning)](https://marketplace.visualstudio.com/items?itemName=Vue.volar)

### Frontend Aanpassingen

Wil je de frontend aanpassen? Bewerk dan de inhoud in de `.vue`-bestanden. Na elke wijziging moet je `npm run dev` draaien. Een handige truc is om `npm run watch` te gebruiken; dit commando houdt veranderingen in de `.vue`-bestanden in de gaten en past ze direct toe. Vergeet niet om de pagina te verversen na elke wijziging.

### De Repository Draaien

#### Snel Aan de Slag

Deze repository heeft een snelle start met een Docker Compose-bestand. Hiermee kun je de applicatie in één keer opstarten. Je hebt hiervoor [Docker](https://docker.com) nodig.

Volg deze stappen om de applicatie te starten:

1. Installeer Docker als je dat nog niet hebt gedaan.
2. Voer in de hoofdmap van de repository het commando `docker compose up` uit.
3. Wacht tot de Nextcloud-container aangeeft dat deze klaar is (`[core:notice] [pid 1] AH00094: Command line: 'apache2 -D FOREGROUND'`), en ga dan naar [http://localhost:8080](http://localhost:8080).
4. Maak een account aan en log in. Schakel de OpenCatalogi-app in via het gebruikersmenu: 'apps' -> 'jouw apps' -> 'opencatalogi' -> 'inschakelen'.
5. Open daarna de OpenCatalogi-app via het hoofdmenu en vul bij 'instellingen' -> 'configuratie' de benodigde gegevens in:
   * Een MongoDB API met sleutel en clusternaam
   * Voor het activeren van het zoekendpoint: een Elasticsearch met API key en index

En dat is het! Volg deze stappen om de OpenCatalogi-app snel en soepel op te zetten.
