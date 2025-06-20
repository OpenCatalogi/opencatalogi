<script setup>
import { navigationStore, objectStore } from '../../store/store.js'
</script>

<template>
	<NcAppSidebar
		name="Snelle start"
		class="dashboardSidebar"
		subname="Schakel snel naar waar u nodig bent">
		<NcAppSidebarTab id="search-tab" name="Zoeken" :order="1">
			<template #icon>
				<Magnify :size="20" />
			</template>
			Zoek snel in het voor uw beschikbare federatieve netwerk
			<NcTextField class="searchField"
				:value="objectStore.getSearchTerm('search')"
				label="Zoeken" />
			<NcNoteCard v-if="objectStore.getError('search')" type="error">
				<p>{{ objectStore.getError('search') }}</p>
			</NcNoteCard>
		</NcAppSidebarTab>

		<NcAppSidebarTab id="publication-creation-tab" name="Publicatie aanmaken" :order="2">
			<template #icon>
				<Plus :size="20" />
			</template>
			<h3 style="margin-top: 0;">
				Snel Publicatie aanmaken
			</h3>

			<div class="formContainer">
				<NcSelect v-bind="catalogi"
					v-model="catalogi.value"
					style="min-width: unset; width: 100%;"
					input-label="Catalogus*"
					:loading="catalogiLoading"
					:disabled="catalogiLoading || loading" />
				<NcSelect v-bind="filteredPublicationTypeOptions"
					v-model="publicationType.value"
					style="min-width: unset; width: 100%;"
					input-label="Publicatietype*"
					:loading="publicationTypeLoading"
					:disabled="publicationTypeLoading || loading || !catalogi.value?.id" />
				<NcTextField :disabled="loading"
					label="Titel*"
					:value.sync="publicationItem.title" />
				<NcTextField :disabled="loading"
					label="Samenvatting*"
					:value.sync="publicationItem.summary" />
			</div>
			<NcButton :disabled="!publicationItem.title || !publicationItem.summary || !catalogi.value?.id || !publicationType.value?.id || loading"
				style="margin-top: 0.5rem;"
				type="primary"
				class="addButton"
				@click="addPublication()">
				<template #icon>
					<NcLoadingIcon v-if="loading" :size="20" />
					<Plus v-if="!loading" :size="20" />
				</template>
				Toevoegen
			</NcButton>

			<div v-if="success !== null || error">
				<NcNoteCard v-if="success" type="success">
					<p>Publicatie succesvol toegevoegd</p>
				</NcNoteCard>
				<NcNoteCard v-if="!success" type="error">
					<p>Er is iets fout gegaan bij het toevoegen van publicatie</p>
				</NcNoteCard>
				<NcNoteCard v-if="error" type="error">
					<p>{{ error }}</p>
				</NcNoteCard>
			</div>
		</NcAppSidebarTab>

		<NcAppSidebarTab id="settings-tab" name="Publicaties" :order="3">
			<template #icon>
				<ListBoxOutline :size="20" />
			</template>
			Welke publicaties vereisen uw aandacht?
			<NcListItem v-for="(publication, i) in objectStore.getCollection('publication').results"
				:key="`${publication}${i}`"
				:name="publication.title"
				:bold="false"
				:force-display-actions="true"
				:active="objectStore.getActiveObject('publication')?.id === publication?.id"
				:details="publication?.status">
				<template #icon>
					<ListBoxOutline :class="objectStore.getActiveObject('publication')?.id === publication?.id && 'selectedZaakIcon'"
						disable-menu
						:size="44" />
				</template>
				<template #subname>
					{{ publication?.description }}
				</template>
				<template #actions>
					<NcActionButton close-after-click @click="objectStore.setActiveObject('publication', publication); navigationStore.setSelected('publication');">
						<template #icon>
							<ListBoxOutline :size="20" />
						</template>
						Bekijken
					</NcActionButton>
					<NcActionButton close-after-click @click="objectStore.setActiveObject('publication', publication); navigationStore.setModal('editPublication')">
						<template #icon>
							<Pencil :size="20" />
						</template>
						Bewerken
					</NcActionButton>
					<NcActionButton close-after-click @click="objectStore.setActiveObject('publication', publication); navigationStore.setDialog('publishPublication')">
						<template #icon>
							<Publish :size="20" />
						</template>
						Publiceren
					</NcActionButton>
					<NcActionButton close-after-click @click="objectStore.setActiveObject('publication', publication); navigationStore.setDialog('deletePublication')">
						<template #icon>
							<Delete :size="20" />
						</template>
						Verwijderen
					</NcActionButton>
				</template>
			</NcListItem>
			<NcNoteCard v-if="!objectStore.getCollection('publication').results?.length > 0" type="success">
				<p>Er zijn op dit moment geen publicaties die uw aandacht vereisen</p>
			</NcNoteCard>
		</NcAppSidebarTab>

		<NcAppSidebarTab id="share-tab" name="Bijlagen" :order="4">
			<template #icon>
				<FileOutline :size="20" />
			</template>
			Welke bijlagen vereisen uw aandacht?
			<NcListItem v-for="(attachment, i) in objectStore.getCollection('attachment').results"
				:key="`${attachment}${i}`"
				:name="attachment.title"
				:bold="false"
				:force-display-actions="true"
				:active="objectStore.getActiveObject('attachment')?.id === attachment?.id"
				:details="attachment?.status">
				<template #icon>
					<ListBoxOutline :class="objectStore.getActiveObject('attachment')?.id === attachment.id && 'selectedZaakIcon'"
						disable-menu
						:size="44" />
				</template>
				<template #subname>
					{{ attachment?.description }}
				</template>
				<template #actions>
					<NcActionButton close-after-click @click="objectStore.setActiveObject('attachment', attachment); navigationStore.setModal('editAttachment')">
						<template #icon>
							<Pencil :size="20" />
						</template>
						Bewerken
					</NcActionButton>
					<NcActionButton close-after-click @click="objectStore.setActiveObject('attachment', attachment); navigationStore.setDialog('publishAttachment')">
						<template #icon>
							<Publish :size="20" />
						</template>
						Publiceren
					</NcActionButton>
					<NcActionButton close-after-click @click="objectStore.setActiveObject('attachment', attachment); navigationStore.setDialog('deleteAttachment')">
						<template #icon>
							<Delete :size="20" />
						</template>
						Verwijderen
					</NcActionButton>
				</template>
			</NcListItem>
			<NcNoteCard v-if="!objectStore.getCollection('attachment').results?.length > 0" type="success">
				<p>Er zijn op dit moment geen bijlagen die uw aandacht vereisen</p>
			</NcNoteCard>
		</NcAppSidebarTab>
	</NcAppSidebar>
</template>
<script>

import {
	NcAppSidebar,
	NcAppSidebarTab,
	NcLoadingIcon,
	NcTextField,
	NcNoteCard,
	NcListItem,
	NcActionButton,
	NcSelect,
	NcButton,
} from '@nextcloud/vue'
import Magnify from 'vue-material-design-icons/Magnify.vue'
import ListBoxOutline from 'vue-material-design-icons/ListBoxOutline.vue'
import Plus from 'vue-material-design-icons/Plus.vue'
import FileOutline from 'vue-material-design-icons/FileOutline.vue'
import Pencil from 'vue-material-design-icons/Pencil.vue'
import Publish from 'vue-material-design-icons/Publish.vue'
import Delete from 'vue-material-design-icons/Delete.vue'

import { useFileSelection } from '../../composables/UseFileSelection.js'
import { ref } from 'vue'
import axios from 'axios'

import { Publication } from '../../entities/index.js'

const dropZoneRef = ref()
const { files, reset } = useFileSelection({ allowMultiple: false, dropzone: dropZoneRef })

export default {
	name: 'DashboardSideBar',
	components: {
		NcAppSidebar,
		NcAppSidebarTab,
		NcTextField,
		NcNoteCard,
		NcListItem,
		NcActionButton,
		// Icons
		Magnify,
		ListBoxOutline,
		FileOutline,
		Pencil,
		Publish,
		Delete,
	},
	data() {
		return {
			publications: false,
			attachments: false,
			publicationItem: {
				title: '',
				summary: '',
				reference: '',
				category: '',
				portal: '',
				license: '',
			},
			catalogiLoading: false,
			catalogiList: [],
			catalogi: {
				options: [],
				value: null,
			},
			publicationTypeLoading: false,
			publicationTypeList: [], // this is the entire dataset of publicationType
			publicationType: {},
			loading: false,
			error: null,
			success: null,
		}
	},
	computed: {
		/**
		 * Filters publicationType (Now known as Publication Type) based on the catalogi
		 */
		filteredPublicationTypeOptions() {
			if (!this.catalogi?.options?.length) return {}
			if (!this.catalogi?.value?.id) return {}
			if (!this.publicationTypeList?.length) return {}

			// step 1: get the selected catalogus from the catalogi dropdown
			const selectedCatalogus = objectStore.getCollection('catalogus').results
				.find((catalogus) =>
					(catalogus.id?.toString() || Symbol('catalogusId')) === (this.catalogi?.value.id?.toString() || Symbol('catalogiId')),
				)

			// step 2: get the full publicationType's from the publicationTypeIds
			const filteredPublicationType = this.publicationTypeList
				.filter((publicationType) => selectedCatalogus.publicationTypes.map(String).includes(publicationType.id.toString()))

			return {
				options: filteredPublicationType.map((publicationType) => ({
					id: publicationType.id,
					source: publicationType.source,
					label: publicationType.title,
				})),
			}
		},
	},
	mounted() {
		objectStore.fetchCollection('publication')
		objectStore.fetchCollection('attachment')
		this.fetchCatalogi()
		this.fetchPublicationType()
	},
	methods: {
		cleanup() {
			if (this.success === true) {
				this.publicationItem = {
					title: '',
					summary: '',
					reference: '',
					category: '',
					portal: '',
					license: '',
				}
				this.catalogi.value = []
				this.publicationType.value = []
			}
			this.error = null
		},
		fetchCatalogi() {
			this.catalogiLoading = true

			objectStore.fetchCollection('catalogus')
				.then(({ response, data }) => {
					this.catalogiList = data
					this.catalogi.options = this.catalogiList.map((catalog) => ({
						id: catalog.id,
						label: catalog.title,
					}))
					this.catalogi.value = null
					this.catalogiLoading = false
				})
				.catch((err) => {
					console.error(err)
					this.catalogiLoading = false
				})
		},
		fetchPublicationType() {
			this.publicationTypeLoading = true

			objectStore.fetchCollection('publication_type')
				.then(({ response, data }) => {
					this.publicationTypeList = data.results
					this.publicationTypeLoading = false
				})
				.catch((err) => {
					console.error(err)
					this.publicationTypeLoading = false
				})
		},
		addPublication() {
			this.loading = true
			this.error = false

			// create the publication
			objectStore.createObject('publication', {
				...this.publicationItem,
				catalog: this.catalogi.value.id,
				publicationType: this.publicationType.value.id,
			})
				.then(async (response) => {
					this.loading = false
					this.success = response.ok

					// if response is ok, and files exist, we add the attachment
					const publicationItem = new Publication(await response.json())
					if (response.ok && files.value) this.addAttachment(publicationItem)

					// Let's refresh the publicationList
					objectStore.fetchCollection('publication')
					// Wait for the user to read the feedback then close the model
					setTimeout(this.cleanup, 2000)
					if (!files.value && files.value?.length === 0) {
						setTimeout(this.success = null, 2000)
					}
				})
				.catch((err) => {
					this.error = err
					this.loading = false
					this.hasUpdated = false
				})
		},
		addAttachment(publicationItem) {
			this.loading = true
			this.errorMessage = false

			axios.post('/index.php/apps/opencatalogi/api/attachments', {
				published: null,
				summary: '',
				license: '',
				_file: files.value ? files.value[0] : '',
			}, {
				headers: {
					'Content-Type': 'multipart/form-data',
					// These headers are used to pass along some publication info to use as name for a Folder,
					// to store (attachments/) files in for that specific publication,
					'Publication-Id': publicationItem.id,
					'Publication-Title': publicationItem.title,
				},
			}).then((response) => {
				this.success = response.status
				reset()
				// Let's refresh the attachment list

				objectStore.fetchCollection('attachment')

				objectStore.updateObject('publication', publicationItem.id, {
					...publicationItem,
					attachments: [...publicationItem.attachments, response.data.id],
				})
					.then((response) => {
						this.success = response.ok
						this.loading = false

						// Let's refresh the publicationList
						objectStore.fetchCollection('publication')
						response.json().then((data) => {
							objectStore.setActiveObject('publication', data)
						})

					})
					.catch((err) => {
						this.error = err
						this.loading = false
					})

				objectStore.setActiveObject('attachment', response)

				// Wait for the user to read the feedback then close the model
				const self = this
				setTimeout(function() {
					self.success = null
				}, 2000)
			})
				.catch((err) => {
					this.error = err.response?.data?.error ?? err
					this.loading = false
				})
		},
	},
}
</script>

<style lang="css">
.dashboardSidebar .addFileContainer{
	margin-block: var(--OC-margin-20);
}
.dashboardSidebar .filesListDragDropNoticeWrapper{
	padding-block: 2rem;
}
</style>
<style scoped>
.addButton {
	margin-block-start: 10px;
	display: flex;
	justify-content: flex-end;
	gap: 10px;
}
.formContainer {
	display: flex;
	flex-direction: column;
}
</style>
