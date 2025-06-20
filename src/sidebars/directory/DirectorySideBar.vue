<script setup>
import { navigationStore, objectStore } from '../../store/store.js'
</script>

<template>
	<NcAppSidebar
		:name="objectStore.getActiveObject('listing')?.title || 'Geen listing' "
		:subname="objectStore.getActiveObject('listing')?.organization?.title">
		<NcEmptyContent v-if="!objectStore.getActiveObject('listing')?.id || navigationStore.selected != 'directory'"
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
		<NcAppSidebarTab v-if="objectStore.getActiveObject('listing')?.id && navigationStore.selected === 'directory'"
			id="detail-tab"
			name="Details"
			:order="1">
			<template #icon>
				<InformationSlabSymbol :size="20" />
			</template>
			<div class="container">
				<div v-if="objectStore.getActiveObject('listing').organization">
					<CertificateOutline class="orgCertIcon" :size="20" />
					<p>Deze organisatie is niet gevalideerd met een certificaat.</p>
				</div>
				<div v-if="!objectStore.getActiveObject('listing').organization">
					<CertificateOutline class="orgCertIcon" :size="20" />
					<p>Deze listing heeft geen organisatie.</p>
				</div>
				<div>
					<b>Samenvatting:</b>
					<span>{{ objectStore.getActiveObject('listing')?.summary }}</span>
				</div>
				<div>
					<b>Status:</b>
					<span>{{ objectStore.getActiveObject('listing')?.status }}</span>
				</div>
				<div>
					<b>Last synchronysation:</b>
					<span>{{ objectStore.getActiveObject('listing')?.lastSync }}</span>
				</div>
				<div>
					<b>Directory:</b>
					<span>{{ objectStore.getActiveObject('listing')?.directory }}</span>
				</div>
				<div>
					<b>Zoeken:</b>
					<span>{{ objectStore.getActiveObject('listing')?.search }}</span>
				</div>
				<div>
					<b>Beschrijving:</b>
					<span>{{ objectStore.getActiveObject('listing')?.description }}</span>
				</div>
			</div>
		</NcAppSidebarTab>
		<NcAppSidebarTab v-if="objectStore.getActiveObject('listing')?.id && navigationStore.selected === 'directory'"
			id="settings-tab"
			name="Configuratie"
			:order="2">
			<template #icon>
				<CogOutline :size="20" />
			</template>
			<NcCheckboxRadioSwitch :checked.sync="objectStore.getActiveObject('listing').available" type="switch">
				Beschikbaar maken voor mijn zoekopdrachten
			</NcCheckboxRadioSwitch>
			<NcCheckboxRadioSwitch :checked.sync="objectStore.getActiveObject('listing').default" type="switch">
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
		<NcAppSidebarTab v-if="objectStore.getActiveObject('listing')?.id && navigationStore.selected === 'directory'"
			id="metdata-tab"
			name="Publicatietype"
			:order="3">
			<template #icon>
				<FileTreeOutline :size="20" />
			</template>
			Welke publicatietype zou u uit deze catalogus willen overnemen?
			<div v-if="!loading">
				<template v-for="(publicationType, i) in objectStore.getActiveObject('listing').publicationTypes">
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
			return objectStore.getActiveObject('listing')
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
						} else if (shouldCopyPublicationType === false && publicationTypeUrl && objectStore.getCollection('publication_type').results.length > 0) {
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
				if (newValue !== false && objectStore.getCollection('publication_type').results) {
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
		objectStore.fetchCollection('publication_type')
		this.checkPublicationTypeSwitches()
	},
	methods: {
		openLink(url, type = '') {
			window.open(url, type)
		},
		getPublicationTypeId(publicationTypeUrl) {
			let publicationTypeId
			objectStore.getCollection('publication_type').results.forEach((publicationTypeItem) => {
				if (publicationTypeUrl === publicationTypeItem.source) {
					publicationTypeId = publicationTypeItem.id
				}
			})
			return publicationTypeId
		},
		checkPublicationTypeSwitches() {
			if (Array.isArray(objectStore.getActiveObject('listing')?.publicationType)) {
				objectStore.getActiveObject('listing').publicationType.forEach((publicationTypeUrl) => {
					// Check if the publicationType URL exists in the publicationTypeStore.publicationTypeList
					const exists = objectStore.getCollection('publication_type').results.some(publicationType => publicationType.source === publicationTypeUrl)
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
					objectStore.fetchCollection('publication_type')
					response.json().then((data) => {
						const publicationTypeSources = objectStore.getCollection('publication_type').results.map((publicationType) => publicationType.source)
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

			objectStore.createObject('publication_type', data)
				.then((response) => {
					objectStore.fetchCollection('publication_type')
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

			objectStore.deleteObject('publication_type', publicationTypeId)
				.then(() => {
					this.loading = false

					objectStore.fetchCollection('publication_type')

				})
				.catch((err) => {
					this.error = err
					this.loading = false

				})
		},
		synDirectroy() {
			this.syncLoading = true
			fetch(
				`/index.php/apps/opencatalogi/api/listings/synchronise/${objectStore.getActiveObject('listing').id}`,
				{
					method: 'GET',
				},
			)
				.then(() => {
					objectStore.fetchCollection('listing')
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
