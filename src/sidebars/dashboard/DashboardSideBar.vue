<script setup>
import { navigationStore, searchStore, publicationStore } from '../../store/store.js'
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
				:value.sync="searchStore.search"
				label="Zoeken" />
			<NcNoteCard v-if="searchStore.searchError" type="error">
				<p>{{ searchStore.searchError }}</p>
			</NcNoteCard>
		</NcAppSidebarTab>

		<NcAppSidebarTab id="publication-creation-tab" name="Publicatie aanmaken" :order="2">
			<template #icon>
				<Plus :size="20" />
			</template>
			<h3 style="margin-top: 0;">
				Snel Publicatie aanmaken
			</h3>

			<NcSelect v-bind="catalogi"
				v-model="catalogi.value"
				style="min-width: unset; width: 100%;"
				input-label="Catalogus*"
				:loading="catalogiLoading"
				:disabled="catalogiLoading" />
			<NcSelect v-bind="filteredMetadataOptions"
				v-model="metaData.value"
				style="min-width: unset; width: 100%;"
				input-label="Publicatie type*"
				:loading="metaDataLoading"
				:disabled="metaDataLoading || !catalogi.value?.id" />
			<NcTextField :disabled="loading"
				label="Titel*"
				:value.sync="publicationItem.title" />
			<NcTextField :disabled="loading"
				label="Samenvatting*"
				:value.sync="publicationItem.summary" />

			<div class="addFileContainer">
				<div :ref="'dropZoneRef'" class="filesListDragDropNotice">
					<div class="filesListDragDropNoticeWrapper">
						<div class="filesListDragDropNoticeWrapperIcon">
							<TrayArrowDown :size="48" />
						</div>

						<h3 class="filesListDragDropNoticeTitle">
							Of
						</h3>

						<div class="filesListDragDropNoticeTitle">
							<NcButton v-if="!files"
								:disabled="loading"
								type="primary"
								@click="openFileUpload()">
								<template #icon>
									<Plus :size="20" />
								</template>
								Bestand toevoegen
							</NcButton>

							<NcButton v-if="files"
								:disabled="loading"
								type="primary"
								@click="reset()">
								<template #icon>
									<Minus :size="20" />
								</template>
								<span v-for="file of files" :key="file.name">{{ file.name }}</span>
							</NcButton>
						</div>
					</div>
				</div>
			</div>

			<NcButton :disabled="!publicationItem.title || !publicationItem.summary || !catalogi.value?.id || !metaData.value?.id || loading"
				style="margin-top: 0.5rem;"
				type="primary"
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
			<NcListItem v-for="(publication, i) in publicationStore.conceptPublications"
				:key="`${publication}${i}`"
				:name="publication.title"
				:bold="false"
				:force-display-actions="true"
				:active="publicationStore.publicationItem.id === publication.id"
				:details="publication?.status">
				<template #icon>
					<ListBoxOutline :class="publicationStore.publicationItem.id === publication.id && 'selectedZaakIcon'"
						disable-menu
						:size="44" />
				</template>
				<template #subname>
					{{ publication?.description }}
				</template>
				<template #actions>
					<NcActionButton @click="publicationStore.setPublicationItem(publication); navigationStore.setSelected('publication');">
						<template #icon>
							<ListBoxOutline :size="20" />
						</template>
						Bekijken
					</NcActionButton>
					<NcActionButton @click="publicationStore.setPublicationItem(publication); navigationStore.setModal('editPublication')">
						<template #icon>
							<Pencil :size="20" />
						</template>
						Bewerken
					</NcActionButton>
					<NcActionButton @click="publicationStore.setPublicationItem(publication); navigationStore.setDialog('publishPublication')">
						<template #icon>
							<Publish :size="20" />
						</template>
						Publiceren
					</NcActionButton>
					<NcActionButton @click="publicationStore.setPublicationItem(publication); navigationStore.setDialog('deletePublication')">
						<template #icon>
							<Delete :size="20" />
						</template>
						Verwijderen
					</NcActionButton>
				</template>
			</NcListItem>
			<NcNoteCard v-if="!publicationStore.conceptPublications?.length > 0" type="success">
				<p>Er zijn op dit moment geen publicaties die uw aandacht vereisen</p>
			</NcNoteCard>
		</NcAppSidebarTab>
		<NcAppSidebarTab id="share-tab" name="Bijlagen" :order="4">
			<template #icon>
				<FileOutline :size="20" />
			</template>
			Welke bijlagen vereisen uw aandacht?
			<NcListItem v-for="(attachment, i) in publicationStore.conceptAttachments"
				:key="`${attachment}${i}`"
				:name="attachment.title"
				:bold="false"
				:force-display-actions="true"
				:active="publicationStore.attachmentItem.id === attachment.id"
				:details="attachment?.status">
				<template #icon>
					<ListBoxOutline :class="publicationStore.publicationItem.id === attachment.id && 'selectedZaakIcon'"
						disable-menu
						:size="44" />
				</template>
				<template #subname>
					{{ attachment?.description }}
				</template>
				<template #actions>
					<NcActionButton @click="publicationStore.setAttachmentItem(attachment); navigationStore.setModal('editAttachment')">
						<template #icon>
							<Pencil :size="20" />
						</template>
						Bewerken
					</NcActionButton>
					<NcActionButton @click="publicationStore.setAttachmentItem(attachment); navigationStore.setDialog('publishAttachment')">
						<template #icon>
							<Publish :size="20" />
						</template>
						Publiceren
					</NcActionButton>
					<NcActionButton @click="publicationStore.setAttachmentItem(attachment); navigationStore.setDialog('deleteAttachment')">
						<template #icon>
							<Delete :size="20" />
						</template>
						Verwijderen
					</NcActionButton>
				</template>
			</NcListItem>
			<NcNoteCard v-if="!publicationStore.conceptAttachments?.length > 0" type="success">
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
import Minus from 'vue-material-design-icons/Minus.vue'
import FileOutline from 'vue-material-design-icons/FileOutline.vue'
import Pencil from 'vue-material-design-icons/Pencil.vue'
import Publish from 'vue-material-design-icons/Publish.vue'
import Delete from 'vue-material-design-icons/Delete.vue'
import TrayArrowDown from 'vue-material-design-icons/TrayArrowDown.vue'

import { useFileSelection } from '../../composables/UseFileSelection.js'
import { ref } from 'vue'
import axios from 'axios'

import { Publication } from '../../entities/index.js'

const dropZoneRef = ref()
const { openFileUpload, files, reset } = useFileSelection({ allowMultiple: false, dropzone: dropZoneRef })

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
			catalogiList: [], // this is the entire dataset of catalogi
			catalogi: {},
			metaDataLoading: false,
			metaDataList: [], // this is the entire dataset of metadata
			metaData: {},
			loading: false,
			error: null,
			success: null,
		}
	},
	computed: {
		/**
		 * Filters metadata (Now known as Publication Type) based on the catalogi
		 */
		filteredMetadataOptions() {
			if (!this.catalogiList?.length) return {}
			if (!this.catalogi?.options?.length) return {}
			if (!this.catalogi?.value?.id) return {}
			if (!this.metaDataList?.length) return {}

			// step 1: get the selected catalogus from the catalogi dropdown
			const selectedCatalogus = this.catalogiList
				.find((catalogus) =>
					(catalogus.id?.toString() || Symbol('catalogusId')) === (this.catalogi?.value.id?.toString() || Symbol('catalogiId')),
				)

			// step 2: get the full metadata's from the metadataIds
			const filteredMetadata = this.metaDataList
				.filter((metadata) => selectedCatalogus.metadata.includes(metadata.source))

			return {
				options: filteredMetadata.map((metaData) => ({
					id: metaData.id,
					source: metaData.source,
					label: metaData.title,
				})),
			}
		},
	},
	mounted() {
		publicationStore.getConceptPublications()
		publicationStore.getConceptAttachments()
		this.fetchCatalogi()
		this.fetchMetaData()
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
				this.metaData.value = []
			}
			this.error = null
		},
		fetchCatalogi() {
			this.catalogiLoading = true
			fetch('/index.php/apps/opencatalogi/api/catalogi', {
				method: 'GET',
			})
				.then((response) => {
					response.json().then((data) => {
						this.catalogiList = data.results

						this.catalogi = {
							options: data.results.map((catalog) => ({
								id: catalog.id,
								label: catalog.title,
							})),
							value: [],
						}
					})
					this.catalogiLoading = false
				})
				.catch((err) => {
					console.error(err)
					this.catalogiLoading = false
				})
		},
		fetchMetaData() {
			this.metaDataLoading = true

			fetch('/index.php/apps/opencatalogi/api/metadata', {
				method: 'GET',
			})
				.then((response) => {
					response.json().then((data) => {
						this.metaDataList = data.results
					})
					this.metaDataLoading = false
				})
				.catch((err) => {
					console.error(err)
					this.metaDataLoading = false
				})
		},
		addPublication() {
			this.loading = true
			this.error = false

			// create the publication
			fetch(
				'/index.php/apps/opencatalogi/api/publications',
				{
					method: 'POST',
					headers: {
						'Content-Type': 'application/json',
					},
					body: JSON.stringify({
						...this.publicationItem,
						catalogi: this.catalogi.value.id,
						metaData: this.metaData.value.source,
					}),
				},
			)
				.then(async (response) => {
					this.loading = false
					this.success = response.ok

					// if response is ok, and files exist, we add the attachment
					const publicationItem = new Publication(await response.json())
					if (response.ok && files.value) this.addAttachment(publicationItem)

					// Lets refresh the publicationList
					publicationStore.refreshPublicationList()
					// Wait for the user to read the feedback then close the model
					setTimeout(this.cleanup, 2000)
					if (!files.value && files.value.length === 0) {
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
				// Lets refresh the attachment list

				publicationStore.getPublicationAttachments(publicationItem?.id)

				fetch(
					`/index.php/apps/opencatalogi/api/publications/${publicationItem.id}`,
					{
						method: 'PUT',
						headers: {
							'Content-Type': 'application/json',
						},
						body: JSON.stringify({
							...publicationItem,
							attachments: [...publicationItem.attachments, response.data.id],
							catalogi: publicationItem.catalogi.id,
							metaData: publicationItem.metaData,
						}),
					},
				)
					.then((response) => {
						this.success = response.ok
						this.loading = false

						// Lets refresh the publicationList
						publicationStore.refreshPublicationList()
						response.json().then((data) => {
							publicationStore.setPublicationItem(data)
						})

					})
					.catch((err) => {
						this.error = err
						this.loading = false
					})
				// store.refreshCatalogiList()

				publicationStore.setAttachmentItem(response)

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
	margin-block-end: var(--OC-margin-20);
}
.dashboardSidebar .filesListDragDropNoticeWrapper{
	padding-block: 2rem;
}
</style>
