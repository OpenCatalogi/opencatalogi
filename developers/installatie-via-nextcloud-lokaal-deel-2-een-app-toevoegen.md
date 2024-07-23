---
description: (vervolg van deel 1)
---

# Installatie via Nextcloud lokaal - deel 2: een app toevoegen

### De code voor de OpenCatalogi-app

Het toevoegen van een Nextcloud app is niet moeilijk, maar het helpt wel als je basiskennis hebt van git en hoe applicatiestructuren ingedeeld zijn. Deze handleiding gaat uit van een succesvolle installatie van Nextcloud en de\
\
De makkelijkste manier is om naar de repository te gaan van de OpenCatalogi-Nextcloud app en de code hier te kopieren naar de juiste Nextcloud-directory.\
\
Dat kan op 2 manieren.\
\
1\) De git clone manier (verondersteld dat je git geinstalleerd hebt):\
\
Ga in jouw terminal naar de "apps-extra"-directory. Die is te vinden in `nextcloud-docker-dev/workspace/server/apps-extra/`

en daar het volgende commando's uit te voeren.

<pre><code><strong>git clone https://github.com/ConductionNL/opencatalogi.git
</strong><strong>cd opencatalogi
</strong><strong>npm install
</strong><strong>docker compose up nextcloud proxy 
</strong></code></pre>

2\) in plaats van de git clone, kan er gekozen worden voor de code te downloaden in een .ZIP-bestand en daarna uit te pakken in de "apps-extra"-directory. Dit vervangt het git clone commando. De rest van de stappen zijn hetzelfde.

### De app activeren

De code voor de app staat er nu, maar in Nextcloud moeten apps geactiveerd worden voor gebruik. Hiervoor kan er geklikt worden op het profiel icoontje rechts bovenin. Kies voor "Apps".

<figure><img src="../.gitbook/assets/image (5).png" alt="" width="138"><figcaption></figcaption></figure>

In het volgende scherm, zie je aan de linkerzijde een navigatie menu. Klik op "Disabled apps". Je ziet een overzicht met de apps die in de "apps-extra"-directory zitten. . \


<figure><img src="../.gitbook/assets/image.png" alt=""><figcaption><p>Het enablen van de applicatie</p></figcaption></figure>

Zodra je klikt op "enable" verschijnt deze links bovenin, naast het Nextcloud logo. Dit zijn de shortcut-icoontjes voor de geactiveerde applicaties. Klik op het icoontje van OpenCatalogi en je zit in de app!

<figure><img src="../.gitbook/assets/image (2).png" alt=""><figcaption></figcaption></figure>
