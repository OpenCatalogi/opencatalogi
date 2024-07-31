<script setup>
import { navigationStore, publicationStore } from '../../store/store.js'
</script>
<template>
	<NcModal
		v-if="navigationStore.modal === 'editPublication'"
		ref="modalRef"
		@close="navigationStore.setModal(false)">
		<div class="modal__content">
			<h2>Edit publication</h2>
			<div v-if="success !== null || error">
				<NcNoteCard v-if="success" type="success">
					<p>Publicatie succesvol bewerkt</p>
				</NcNoteCard>
				<NcNoteCard v-if="!success" type="error">
					<p>Er is iets fout gegaan bij het bewerken van Publicatie</p>
				</NcNoteCard>
				<NcNoteCard v-if="error" type="error">
					<p>{{ error }}</p>
				</NcNoteCard>
			</div>
			<div v-if="success === null" class="form-group">
				<NcTextField :disabled="loading"
					label="Titel"
					:value.sync="publicationStore.publicationItem.title" />
				<NcTextField :disabled="loading"
					label="Samenvatting"
					:value.sync="publicationStore.publicationItem.summary" />
				<NcTextArea :disabled="loading"
					label="Beschrijving"
					:value.sync="publicationStore.publicationItem.description" />
				<NcTextField :disabled="loading"
					label="Reference"
					:value.sync="publicationStore.publicationItem.reference" />
				<NcTextField :disabled="loading"
					label="Categorie"
					:value.sync="publicationStore.publicationItem.category" />
				<NcTextField :disabled="loading"
					label="Portaal"
					:value.sync="publicationStore.publicationItem.portal" />
				<span>
					<p>Published</p>
					<NcDateTimePicker v-model="publicationStore.publicationItem.published"
						:disabled="loading"
						label="Publicatie datum" />
				</span>
				<span>
					<p>Modified</p>
					<NcDateTimePicker v-model="publicationStore.publicationItem.modified"
						:disabled="loading"
						label="Modified" />
				</span>
				<NcTextField :disabled="loading"
					label="Organization"
					:value.sync="publicationStore.publicationItem.organization" />
				<NcTextField :disabled="loading"
					label="Attachments"
					:value.sync="publicationStore.publicationItem.attachments" />
				<NcTextField :disabled="loading"
					label="Schema"
					:value.sync="publicationStore.publicationItem.schema" />
				<NcTextField :disabled="loading"
					label="Thema's (splits op ,)"
					:value.sync="publicationStore.publicationItem.themes" />
				<p>Featured</p>
				<span class="EPM-horizontal">
					<NcCheckboxRadioSwitch :disabled="loading"
						label="Featured"
						:checked.sync="publicationStore.publicationItem.featured">
						Featured
					</NcCheckboxRadioSwitch>
				</span>
				<NcTextField :disabled="loading"
					label="Image"
					:value.sync="publicationStore.publicationItem.image" />
				<b>Juridisch</b>
				<NcTextField :disabled="loading"
					label="Licentie"
					:value.sync="publicationStore.publicationItem.license" />
				<NcSelect v-bind="catalogi"
					v-model="publicationStore.publicationItem.catalogi"
					input-label="Catalogi"
					:loading="catalogiLoading"
					required />
				<NcSelect v-bind="metaData"
					v-model="publicationStore.publicationItem.metaData"
					input-label="MetaData"
					:loading="metaDataLoading"
					required />
			</div>
			<NcButton v-if="success === null"
				:disabled="!publicationStore.publicationItem.title"
				type="primary"
				@click="updatePublication()">
				<template #icon>
					<NcLoadingIcon v-if="loading" :size="20" />
					<ContentSaveOutline v-if="!loading" :size="20" />
				</template>
				Opslaan
			</NcButton>
		</div>
	</NcModal>
</template>

<script>
import {
	NcButton,
	NcModal,
	NcTextField,
	NcTextArea,
	NcLoadingIcon,
	NcCheckboxRadioSwitch,
	NcDateTimePicker,
	NcNoteCard,
} from '@nextcloud/vue'
import ContentSaveOutline from 'vue-material-design-icons/ContentSaveOutline.vue'

export default {
	name: 'EditPublicationModal',
	components: {
		NcModal,
		NcTextField,
		NcTextArea,
		NcCheckboxRadioSwitch,
		NcDateTimePicker,
		NcButton,
		NcLoadingIcon,
		NcNoteCard,
		// Icons
		ContentSaveOutline,
	},
	data() {
		return {
			catalogi: {
				value: [],
				options: [],
			},
			metaData: {
				value: [],
				options: [],
			},
			loading: false,
			success: null,
			error: false,
			catalogiLoading: false,
			metaDataLoading: false,
		}
	},
	mounted() {
		this.publication = publicationStore.publicationItem
	},
	updated() {
		if (navigationStore.modal === 'publicationEdit' && this.hasUpdated) {
			if (this.publication.id === publicationStore.publicationItem.id) return
			this.hasUpdated = false
		}
		if (navigationStore.modal === 'publicationEdit' && !this.hasUpdated) {
			this.fetchCatalogi()
			this.fetchMetaData()
			this.fetchData(publicationStore.publicationItem.id)
			this.hasUpdated = true
		}
	},
	methods: {
		fetchData(id) {
			this.loading = true
			fetch(
				`/index.php/apps/opencatalogi/api/publications/${id}`,
				{
					method: 'GET',
				},
			)
				.then((response) => {
					response.json().then((data) => {
						publicationStore.setPublicationItem(data)
					})
					this.loading = false
				})
				.catch((err) => {
					console.error(err)
					this.loading = false
				})
		},
		fetchCatalogi() {
			this.catalogiLoading = true
			fetch('/index.php/apps/opencatalogi/api/catalogi', {
				method: 'GET',
			})
				.then((response) => {
					response.json().then((data) => {

						const selectedCatalogi = Object.entries(data.results).find((catalogi) => catalogi[1]._id === this.publication.catalogi)

						this.catalogi = {
							inputLabel: 'Catalogi',
							options: Object.entries(data.results).map((catalog) => ({
								id: catalog[1]._id,
								label: catalog[1].name,
							})),
							value: {
								id: selectedCatalogi[1]._id ?? '',
								label: selectedCatalogi[1].name ?? '',
							},

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
						const selectedMetaData = Object.entries(data.results).find((metadata) => metadata[1]._id === this.publication.metaData)

						this.metaData = {
							inputLabel: 'MetaData',
							options: Object.entries(data.results).map((metaData) => ({
								id: metaData[1].id ?? metaData[1]._id,
								label: metaData[1].title ?? metaData[1].name,
							})),
							value: {
								id: selectedMetaData[1]._id ?? '',
								label: selectedMetaData[1].name ?? selectedMetaData[1].title ?? '',
							},
						}
					})
					this.metaDataLoading = false
				})
				.catch((err) => {
					console.error(err)
					this.metaDataLoading = false
				})
		},
		updatePublication() {
			this.loading = true
			fetch(
				`/index.php/apps/opencatalogi/api/publications/${publicationStore.publicationItem.id}`,
				{
					method: 'PUT',
					headers: {
						'Content-Type': 'application/json',
					},
					body: JSON.stringify({
						...publicationStore.publicationItem,
						id: publicationStore.publicationItem.id.toString(),
						themes: Array.isArray(publicationStore.publicationItem.themes)
							? publicationStore.publicationItem.themes
							: publicationStore.publicationItem.themes.split(/, */g),
					}),
				},
			)
				.then((response) => {
					this.loading = false
					this.success = response.ok
					// Lets refresh the catalogiList
					publicationStore.refreshPublicationList()
					response.json().then((data) => {
						publicationStore.setPublicationItem(data)
					})
					navigationStore.setSelected('publication')

					const self = this
					setTimeout(() => {
						self.success = null
						navigationStore.setModal(false)
					}, 2500)
				})
				.catch((err) => {
					this.error = err
					this.loading = false
				})
		},
	},
}
</script>

<style>
.modal__content {
  margin: var(--OC-margin-50);
  text-align: center;
}

.zaakDetailsContainer {
  margin-block-start: var(--OC-margin-20);
  margin-inline-start: var(--OC-margin-20);
  margin-inline-end: var(--OC-margin-20);
}

.success {
  color: green;
}

.EPM-horizontal {
    display: flex;
    gap: 4px;
    flex-direction: row;
    align-items: center;
}
</style>
