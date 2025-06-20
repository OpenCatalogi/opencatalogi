# De OpenCatalogi-app toevoegen aan Nextcloud

We gaan er voor deze stap vanuit dat je reeds een werkende lokale Nextcloud-omgeving hebt met daarin de code voor de app, heb je die nog niet kijk dan eerst onder Installatie van [Nextcloud Demo/Test-omgeving](installatie-van-nextcloud-demo-test-omgeving.md) of[Installatie van Nextcloud Development](../Installation)-omgeving

## De app activeren

De code voor de app staat er nu, maar in Nextcloud moeten apps geactiveerd worden voor gebruik. Hiervoor kan er geklikt worden op het profiel icoontje rechts bovenin. Kies voor "Apps".

![next cloud app menu](../assets/activate-app-menu.png)

In het volgende scherm, zie je aan de linkerzijde een navigatie menu. Klik op "Disabled apps". Je ziet een overzicht met de apps die in de "apps-extra"-directory zitten.

![nextcloud uitgeschakelde apps](../assets/nc_disabled_apps.png)

Zodra je klikt op "enable" verschijnt deze links bovenin, naast het Nextcloud-logo. Dit zijn de shortcut-icoontjes voor de geactiveerde applicaties.

Klik op het icoontje van OpenCatalogi en je zit in de app!

![Nexcloud app menu](<../assets/nc_app_menu (1).png>)

## De app configuren

* Open de OpenCatalogi-app via de navigeerbalk links bovenin het scherm. Het zal het meest rechtse icoontje zijn.

![OpenCatalogi instellingen](../assets/oc_instellingen_1.png)

* Vul bij 'settings' -> 'configuratie' de benodigde gegevens in voor opslag:

  * default instellingen voor opslag (optioneel)
  * DRC or ORC (optioneel)
  * Een MongoDB API met sleutel en clusternaam(optioneel).
  * Voor het activeren van het zoekendpoint: een Elasticsearch met API key en index(optioneel).

  ![img](../assets/Instellingen_voor_opslag.png)
