# Metadata
Iedere publicatie op open catalogi hoort bij een bepaald metadata type, dit metadata type kan door u zelf gedefineerd zijn over overgenomen van een andere catalogussen. Een metadata beschrijft een publicatie in doel, archivering en inhoudenlijk gegevens. Het modeleleerd dus als het ware publicaties en maakt publicaties daarmee leesbaar en interpeteerdbaar maar vormd in de praktijk ook de meest basale zoek mogenlijkheid "doe mij alle publicaite van het type woo verzoek"

>  Metadata zijn onderdeel van de (Open Catalogi Standaard)[] en gebaseerd op het [metadata object](https://conduction.stoplight.io/docs/open-catalogi/92e81a078982b-metadata). Zij beschrijven de data in publicaites aan de hand van het voor defineren van properties, die zijn gedefineerd in het [property object](https://conduction.stoplight.io/docs/open-catalogi/d0ci97hdxnctp-property)


## Metadata overnemen
Er zijn verschillende landelijk gedefineerde metadata typen, voorbeelden hiervan zijn publiccode (vanuit de software catalogus), DCAT (vanuit open overheid) en Woo categorien (Vanuit Koop). Als je wilt publiceren voor een elders vastgesteld metadata type moet je die eerst activeren. Dit gaat via de [directory](directory.md) en dan onder de listing van de betreffende catalogus.

Nadat een metadata type is geactiveerd kan je deze activeren voor je eigen catalogi, die gaat via het [catalogi overzicht](catalogi.md) onder het tabblad metadata.

Nadat de metadata voor de catalogi is geactiveerd kan je via [publicaties](../gebruikers/publicaties.md) aanmaken een publicatie aanmaken voor dit metadata type.

## Metadata creeren
Je kan ook zelf metadata definities toevoegen, bijvoorbeeld om dat er nog geen metadata beschrijving beschickbaar is voor de publicatie die je wilt doen. In dat geval kan je bovenaan het metadata overzicht op `+ Metadata toevoegen` klikken (rechts van de zoeken balk) om een metadata definitie aan te maken.

Nadat de metadata type is aangemaakt ka je deze voorzien van eigenschappen.

Als laaste moet je de metadata type activeren via het [catalogi overzicht](catalogi.md) onder het tabblad metadata.

## Eigenschappen defineren
Metedata defineerd objecten door het opgeven properties, hiervoor wordt [json-schema](https://json-schema.org/) als standaard gebruikt. metadata schema's geven  eigenschappen (`properties`) op die worden verwacht in de data van een publicatie objects. Ze beschrijven daarmee de spelregels over wat er in een publicaie object moet moeten ziten. Is bijvoorbeeld in het schema van publiccode gedefineerd dat een publiccode publicate een property genaamd repositoryUrl heeft, dat dit een string is een format url heeft en verplicht is dan moeten alle publicaites die aan dit schema referen deze waarde bevaten. Doen ze dit niet dan worden ze geclacificeerd als ongeldig en zijn ze niet vindbaar.

De voornaamste manieren om een eigenschap te definiren zijn
- **type** (verplicht) Het type van de waarde, bijvoorbeeld string of url
- **format**: De indeling van de waarde Bijvoorbeeld date-time of url
- **requered**: Of de waarde verplicht aanwezig moet zijn
- **patern**: Een [regex](https://en.wikipedia.org/wiki/Regular_expression) definitie waar de waarde aan moet voldoen 

Voor alle waardes, mogenlijke en invullingen en gevolgen kan je het beste een kijkje nemen in in het [property object](https://conduction.stoplight.io/docs/open-catalogi/d0ci97hdxnctp-property).


## Archiveren
Metadata objecten zijn vormend voor de bewaartermijn van objecten, hierin volgen we [MDTO](https://www.nationaalarchief.nl/archiveren/mdto/begrippenlijst-metagegevensschema#collapse-102681) en de [VNG](chrome-extension://efaidnbmnnnibpcajpcglclefindmkaj/https://vng.nl/sites/default/files/2020-02/selectielijst_20200214.pdf) selectie lijst. Met andere woorden:

- Iedere metadata heeft een waardeerding B,V of N (standaard N)
- Als de waardering V is dan moet het termijn zijn opgegeven als klasse
- De definering van klasse is
    - 1: 1 Jaar
    - 2: 5 Jaar
    - 3: 10 Jaar
    - 4: 20 Jaar
    - 5: 50 Jaar

Bij het aanmaken van een publicatie word vervolgens de archive.date gezet op de createDate + doorlooptijd van de klasse
