# Installatie van Nextcloud Development-omgeving

Als je aan de slag wilt met het ontwikklen voor Open Catalogi kan je beter een development omgeving neerzetten, de instructies daarvoor vind je als [tutorial op nextcloud](https://cloud.nextcloud.com/s/iyNGp8ryWxc7Efa?path=%2F1%20Setting%20up%20a%20development%20environment).

## De code voor de OpenCatalogi-app

Het toevoegen van een Nextcloud app is niet moeilijk, maar het helpt wel als je basiskennis hebt van git en hoe applicatiestructuren ingedeeld zijn. Deze handleiding gaat uit van een succesvolle installatie van Nextcloud. Er is [hiervoor](https://cloud.nextcloud.com/s/iyNGp8ryWxc7Efa?path=%2F1%20Setting%20up%20a%20development%20environment) een goede tutorial te vinden van Nextcloud zelf.

De makkelijkste manier is om naar de repository te gaan van de OpenCatalogi-Nextcloud app en de code hier te kopieren naar de juiste Nextcloud-directory.\

Dat kan op 2 manieren.

1) De git clone manier (verondersteld dat je git geinstalleerd hebt):\

Ga in jouw terminal naar de "apps-extra"-directory. Die is te vinden in `nextcloud-docker-dev/workspace/server/apps-extra/`

en daar het volgende commando's uit te voeren.

```cli
git clone https://github.com/ConductionNL/opencatalogi.git
cd opencatalogi
npm install
docker compose up nextcloud proxy 
```

2) in plaats van de git clone, kan er gekozen worden voor de code te downloaden in een .ZIP-bestand en daarna uit te pakken in de "apps-extra"-directory. Dit vervangt het git clone commando. De rest van de stappen zijn hetzelfde.

Hou er rekening mee dat er afspraken zijn over het terugleveren van ontwikkelinde code die vind je [hier](aan-de-slag-met-development.md).

Nadat je de code locaal hebt gekopierd moet je de app toevoegen en actieveren, kijk daarvoor onder [app toevoegen](de-opencatalogi-app-toevoegen-aan-nexc=tcloud.md).
