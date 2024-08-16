<script setup>
import { navigationStore, directoryStore, metadataStore } from '../../store/store.js'
</script>

<template>
	<NcAppSidebar
		:name="directoryStore.listingItem?.title || 'Geen listing' "
		:subname="directoryStore.listingItem?.organisation?.title">
		<NcEmptyContent v-if="!directoryStore.listingItem.id || navigationStore.selected != 'directory'"
			class="detailContainer"
			name="Geen listing"
			description="Nog geen listing geselecteerd, listings kan je ontdekken via (externe) directories.">
			<template #icon>
				<LayersOutline />
			</template>
			<template #action>
				<NcButton type="primary" @click="navigationStore.setModal('addDirectory')">
					<template #icon>
						<Plus :size="20" />
					</template>
					Directory inlezen
				</NcButton>
				<NcButton @click="openLink('https://conduction.gitbook.io/opencatalogi-nextcloud/beheerders/directory', '_blank')">
					<template #icon>
						<HelpCircleOutline :size="20" />
					</template>
					Meer informatie over de directory
				</NcButton>
			</template>
		</NcEmptyContent>
		<NcAppSidebarTab v-if="directoryStore.listingItem.id && navigationStore.selected === 'directory'"
			id="detail-tab"
			name="Details"
			:order="1">
			<template #icon>
				<InformationSlabSymbol :size="20" />
			</template>
			<div class="container">
				<div>
					<b>Samenvatting:</b>
					<span>{{ directoryStore.listingItem?.summery }}</span>
				</div>
				<div>
					<b>Status:</b>
					<span>{{ directoryStore.listingItem?.status }}</span>
				</div>
				<div>
					<b>Last synchronysation:</b>
					<span>{{ directoryStore.listingItem?.lastSync }}</span>
				</div>
				<div>
					<b>Directory:</b>
					<span>{{ directoryStore.listingItem?.directory }}</span>
				</div>
				<div>
					<b>Zoeken:</b>
					<span>{{ directoryStore.listingItem?.search }}</span>
				</div>
				<div>
					<b>Beschrijving:</b>
					<span>{{ directoryStore.listingItem?.description }}</span>
				</div>
			</div>
		</NcAppSidebarTab>
		<NcAppSidebarTab v-if="directoryStore.listingItem.id && navigationStore.selected === 'directory'"
			id="settings-tab"
			name="Configuratie"
			:order="2">
			<template #icon>
				<CogOutline :size="20" />
			</template>
			<NcCheckboxRadioSwitch :checked.sync="directoryStore.listingItem.available" type="switch">
				Beschickbaar maken voor mijn zoek opdrachten
			</NcCheckboxRadioSwitch>
			<NcCheckboxRadioSwitch :checked.sync="directoryStore.listingItem.default" type="switch">
				Standaard mee nemen in de beantwoording van mijn zoekopdrachten
			</NcCheckboxRadioSwitch>
		</NcAppSidebarTab>
		<NcAppSidebarTab v-if="directoryStore.listingItem.id && navigationStore.selected === 'directory'"
			id="metdata-tab"
			name="Publicatie typen"
			:order="3">
			<template #icon>
				<FileTreeOutline :size="20" />
			</template>
			Welke meta data typen zou u uit deze catalogus willen overnemen?
            <div v-if="!loading">
			    <NcCheckboxRadioSwitch :disabled="loading" v-for="(metadataSingular, i) in directoryStore.listingItem.metadata" :checked.sync="checkedMetadata[metadataSingular]" :key="`${metadataSingular}${i}`" type="switch">
				    {{ metadataSingular }}
                </NcCheckboxRadioSwitch>
            </div>
			<NcLoadingIcon v-if="loading" :size="20" />
		</NcAppSidebarTab>
	</NcAppSidebar>
</template>
<script>

import { NcAppSidebar, NcEmptyContent, NcButton, NcAppSidebarTab, NcCheckboxRadioSwitch, NcLoadingIcon } from '@nextcloud/vue'
import LayersOutline from 'vue-material-design-icons/LayersOutline.vue'
import Plus from 'vue-material-design-icons/Plus.vue'
import HelpCircleOutline from 'vue-material-design-icons/HelpCircleOutline.vue'
import CogOutline from 'vue-material-design-icons/CogOutline.vue'
import FileTreeOutline from 'vue-material-design-icons/FileTreeOutline.vue'
import InformationSlabSymbol from 'vue-material-design-icons/InformationSlabSymbol.vue'

export default {
	name: 'DirectorySideBar',
	components: {
		NcAppSidebar,
		NcAppSidebarTab,
		NcEmptyContent,
		NcButton,
		NcCheckboxRadioSwitch,
		// Icons
		LayersOutline,
		Plus,
		HelpCircleOutline,
		CogOutline,
		FileTreeOutline,
		InformationSlabSymbol,
	},
	props: {
		listingItem: {
			type: Object,
			required: true,
		},
	},
	data() {
		return {
			checkedMetadata: {},
			listing: '',
            loading: false
		}
	},
	// created() {
    //     if (directoryStore?.listingItem?.metadata !== undefined) {
	// 	    this.checkMetadataSwitches()
    //     }
	// },
    watch: {
        checkedMetadata: {
            handler(newValue, oldValue) {
                const metadataUrl = Object.entries(newValue)[0][0]
                const shouldCopyMetadata = Object.entries(newValue)[0][1]
                this.loading = true
                if (shouldCopyMetadata === true) {
                    this.copyMetadata(metadataUrl)
                } else if (shouldCopyMetadata === false) {
                    console.log('deletemetadata')
                    this.deleteMetadata(metadataUrl)
                }
                this.checkMetadataSwitches();
                this.loading = false
            },
            deep: true
        },
        'directoryStore.listingItem': {
            handler(newValue, oldValue) {
                if (directoryStore?.listingItem !== false) {
                    this.loading = true
                    this.checkMetadataSwitches();
                }
            },
            deep: true, // If listingItem has nested objects and you want to track changes in them as well
            immediate: true // Optionally, run the handler immediately on initialization
        },
    },
	methods: {
		openLink(url, type = '') {
			window.open(url, type)
		},
        deleteMetadata(metadataUrl) {
            console.log('deleteMetadata')
            metedataId = getMetadataId(metadataUrl)

            fetch(
				`/index.php/apps/opencatalogi/api/catalogi/${catalogiStore.catalogiItem.id}`,
				{
					method: 'DELETE',
					headers: {
						'Content-Type': 'application/json',
					},
				},
			)
				.then((response) => {
					this.loading = false
				})
				.catch((err) => {
					this.error = err
					this.loading = false
				})
        },
        getMetadataId(metadataUrl) {
            console.log('getMetadataId')
            directoryStore.listingItem.metadata.forEach((metadataItem) => {
                const isEqual = metadataUrl.some(metadataUrl === metadataItem.source);
                if (isEqual) {
                    return metadataItem.id
                }
            })
        },
        checkMetadataSwitches() {
            console.log('createMetadata')
            console.log('refresh');
            metadataStore.refreshMetaDataList()
            console.log('refresh done');

            console.log('check the switches');
            if (directoryStore?.listingItem?.metadata !== undefined) {
                directoryStore.listingItem.metadata.forEach((metadataItem) => {
                    const exists = metadataStore.metaDataList.some(metaData => metaData.source === metadataItem.source);
                    this.$set(this.checkedMetadata, metadataItem.source, exists);
                });
            }
            console.log('check the switches done');
            
            this.loading = false
            console.log('set loading false');

        },
		copyMetadata(metadataUrl) {
            console.log('copyMetadata')
			fetch(
				metadataUrl,
				{
					method: 'GET'
				},
			)
				.then((response) => {
					// Lets refresh the catalogiList
					metadataStore.refreshMetaDataList()
					response.json().then((data) => {
						this.createMetadata(data)
					})
					this.loading = false
				})
				.catch((err) => {
					this.error = err
					this.loading = false
				})
		},
		createMetadata(data) {
            console.log('createMetadata')
			data.title = 'KOPIE: ' + data.title

			if (Object.keys(data.properties).length === 0) {
				delete data.properties
			}

			delete data.id
			delete data._id

			fetch(
				'/index.php/apps/opencatalogi/api/metadata',
				{
					method: 'POST',
					headers: {
						'Content-Type': 'application/json',
					},
					body: JSON.stringify(data),
				},
			)
				.then((response) => {
				})
				.catch((err) => {
					this.error = err
					this.loading = false
				})
		},
	},
}
</script>
