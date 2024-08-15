<script setup>
import { navigationStore, directoryStore } from '../../store/store.js'
</script>

<template>
	<NcAppSidebar
		name="Listing"
		subname="Listing Summery">
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
			id="settings-tab"
			name="Configuratie"
			:order="1">
			<template #icon>
				<CogOutline :size="20" />
			</template>
			<NcCheckboxRadioSwitch type="switch">
				Beschickbaar maken voor mijn zoek opdrachten
			</NcCheckboxRadioSwitch>
			<NcCheckboxRadioSwitch type="switch">
				Standaard mee nemen in de beantwoording van mijn zoekopdrachten
			</NcCheckboxRadioSwitch>
		</NcAppSidebarTab>
		<NcAppSidebarTab v-if="directoryStore.listingItem.id && navigationStore.selected === 'directory'"
			id="metdata-tab"
			name="Publicatie typen"
			:order="2">
			<template #icon>
				<FileTreeOutline :size="20" />
			</template>
			Welke meta data typen zou u uit deze catalogus willen overnemen?
			<NcCheckboxRadioSwitch v-for="(metadataSingular, i) in metadata" :key="`${metadataSingular}${i}`" type="switch">
				{{ metadataSingular.title }}
			</NcCheckboxRadioSwitch>
		</NcAppSidebarTab>
	</NcAppSidebar>
</template>
<script>

import { NcAppSidebar, NcEmptyContent, NcButton, NcAppSidebarTab, NcCheckboxRadioSwitch } from '@nextcloud/vue'
import LayersOutline from 'vue-material-design-icons/LayersOutline.vue'
import Plus from 'vue-material-design-icons/Plus.vue'
import HelpCircleOutline from 'vue-material-design-icons/HelpCircleOutline.vue'
import CogOutline from 'vue-material-design-icons/CogOutline.vue'
import FileTreeOutline from 'vue-material-design-icons/FileTreeOutline.vue'

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
	},
	props: {
		listingItem: {
			type: Object,
			required: true,
		},
	},
	data() {
		return {
			metadata: [],
			listing: '',
		}
	},
	watch: {
		publicationItem: {
			handler(newVal) {
				console.log('test watch')
				console.log(newVal)
				if (newVal && newVal.id) {
					this.fetchMetaData()
				}
			},
			immediate: true, // Ensures the watcher runs when the component is created
		},
	},
	mounted() {
		this.fetchMetaData()
	},
	methods: {
		openLink(url, type = '') {
			window.open(url, type)
		},
		CopyMetadata() {
			this.loading = true
			// metadataStore.metaDataItem.title = 'KOPIE: ' + metadataStore.metaDataItem.title
			if (Object.keys(metadataStore.metaDataItem.properties).length === 0) {
				delete metadataStore.metaDataItem.properties
			}
			delete metadataStore.metaDataItem.id
			delete metadataStore.metaDataItem._id
			fetch(
				'/index.php/apps/opencatalogi/api/metadata',
				{
					method: 'POST',
					headers: {
						'Content-Type': 'application/json',
					},
					body: JSON.stringify(metadataStore.metaDataItem),
				},
			)
				.then((response) => {
					this.loading = false
					this.succes = true
					// Lets refresh the catalogiList
					metadataStore.refreshMetaDataList()
					response.json().then((data) => {
						metadataStore.setMetaDataItem(data)
					})
					navigationStore.setSelected('metaData')
					// Wait for the user to read the feedback then close the model
					const self = this
					setTimeout(function() {
						self.succes = false
						navigationStore.setDialog(false)
					}, 2000)
				})
				.catch((err) => {
					this.error = err
					this.loading = false
				})
		},
		fetchMetaData() {
			this.loading = true
			console.log('test1')
			console.log(directoryStore.listingItem)
			if (directoryStore.listingItem && Array.isArray(directoryStore.listingItem.metadata)) {
				directoryStore.listingItem?.metadata.forEach(metadataSingular => {
					console.log('test2')
					fetch(
						'/index.php/apps/opencatalogi/api/metadata?source=' + metadataSingular,
						{
							method: 'GET',
							headers: {
								'Content-Type': 'application/json',
							},
						},
					)
						.then((response) => {
							this.loading = false
							this.succes = true

							response.json().then(
								(data) => {
									if (data?.results[0] !== undefined) {
										this.metaData.push(data.results[0])
									}
									return data
								},
							)
						})
						.catch((err) => {
							this.error = err
							this.loading = false
						})
				})
			}
		},
	},
}
</script>
