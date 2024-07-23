# Installatie via Nextcloud lokaal - deel 1: Nextcloud

Deze tutorial is perfect voor jou als je een lokaal draaiende ontwikkelomgeving voor Nextcloud-ontwikkeling en Nextcloud-appontwikkeling wilt opzetten. Afhankelijk van je besturingssysteem duurt dit een half uur tot een paar uur.

**Als je snel iets wilt uitproberen of een workshop wilt volgen, gebruik dan liever de** [**DevContainer**](installatie-via-docker.md)**.**

In deze tutorial leer je hoe je je ontwikkelomgeving kunt opzetten met Docker. Na deze tutorial weet je:

* Hoe je de Docker Desktop-app op je machine installeert
* Hoe je Nextcloud lokaal draait

### Wat Je Moet Weten

Voordat je begint, is het handig als je een beetje bekend bent met:

* Docker en Docker Compose
* Command line interface (CLI)
* Basiskennis van Linux (voor WSL2 op Windows)
* Basisbegrip van webapplicaties en netwerken

De stappen in de tutorial zijn verschillend afhankelijk van je besturingssysteem, dus we hebben een tutorial voor Ubuntu-gebruikers, Mac-gebruikers en Windows-gebruikers.

ℹ️ Deze tutorial gaat uit van een vers geïnstalleerd besturingssysteem. Dit betekent dat de tutorial waarschijnlijk niet werkt als je al Docker hebt geïnstalleerd, containers hebt draaien, of als er andere processen op poort 80 draaien. Zorg er ook voor dat je bent ingelogd als admin-gebruiker en niet als secundaire of gastgebruiker.

De stappen in de tutorial zijn verschillend afhankelijk van je besturingssysteem, deze handleiding volgt de Windows-route. Voor andere besturingsystemen, volg de [Nextcloud documentatie](https://cloud.nextcloud.com/s/iyNGp8ryWxc7Efa?path=%2F1%20Setting%20up%20a%20development%20environment)

### Installatie van de Nextcloud Ontwikkelomgeving op Windows

#### Stap 1: Controleer je Systeemeisen

* Je moet een recente build van Windows draaien (Build 20262+). Om je versie en buildnummer te controleren, druk op de Windows-logotoets + R, typ "winver" en selecteer OK. Je kunt bijwerken via het menu Instellingen of de Windows Update Assistant.
* Zorg ervoor dat je geen containers hebt draaien of andere processen op poort 80.
* Zorg ervoor dat je bent ingelogd als admin-gebruiker.
* Windows 11 64-bit: Home of Pro versie 21H2 of hoger, of Enterprise of Education versie 21H2 of hoger.
* Windows 10 64-bit: Home of Pro 21H1 (build 19043) of hoger, of Enterprise of Education 20H2 (build 19042) of hoger.
* 8GB RAM of meer
* SSD met minimaal 40GB vrije ruimte
* 4-core, 8-threads CPU

Controleer echt je systeemeisen voordat je verder gaat. Sla deze stap niet over. Als je computer niet aan deze eisen voldoet, is de kans groot dat deze tutorial niet werkt.

#### Stap 2: Installeer WSL 2

* Open de Windows Command Prompt in administrator-modus door met de rechtermuisknop te klikken en 'Als administrator uitvoeren' te selecteren.
* Voer het volgende commando uit:wsl --install -d Ubuntu-20.04
* Volg de installatieprocedure.
  * Als je de foutmelding "WSL 2 requires an update to its kernel component" krijgt, bezoek dan [deze link](https://aka.ms/wsl2kernel), download en installeer de kernel.

Het `--install` commando voert de volgende acties uit:

1. Activeert de WSL en Virtual Machine Platform componenten
2. Downloadt en installeert de nieuwste Linux-kernel
3. Stelt WSL2 in als standaard
4. Downloadt en installeert de Ubuntu Linux distributie versie 20.04

Na de installatie kun je de Ubuntu Linux distributie openen via het startmenu door te zoeken op 'Ubuntu'.

* Herstart je computer zodat alle wijzigingen van kracht worden.

#### Stap 3: Ubuntu Instellen

Open 'Ubuntu' via het Windows startmenu.

Je wordt gevraagd om een gebruikersnaam en wachtwoord voor je Linux-distributie in te stellen.

* Deze gebruikersnaam en wachtwoord zijn specifiek voor elke afzonderlijke Linux-distributie die je installeert en hebben geen invloed op je Windows-gebruikersnaam.
* Let op dat er tijdens het invoeren van het wachtwoord niets op het scherm verschijnt. Dit wordt blind typen genoemd. Je ziet niet wat je typt, dit is volkomen normaal.

#### Stap 4: Docker Installeren

Ga naar [de Docker-website](https://www.docker.com/products/docker-desktop/). Klik op de downloadlink voor Windows-gebruikers. Dit downloadt een .exe-bestand.

Wanneer de download is voltooid, open het .exe-bestand en volg de installatieprocedure.

Mogelijk krijg je een prompt om je computer opnieuw op te starten. Als je deze prompt krijgt, herstart je computer dan.

Zodra Docker is geïnstalleerd, start Docker Desktop vanaf het Windows startmenu en selecteer het Docker-pictogram vanuit het verborgen pictogrammenu van je taakbalk. Klik met de rechtermuisknop op het pictogram om het Docker-commando menu weer te geven en selecteer "Settings".

Lees en accepteer de gebruiksvoorwaarden wanneer daarom wordt gevraagd:

Zorg ervoor dat "Use the WSL 2 based engine" is aangevinkt in Settings > General.

Zorg ervoor dat de Ubuntu-distributies zijn geselecteerd in Settings > Resources > WSL Integration.

Om te bevestigen dat Docker is geïnstalleerd, open een WSL-distributie (bijv. Ubuntu) en geef de versie en buildnummer weer door het volgende commando in te voeren:

```bash
docker --version
```

Binnen Ubuntu kunnen sommige Docker-commando's alleen worden uitgevoerd door leden van de docker-groep. Voeg je gebruiker toe aan de groep zodat je Docker-commando's eenvoudig kunt uitvoeren (vervang `user_name` door je login):

```bash
sudo usermod -aG docker user_name
```

Bevestig dat het volgende commando werkt:

```bash
docker ps
```

De uitvoer zou moeten zijn: `CONTAINER ID IMAGE COMMAND CREATED STATUS PORT NAMES.`

Als het commando `docker ps` niet werkt, kun je dit mogelijk oplossen door de WSL-integratie in Docker Desktop uit en weer in te schakelen.

#### Stap 5: Installeer nvm en node in Ubuntu

Open Ubuntu en zorg ervoor dat het up-to-date is door het volgende commando uit te voeren:

```bash
sudo apt update && sudo apt upgrade
```

Installeer curl:

```bash
kopiërensudo apt install curl
```

Installeer nvm:

```bash
curl -o- https://raw.githubusercontent.com/nvm-sh/nvm/master/install.sh | bash
```

Controleer of nvm werkt en welke versies van node zijn geïnstalleerd (zou geen moeten zijn):

```bash
nvm ls
```

Installeer node 16:

```bash
nvm install 16
```

Controleer of node en npm zijn geïnstalleerd:

```bash
node --version
npm --version
```

#### Stap 6: Bewerk je hostbestand

Je kunt dit doen terwijl het commando van Stap 2 nog aan het laden is.

Je moet de volgende regel toevoegen aan het hostbestand:

```plaintext
plaintextCode kopiëren127.0.0.1 nextcloud.local
```

Volg deze stappen om het hostbestand te bewerken:

**Stap 6.1: Open Notepad als Administrator**

Je hebt beheerdersrechten nodig voor deze bewerking.

1. Klik op de Windows-knop en typ "notepad". Laat de zoekfunctie de Notepad-toepassing vinden.
2. Klik met de rechtermuisknop op de Notepad-app en selecteer 'Als administrator uitvoeren'.
3. Windows User Account Control zal verschijnen met de vraag "Wilt u toestaan dat deze app wijzigingen aanbrengt aan uw apparaat?" Klik op Ja.

**Stap 6.2: Open het Windows Hosts-bestand**

1. In Notepad, klik op Bestand > Openen.
2. Navigeer naar `C:\Windows\System32\drivers\etc`.
3. In de rechterbenedenhoek, net boven de Open-knop, klik op het dropdown-menu om het bestandstype te wijzigen naar Alle bestanden.
4. Selecteer "hosts" en klik op Openen.

**Stap 6.3: Bewerk het Bestand**

Voeg de volgende regel toe aan de onderkant van het hostbestand en sla het bestand op:

```plaintext
127.0.0.1 nextcloud.local
```

Hier is een korte uitleg van hoe de regels in het Windows hostbestand zijn gestructureerd:

```
0.0.0.0 server.domain.com
```

Het eerste cijfer van vier cijfers is het IP-adres dat je aan het mappen bent. Dit kan het interne IP-adres van een server op het netwerk zijn, of het IP-adres van een website.

Het tweede label is de naam die je in een browser wilt kunnen typen om de server op het IP-adres dat je zojuist hebt opgegeven te bereiken.

#### Stap 7: Installeer git en nextcloud-docker-dev

Installeer het git versiebeheersysteem:

```bash
sudo apt install git
```

Clone de nextcloud-docker-dev ontwikkelomgeving voor Nextcloud en volg de eenvoudige master setup om Nextcloud te downloaden en te installeren:

```bash
git clone https://github.com/juliushaertl/nextcloud-docker-dev.git
cd nextcloud-docker-dev
./bootstrap.sh
sudo sh -c "echo '127.0.0.1 nextcloud.local' >> /etc/hosts"
```

Start nu Nextcloud:

```bash
docker-compose up nextcloud proxy / docker compose up nextcloud proxy
```

#### Stap 8: Toegang tot je Nextcloud

Open je browser en ga naar [http://nextcloud.local/](http://nextcloud.local/) om Nextcloud in de browser te openen.

* Mogelijk wordt je gevraagd om bij te werken voordat je verder gaat. Klik op de knop "Update" en volg de procedure.
* Log in met de gebruikersnaam "admin" en wachtwoord "admin".

ℹ️ De gebruikersnaam en het wachtwoord zijn voor alle gebruikers hetzelfde.

En dat is het! Volg deze stappen zorgvuldig om je ontwikkelomgeving voor Nextcloud op te zetten.\
\
![](<../.gitbook/assets/image (3) (1).png>)
