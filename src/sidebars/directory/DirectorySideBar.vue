<script setup>
import { navigationStore, directoryStore, publicationTypeStore } from '../../store/store.js'
</script>

<template>
	<NcAppSidebar
		:name="directoryStore.listingItem?.title || 'Geen listing' "
		:subname="directoryStore.listingItem?.organization?.title">
		<NcEmptyContent v-if="!directoryStore.listingItem?.id || navigationStore.selected != 'directory'"
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
		<NcAppSidebarTab v-if="directoryStore.listingItem?.id && navigationStore.selected === 'directory'"
			id="detail-tab"
			name="Details"
			:order="1">
			<template #icon>
				<InformationSlabSymbol :size="20" />
			</template>
			<div class="container">
				<div v-if="directoryStore.listingItem.organization">
					<CertificateOutline class="orgCertIcon" :size="20" />
					<p>Deze organisatie is niet gevalideerd met een certificaat.</p>
				</div>
				<div v-if="!directoryStore.listingItem.organization">
					<CertificateOutline class="orgCertIcon" :size="20" />
					<p>Deze listing heeft geen organisatie.</p>
				</div>
				<div>
					<b>Samenvatting:</b>
					<span>{{ directoryStore.listingItem?.summary }}</span>
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
		<NcAppSidebarTab v-if="directoryStore.listingItem?.id && navigationStore.selected === 'directory'"
			id="settings-tab"
			name="Configuratie"
			:order="2">
			<template #icon>
				<CogOutline :size="20" />
			</template>
			<NcCheckboxRadioSwitch :checked.sync="directoryStore.listingItem.available" type="switch">
				Beschikbaar maken voor mijn zoekopdrachten
			</NcCheckboxRadioSwitch>
			<NcCheckboxRadioSwitch :checked.sync="directoryStore.listingItem.default" type="switch">
				Standaard mee nemen in de beantwoording van mijn zoekopdrachten
			</NcCheckboxRadioSwitch>

			<NcButton
				:disabled="syncLoading"
				type="primary"
				class="syncButton"
				@click="synDirectroy">
				<template #icon>
					<NcLoadingIcon v-if="syncLoading" :size="20" />

					<DatabaseSyncOutline v-if="!syncLoading" :size="20" />
				</template>
				Synchroniseren
			</NcButton>
		</NcAppSidebarTab>
		<NcAppSidebarTab v-if="directoryStore.listingItem?.id && navigationStore.selected === 'directory'"
			id="metdata-tab"
			name="Publicatietype"
			:order="3">
			<template #icon>
				<FileTreeOutline :size="20" />
			</template>
			Welke publicatietype zou u uit deze catalogus willen overnemen?
			<div v-if="!loading">
				<template v-for="(publicationType, i) in directoryStore.listingItem.publicationTypes">
					<div v-if="publicationType.owner" :key="`${publicationType}${i}`" class="publication-type-item">
						<Check :size="20" />
						{{ publicationType.title ?? publicationType.source ?? publicationType }}
					</div>
					<NcCheckboxRadioSwitch
						v-else
						:key="`${publicationType}${i}`"
						:checked="publicationType.listed"
						type="switch"
						@update:checked="togglePublicationType(publicationType)">
						{{ publicationType.title ?? publicationType.source ?? publicationType }}
					</NcCheckboxRadioSwitch>
				</template>
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
import DatabaseSyncOutline from 'vue-material-design-icons/DatabaseSyncOutline.vue'
import CogOutline from 'vue-material-design-icons/CogOutline.vue'
import FileTreeOutline from 'vue-material-design-icons/FileTreeOutline.vue'
import InformationSlabSymbol from 'vue-material-design-icons/InformationSlabSymbol.vue'
import CertificateOutline from 'vue-material-design-icons/CertificateOutline.vue'
import Check from 'vue-material-design-icons/Check.vue'

export default {
	name: 'DirectorySideBar',
	components: {
		NcAppSidebar,
		NcAppSidebarTab,
		NcEmptyContent,
		NcButton,
		NcCheckboxRadioSwitch,
		NcLoadingIcon,
	},
	data() {
		return {
			checkedPublicationTypeObject: {},
			switchedListing: false,
			listing: '',
			loading: false,
			syncLoading: false,
			publicationTypeLoading: false,
		}
	},
	computed: {
		listingItem() {
			return directoryStore.listingItem
		},
		checkedPublicationType() {
			return Object.assign({}, this.checkedPublicationTypeObject)
		},
	},
	watch: {
		checkedPublicationType: {
			handler(newValue, oldValue) {
				// Set new and old values to objects
				const newValueObject = Object.entries(newValue)
				let oldValueObject = Object.entries(oldValue)

				// Checks if listing is switched
				if (this.switchedListing === true) {
					oldValueObject = []
					this.switchedListing = false
				}

				newValueObject.map((value, idx) => {
					// If oldValueObject does not exist it means we have selected new listing and not updated a switch so we return
					if (!oldValueObject.length) return {}

					// Checks which switch has been updated by checking the old value of that switch
					if (value[1] !== oldValueObject[idx][1]) {

						// Sets publicationTypeUrl and shouldCopyPublicationType with the right values
						const publicationTypeUrl = value[0]
						const shouldCopyPublicationType = value[1]

						if (shouldCopyPublicationType === true) {
							this.copyPublicationType(publicationTypeUrl)
						} else if (shouldCopyPublicationType === false && publicationTypeUrl && publicationTypeStore.publicationTypeList.length > 0) {
							this.deletePublicationType(publicationTypeUrl)
						}
					}
					return {}

				})

			},
			deep: true,
		},
		listingItem: {
			handler(newValue) {
				if (newValue !== false && publicationTypeStore?.publicationTypeList) {
					this.loading = true
					this.switchedListing = true
					this.checkPublicationTypeSwitches()
				}
			},
			deep: true, // Track changes in nested objects
			immediate: true, // Run the handler immediately on initialization
		},
	},
	created() {
		publicationTypeStore.refreshPublicationTypeList()
		this.checkPublicationTypeSwitches()
	},
	methods: {
		openLink(url, type = '') {
			window.open(url, type)
		},
		getPublicationTypeId(publicationTypeUrl) {
			let publicationTypeId
			publicationTypeStore.publicationTypeList.forEach((publicationTypeItem) => {
				if (publicationTypeUrl === publicationTypeItem.source) {
					publicationTypeId = publicationTypeItem.id
				}
			})
			return publicationTypeId
		},
		checkPublicationTypeSwitches() {
			if (Array.isArray(directoryStore?.listingItem?.publicationType)) {
				directoryStore.listingItem.publicationType.forEach((publicationTypeUrl) => {
					// Check if the publicationType URL exists in the publicationTypeStore.publicationTypeList
					const exists = publicationTypeStore.publicationTypeList.some(publicationType => publicationType.source === publicationTypeUrl)
					// Update the checkedPublicationType reactive state
					this.$set(this.checkedPublicationTypeObject, publicationTypeUrl, exists)
				})
			}
			this.loading = false
		},
		copyPublicationType(publicationTypeUrl) {
			this.loading = true

			fetch(
				publicationTypeUrl,
				{
					method: 'GET',
				},
			)
				.then((response) => {
					publicationTypeStore.refreshPublicationTypeList()
					response.json().then((data) => {
						const publicationTypeSources = publicationTypeStore.publicationTypeList.map((publicationType) => publicationType.source)
						if (!publicationTypeSources.includes(data.source)) this.createPublicationType(data)
					})
					this.loading = false

				})
				.catch((err) => {
					this.error = err
					this.loading = false

				})
		},
		createPublicationType(data) {
			this.loading = true

			data.title = 'KOPIE: ' + data.title

			if (Object.keys(data.properties).length === 0) {
				delete data.properties
			}

			delete data.id
			delete data._id

			fetch(
				'/index.php/apps/opencatalogi/api/publication_types',
				{
					method: 'POST',
					headers: {
						'Content-Type': 'application/json',
					},
					body: JSON.stringify(data),
				},
			)
				.then((response) => {
					publicationTypeStore.refreshPublicationTypeList()
					this.loading = false

				})
				.catch((err) => {
					this.error = err
					this.loading = false

				})
		},
		deletePublicationType(publicationTypeUrl) {
			this.loading = true

			const publicationTypeId = this.getPublicationTypeId(publicationTypeUrl)

			fetch(
				`/index.php/apps/opencatalogi/api/publication_types/${publicationTypeId}`,
				{
					method: 'DELETE',
					headers: {
						'Content-Type': 'application/json',
					},
				},
			)
				.then(() => {
					this.loading = false

					publicationTypeStore.refreshPublicationTypeList()

				})
				.catch((err) => {
					this.error = err
					this.loading = false

				})
		},
		synDirectroy() {
			this.syncLoading = true
			fetch(
				`/index.php/apps/opencatalogi/api/listings/synchronise/${directoryStore.listingItem.id}`,
				{
					method: 'GET',
				},
			)
				.then(() => {
					directoryStore.refreshListingList()
					this.syncLoading = false
				})
				.catch((err) => {
					this.error = err
					this.syncLoading = false
				})
		},
		togglePublicationType(publicationType) {
			publicationType.listed = !publicationType.listed
			this.synchronizePublicationType(publicationType)
		},
		synchronizePublicationType(publicationType) {
			this.publicationTypeLoading = true
			fetch(
				'/index.php/apps/opencatalogi/api/publication_types/synchronise',
				{
					method: 'POST',
					headers: {
						'Content-Type': 'application/json',
					},
					body: JSON.stringify({
						listed: publicationType.listed,
						source: publicationType.source,
					}),
				},
			)
				.then(() => {
					this.publicationTypeLoading = false
				})
				.catch((err) => {
					this.error = err
					this.publicationTypeLoading = false
				})
		},
	},
}
</script>

<style>
.syncButton {
	margin-block-start: 15px;
	width: 100% !important;
}

.orgCertIcon {
    float: left;
    margin-block-start: 4px;
    margin-inline-end: 10px;
}
</style>
