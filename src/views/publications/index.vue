<template>
    <NcContent appName="opencatalog">
        <MainMenu selected="publications" />
        <NcAppContent>
            <template #list>
                <PublicationList @activePublication="updateActivePublication" @activePublicationId="updateActivePublicationId" />
            </template>
            <template #default>                
                <NcEmptyContent name="Geen publicatie" description="Nog geen publicaite geselecteerd" v-if="activePublication !== true" >
                    <template #icon>
                        <ListBoxOutline />
                    </template>
                    <template #action>
                    </template>
                </NcEmptyContent>
                <div v-if="activePublication === true">
                    <h1>Input voor OpenCatalogi</h1>
                    <p>
                        Dit is een selectie van high-value datasets in DCAT-AP 2.0 standaard x
                    </p>    
                    <ul>
                        <li><b>created:</b> 2020/04/07</li>
                        <li><b>updated:</b> 2020/12/29</li>
                        <li><b>identifier:</b> https://catalogus-rotterdam.dataplatform.nl/dataset/voorlopige-energielabels-met-bag-kenmerken</li>
                        <li><b>creator:</b> Gemeente Rotterdam</li>
                        <li><b>license:</b> CC0 1.0</li>
                        <li><b>keywords:</b> EI,
                            EP,
                            SODA,
                            energie,
                            energieindex,
                            energielabel,
                            energielabels,
                            energieprestatie,
                            kennisloods soda,
                            voorlopig energielabel</li>
                    </ul>

                    <div style="background-color: gray;">


                    {
                    "@context": "https://www.w3.org/ns/dcat2",
                    "@type": "dcat:Catalog",
                    "dct:title": "Input voor OpenCatalogi",
                    "dct:description": "Dit is een selectie van high-value datasets in DCAT-AP 2.0 standaard x",
                    "dcat:dataset": [
                        {
                        "@context": "https://www.w3.org/ns/dcat2",
                        "@type": "dcat:Dataset",
                        "dct:title": "Voorlopige energielabels met BAG-kenmerken",
                        "dct:description": "Rotterdamse adressen met een voorlopig energelabel volgens EP-Online, aangevuld met BAG-sleutels en een locatie.Â ",
                        "dct:update/modification date": "2020/12/29",
                        "dct:release date": "2020/04/07",
                        "dct:identifier": "https://catalogus-rotterdam.dataplatform.nl/dataset/voorlopige-energielabels-met-bag-kenmerken",
                        "dct:creator": "Rotterdam",
                        "dct:publisher": {
                            "@type": "foaf:Organization",
                            "foaf:name": "Rotterdam"
                        },
                        "dct:license": "CC0 1.0",
                        "dct:keyword/tag": [
                            "EI",
                            "EP",
                            "SODA",
                            "energie",
                            "energieindex",
                            "energielabel",
                            "energielabels",
                            "energieprestatie",
                            "kennisloods soda",
                            "voorlopig energielabel"
                        ],
                        "dct:spatial": "[55500,428647,101033,447000]",
                        "dcat:contactPoint": {
                            "@type": "vcard:Contact",
                            "vcard:fn": "gemeente Rotterdam, Stadsontwikkeling, SODA",
                            "vcard:hasEmail": "dataSO@rotterdam.nl"
                        },
                        "prov:qualifiedAttribution": {
                            "@type": "prov:Attribution",
                            "responsible": {
                            "@type": "Organization",
                            "name": "gemeente Rotterdam, Stadsontwikkeling, SODA",
                            "email": "dataSO@rotterdam.nl"
                            },
                            "role": {
                            "@type": "Role",
                            "name": "beheerder"
                            }
                        },
                        "dct:accrualPeriodicity": "onregelmatig",
                        "dcat.distribution": [
                            {
                            "@context": "https://www.w3.org/ns/dcat2",
                            "@type": "dcat:Distribution",
                            "dct:title": "voorlopige energielabels met BAG-kenmerken ",
                            "dct:description": "ESRI feature layer met voorlopige energielabels met BAG-kenmerken, per addresseerbaar object. Op basis van de gegevens voor december 2019 van EP-online.",
                            "dct:license": "CC0 1.0",
                            "dct:format": "API",
                            "dcat:byteSize": "Onbekend",
                            "dct:issued": "29-12-2020",
                            "dct:modified": "30 december 2020, 17:09 (UTC+01:00)",
                            "dcat:accessURL": "https://services.arcgis.com/zP1tGdLpGvt2qNJ6/arcgis/rest/services/Voorlopige_Energielabels_BAG/FeatureServer",
                            "dcat:downloadURL": "https://services.arcgis.com/zP1tGdLpGvt2qNJ6/arcgis/rest/services/Voorlopige_Energielabels_BAG/FeatureServer"
                            },
                            {
                            "@context": "https://www.w3.org/ns/dcat2",
                            "@type": "dcat:Distribution",
                            "dct:title": "voorlopige energielabels met BAG-kenmerken ",
                            "dct:description": "OGC:Geopackage met voorlopige energielabels met BAG-kenmerken, per addresseerbaar object. Op basis van de gegevens voor december 2019 van EP-online.",
                            "dct:license": "CC0 1.0",
                            "dct:format": "OGC:GPKG",
                            "dcat:byteSize": "Onbekend",
                            "dct:issued": "29-12-2020",
                            "dct:modified": "30 december 2020, 17:10 (UTC+01:00)",
                            "dcat:accessURL": "file:///P:/Geo_Data/SO/SODA/Data/Energielabels/Data/VoorlopigeLabels/GPKG/Voorlopige-labels-december-2019.gpkg",
                            "dcat:downloadURL": "P:\\Geo_Data\\SO\\SODA\\Data\\Energielabels\\Data\\VoorlopigeLabels\\GPKG\\Voorlopige-labels-december-2019.gpkg"
                            },
                            {
                            "@context": "https://www.w3.org/ns/dcat2",
                            "@type": "dcat:Distribution",
                            "dct:title": "voorlopige energielabels met BAG-kenmerken ",
                            "dct:description": "ESRI File Geodatabase met voorlopige energielabels met BAG-kenmerken, per addresseerbaar object. Op basis van de gegevens voor december 2019 van EP-online.",
                            "dct:license": "CC0 1.0",
                            "dct:format": "FGDB",
                            "dcat:byteSize": "Onbekend",
                            "dct:issued": "29-12-2020",
                            "dct:modified": "30 december 2020, 17:11 (UTC+01:00)",
                            "dcat:accessURL": "file:///P:/Geo_Data/SO/SODA/Data/Energielabels/Data/VoorlopigeLabels/GDB/Voorlopige-labels-december-2019.gdb",
                            "dcat:downloadURL": "P:\\Geo_Data\\SO\\SODA\\Data\\Energielabels\\Data\\VoorlopigeLabels\\GDB\\Voorlopige-labels-december-2019.gdb"
                            },
                            {
                            "@context": "https://www.w4.org/ns/dcat2",
                            "@type": "dcat:Distribution",
                            "dct:title": "voorlopige energielabels met BAG-kenmerken ",
                            "dct:description": "ESRI Shape files met voorlopige energielabels met BAG-kenmerken, per addresseerbaar object. Op basis van de gegevens voor december 2019 van EP-online.",
                            "dct:license": "CC0 1.0",
                            "dct:format": "SHP",
                            "dcat:byteSize": "Onbekend",
                            "dct:issued": "29-12-2020",
                            "dct:modified": "30 december 2020, 17:11 (UTC+01:00)",
                            "dcat:accessURL": "file:///P:/Geo_Data/SO/SODA/Data/Energielabels/Data/VoorlopigeLabels/GDB/Voorlopige-labels-december-2019.gdb",
                            "dcat:downloadURL": "P:\\Geo_Data\\SO\\SODA\\Data\\Energielabels\\Data\\VoorlopigeLabels\\SHP\\*.shp"
                            },
                            {
                            "@context": "https://www.w5.org/ns/dcat2",
                            "@type": "dcat:Distribution",
                            "dct:title": "productbeschrijving: voorlopige energielabels met BAG kenmerken",
                            "dct:description": "productbeschrijving: voorlopige energielabels met BAG kenmerken",
                            "dct:license": "CC0 1.0",
                            "dct:format": "HTML",
                            "dcat:byteSize": "Onbekend",
                            "dct:issued": "7-4-2020",
                            "dct:modified": "30 december 2020, 17:12 (UTC+01:00)",
                            "dcat:accessURL": "https://rio.rotterdam.nl/Project/SODAStadsOntwikkelingData/Pages/ThoZIFnen0KbY6eBjPvh-A",
                            "dcat:downloadURL": "https://rio.rotterdam.nl/Project/SODAStadsOntwikkelingData/Pages/ThoZIFnen0KbY6eBjPvh-A"
                            }
                        ]
                        }
                    ]
                    }
                </div>
                </div>
				<PublicationDetails :publicationId="activePublicationId" v-if="activePublication === true && activePublicationId" />
            </template>
        </NcAppContent>
        <!-- <ZaakSidebar /> -->
    </NcContent>
</template>

<script>
import { NcAppContent, NcContent, NcEmptyContent } from '@nextcloud/vue';
import MainMenu from '../../navigation/MainMenu.vue';
import PublicationList from './list.vue';
import PublicationDetails from './details.vue';
import ListBoxOutline from 'vue-material-design-icons/ListBoxOutline';

export default {
    name: 'publicationIndex',
    components: {
        NcContent,
        NcAppContent,
        NcEmptyContent,
        MainMenu,
        ListBoxOutline,
        PublicationList,
        PublicationDetails
    },
	data() {
		return {
			activePublication: false,
			activePublicationId: '',
		}
	},
	methods: {
		updateActivePublication(variable) {
			this.activePublication = variable
		},
		updateActivePublicationId(variable) {
			this.activePublicationId = variable
		}
	}
}
</script>
