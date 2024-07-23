# Design van de applicatie

Voor de backend volgen de we algemene spelregels van nextcloud, let daarbij in het bijzonder op:



Algemeen desigen en opbouw 

## Bijdrage
Als nextcloud app volgen we zowiezo de [next cloud publishing guide lines](https://docs.nextcloud.com/server/19/developer_manual/app/publishing.html#app-guidelines).

Daarbovenop hanteren we een aantal extra spelregeles
- **Features moeten zijn voorzien van gebruikers documentatie**
- **Backend code moet zijn voorzien van automatische tests**: Code de covaragde van het porject verlaagd word niet geacpeteerd, zie ook [php unit testing](https://docs.nextcloud.com/server/latest/developer_manual/server/unit-testing.html).
- **Backend code moet zuiver zijv**: Code mag *géén* linting errors bevaten
- **Frontend code moet zijn voorzien van automatische tests**: 
- **Frontend code moet zuiver zijn**: Code mag *géén* linting errors bevaten
- **Seperation of concern**: Voor zowel backend als frontend moet busnes logic zijn opgenomen in services. Controllers, Templates, Views, Components en Store mogen *géén* busnes logic bevatten
- **Vier ogen princiepe**: Pull requests moeten zijn beoordeeld door een andere developer dan de maker voordat ze worden geacepteerd


## Aanvullend keuzes en inrichting

### Gebruikers documentatie
We gebruiken gitbook voor de gebruikers documentatie, feateatures binnen de app zouden zo veel mogenlijk direct moeten doroverwijzen naar deze documentatie.

Hou bij het schrijven van de

### Storage en Typing
Om gegevens deelbaar te maken tussen de verschillende vue comopenten maken we gebruik van [statemanagment](https://vuejs.org/guide/scaling-up/state-management) waarbij we het Action, State, View patroon van vue zelf volgen. Omdat de applicatie ingewikkelder begint te worden stappen we daarbij over van [simple state managment](https://vuejs.org/guide/scaling-up/state-management#simple-state-management-with-reactivity-api) naar [Pinia](https://pinia.vuejs.org/), de door vue zelf geadviseerde opvolger van [vuex](https://vuejs.org/guide/scaling-up/state-management#pinia).

Omdat Pinia vanuit zichzelf al typing ondersteund en daarop testbaar is vervalt daarmee ook de noodzaak om in de voorkant te werken met typescript, de ontwikkeling daarvan is dan ook gestopt. 

### Modals

- only one modal may be active at all times
- modals should be abstract and reachable form anywhere
- modals should be places in te src/modals folder
- modals should be triggerd through the state
- modals schould be importerd through /src/modals/Modals.vue

## Views

- Views must have the same file name as the exported name and is a correlation to the map the file is in. 
    - For example, if the file is a detail page, and it is in the directory `publications` the file must be named `PublicationDetail.vue`.