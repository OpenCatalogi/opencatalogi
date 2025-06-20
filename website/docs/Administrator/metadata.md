# Metadata

Iedere publicatie op OpenCatalogi hoort bij een bepaald metadatatype. Dit metadatatype kan door jezelf gedefinieerd zijn of overgenomen van andere catalogi. Metadata beschrijft een publicatie in doel, archivering en inhoudelijke gegevens. Het maakt publicaties leesbaar en interpreteerbaar, maar vormt in de praktijk ook de meest basale zoekmogelijkheid: "Doe mij alle publicaties van het type WOO-verzoek."

> Metadata zijn onderdeel van de [OpenCatalogi-Standaard](https://github.com/OpenCatalogi/.github/blob/main/docs/Standaard.md) en gebaseerd op het [metadata object](https://conduction.stoplight.io/docs/open-catalogi/92e81a078982b-metadata). Zij beschrijven de data in publicaties aan de hand van het vooraf definiëren van properties, die zijn gedefinieerd in het [property object](https://conduction.stoplight.io/docs/open-catalogi/d0ci97hdxnctp-property)

## Metadata overnemen

Er zijn verschillende landelijk gedefinieerde metadatatypen. Voorbeelden hiervan zijn Publiccode(vanuit de softwarecatalogus), DCAT(vanuit Open Overheid) en WOO-categorieën (vanuit KOOP). Als je wilt publiceren voor een elders vastgesteld metadatatype, moet je dat eerst activeren. Dit gaat via de [directory](./directory.md) en dan onder de listing van de betreffende catalogus.

Nadat een metadata type is geactiveerd, kan je deze activeren voor jouw eigen catalogi, die gaat via het [catalogioverzicht](catalogi.md) onder het tabblad `Metadata`.

Nadat de metadata voor de catalogi is geactiveerd, kan je via [publicaties](../Users/publicaties.md) aanmaken, een publicatie aanmaken voor dit metadatatype.

## Metadata creëren

Je kunt ook zelf metadatadefinities toevoegen, bijvoorbeeld omdat er nog geen metadatabeschrijving beschikbaar is voor de publicatie die je wilt doen. In dat geval kun je bovenaan het metadata-overzicht op `+ Metadata toevoegen` klikken (rechts van de zoekbalk) om een metadatadefinitie aan te maken.

Nadat de metadatatype is aangemaakt kan je deze voorzien van eigenschappen.

## Eigenschappen defineren

Metadata definieerd objecten door de opgegeven properties, hiervoor wordt [json-schema](https://json-schema.org/) als standaard gebruikt. Metadata schema's geven  eigenschappen (`properties`) op die worden verwacht in de data van een publicatieobject. Ze beschrijven daarmee de spelregels over wat er in een publicatieobject moet moeten ziten. Is bijvoorbeeld in het schema van publiccode gedefineerd dat een publiccode-publicatie een property genaamd `repositoryUrl` heeft, dat dit een `string` is, een format `url` heeft en verplicht(`required`) is, dan moeten alle publicaties die aan dit schema refereren deze waarde bevaten. Doen ze dit niet dan worden ze geclassificeerd als ongeldig en zijn ze niet vindbaar.

De voornaamste manieren om een eigenschap te definiëren zijn:

* **type** (verplicht) Het type van de waarde, bijvoorbeeld `string` of `url`
* **format**: De indeling van de waarde Bijvoorbeeld `date-time` of `url`
* **requered**: Of de waarde verplicht aanwezig moet zijn
* **patern**: Een [regex](https://en.wikipedia.org/wiki/Regular_expression) definitie waar de waarde aan moet voldoen

Voor alle waardes, mogelijke invullingen en gevolgen kan je het beste een kijkje nemen in het [property object](https://conduction.stoplight.io/docs/open-catalogi/d0ci97hdxnctp-property).

## Archiveren

Metadata objecten zijn vormend voor de bewaartermijn van objecten, hierin volgen we [MDTO](https://www.nationaalarchief.nl/archiveren/mdto/begrippenlijst-metagegevensschema#collapse-102681) en de [VNG](https://vng.nl/sites/default/files/2020-02/selectielijst_20200214.pdf) selectie lijst. Met andere woorden:

* Iedere metadata heeft een waardeerding B,V of N (standaard N)
* Als de waardering V is dan moet het termijn zijn opgegeven als klasse
* De definering van klasse is
  * 1: 1 Jaar
  * 2: 5 Jaar
  * 3: 10 Jaar
  * 4: 20 Jaar
  * 5: 50 Jaar

Bij het aanmaken van een publicatie wordt vervolgens de `archive.date` gezet op de `createDate` + doorlooptijd van de klasse.
