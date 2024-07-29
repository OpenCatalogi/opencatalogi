# Installatie van Nextcloud Demo/Test-omgeving

## Wat Je Nodig Hebt

Om alles soepel te laten draaien, heb je het volgende nodig:

* [**Docker:**](https://www.docker.com/products/docker-desktop/) en Docker Desktop
* [**WSL2:**](https://learn.microsoft.com/en-us/windows/wsl/install) (Windows Subsystem for Linux) als je Windows gebruikt. Dit kan geïnstalleerd worden via de Microsoft Store.
* **Systeemeisen:** Minimaal 4 GB RAM en 2 CPU's

## Aan de slag

Deze repository heeft een snelle start met een Docker Compose-bestand. Hiermee kun je de applicatie in één keer opstarten.

Volg deze eenvoudige stappen om de applicatie te starten:

1. **Docker Desktop**: [Download](https://www.docker.com/products/docker-desktop/) en installeer Docker Desktop.
2. **WSL2**: [Download](https://learn.microsoft.com/en-us/windows/wsl/install) en installeer via de Microsoft Store.
3. **Code**: [Download](https://github.com/ConductionNL/opencatalogi/archive/refs/heads/master.zip) de code als zip bestand en pak deze uit.
4.  **(Optie 1, Sart docker via installer)**

    * [Download](installatie-van-nextcloud-demo-test-omgeving.md) het .sh bestand
    * Acodeer een eveneuteele veiligheids waarschuwing
    * Plaats het bestand in dezelfde folder als de code
    * Klik met de rechtermuisknop op het bestand en selecteer uitvoeren **(Optie 2, via comand line inerface)**
    * Typ het volgende commando in de command prompt en druk op Enter:

    ````
      ```cli
    ````

    ````
      cd pad/naar/uitgepakte/map
      ```

      Vervang “pad/naar/uitgepakte/map” door de locatie waar je de bestanden hebt uitgepakt. Bijvoorbeeld:

      ```cli
      cd C:\Users\{{jouwgebruikersnaam}}\Downloads\opencatalogi-main
    ````

    * Typ het volgende commando in de command prompt en druk op Enter:

    ````
      ```cli
    ````

    ````
      docker compose up
      ```
    ````

    * Wacht tot de applicatie is opgestart. Je ziet veel tekst voorbij komen, wacht tot je deze melding ziet:

    ````
      ```cli
    ````

    ````
      [core:notice] [pid 1] AH00094: Command line: 'apache2 -D FOREGROUND'
      ```
    ````
5. **Open de applicatie**: Open je webbrowser en ga naar [http://localhost:8080](http://localhost:8080)
6. **Login**: Voor de standaard installatie werkt dit met admin als log **en** admin wachtwoord

En dat is het! Volg deze stappen om de OpenCatalogi-app snel en soepel op te zetten.
