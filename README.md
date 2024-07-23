---
description: test
---

# README

## Open Catalogi

### Documentatie

* [Icons](https://pictogrammers.com/library/mdi/)
* [Layout](https://docs.nextcloud.com/server/latest/developer\_manual/design/layout.html)
* [Componenten](https://nextcloud-vue-components.netlify.app/)
* [Developing on wsl with visual studio](https://code.visualstudio.com/docs/remote/wsl)

### Onze favo dev omgeving

* https://marketplace.visualstudio.com/items?itemName=ms-vscode-remote.remote-wsl
* https://marketplace.visualstudio.com/items?itemName=Vue.volar

## Frontend veranderen

Om de frontend te veranderen verander je de content in de .vue bestanden. Elke keer als je iets hebt gewijzigd in en .vue bestand dan moet je `npm run dev` draaien. Een makkelijkere manier om dit bij te houden is `npm run watch` dit commando kijkt naar veranderingen in de .vue bestanden en zet dit meteen door. Je moet wel elke keer als je iets veranderd verversen

## Grafieken

Voor grafieken gebruiken we APEXCHARTS https://apexcharts.com/vue-chart-demos/area-charts/spline/

## Vue

Voor de project gebruiken we vue 2.7

### De repository draaien

#### Quick start

Deze repository bevat een quick start docker compose file. Deze docker compose file bevat een init-container die ervoor zorgt dat de applicatie in één keer kan worden gestart. Om deze te gebruiken is [docker](https://docker.com) vereist. Als docker is geïnstalleerd kan de applicatie worden gestart door in deze map het commando `docker compose up` te draaien. Wanneer de nextcloud container aangeeft ready te zijn (`[core:notice] [pid 1] AH00094: Command line: 'apache2 -D FOREGROUND'`), kan deze benaderd worden via http://localhost:8080. Na inloggen (er komt eerst een scherm om een account aan te maken) kan in het rechtermenu de app aangezet worden onder het gebruikersmenu en dan 'apps' -> 'jouw apps' -> opencatalogi -> inschakelen.

Daarna kan de opencatalogi-app worden geopend via het hoofdmenu, en bij instellingen -> configuratie moeten dan worden ingevuld: een MongoDB API met sleutel en clusternaam, en voor het activeren van het zoekendpoint een elasticsearch met API key en index.
