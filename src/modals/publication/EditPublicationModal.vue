<script setup>
import { navigationStore, publicationStore } from '../../store/store.js'
</script>
<template>
	<NcModal v-if="navigationStore.modal === 'editPublication'"
		ref="modalRef"
		label-id="editPublicationModal"
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
					:value.sync="publicationItem.title" />
				<NcTextField :disabled="loading"
					label="Samenvatting"
					:value.sync="publicationItem.summary" />
				<NcTextArea :disabled="loading"
					label="Beschrijving"
					:value.sync="publicationItem.description" />
				<NcTextField :disabled="loading"
					label="Reference"
					:value.sync="publicationItem.reference" />
				<NcTextField :disabled="loading"
					label="Categorie"
					:value.sync="publicationItem.category" />
				<NcTextField :disabled="loading"
					label="Portaal"
					:value.sync="publicationItem.portal" />
				<span>
					<p>Published</p>
					<NcDateTimePicker v-model="publicationItem.published"
						:disabled="loading"
						label="Publicatie datum" />
				</span>
				<span>
					<p>Modified</p>
					<NcDateTimePicker v-model="publicationItem.modified"
						:disabled="loading"
						label="Modified" />
				</span>
				<span class="EPM-horizontal">
					<NcCheckboxRadioSwitch :disabled="loading"
						label="Featured"
						:checked.sync="publicationItem.featured">
						Featured
					</NcCheckboxRadioSwitch>
				</span>
				<NcTextField :disabled="loading"
					label="Image"
					:value.sync="publicationItem.image" />
				<b>Juridisch</b>
				<NcTextField :disabled="loading"
					label="Licentie"
					:value.sync="publicationItem.license" />
			</div>
			<NcButton v-if="success === null"
				:disabled="!publicationItem.title"
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
	NcCheckboxRadioSwitch,
	NcDateTimePicker,
	NcLoadingIcon,
	NcModal,
	NcNoteCard,
	NcTextArea,
	NcTextField,
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
			publicationItem: {
				id: '',
				title: '',
				summary: '',
				description: '',
				reference: '',
				image: '',
				category: '',
				portal: '',
				featured: false,
				published: '',
				modified: '',
				license: '',
				catalogi: '',
				metaData: '',
			},
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
			hasUpdated: false,
		}
	},
	mounted() {
		// publicationStore.publicationItem can be false, so only assign publicationStore.publicationItem to publicationItem if its NOT false
		publicationStore.publicationItem && (this.publicationItem = publicationStore.publicationItem)
	},
	updated() {
		if (navigationStore.modal === 'editPublication' && this.hasUpdated) {
			if (this.publicationItem.id === publicationStore.publicationItem.id) return
			this.hasUpdated = false
		}
		if (navigationStore.modal === 'editPublication' && !this.hasUpdated) {
			publicationStore.publicationItem && (this.publicationItem = publicationStore.publicationItem)
			this.fetchData(publicationStore.publicationItem.id)
			// just incase we decide we want to be able to change catalogi and metadata
			// this.fetchCatalogi()
			// this.fetchMetaData()
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

						const selectedCatalogi = data.results.find((catalogi) => catalogi.id.toString() === this.publicationItem.catalogi.toString())

						this.catalogi = {
							inputLabel: 'Catalogi',
							options: data.results.map((catalog) => ({
								id: catalog.id,
								label: catalog.title,
							})),
							// FIXME: for some reason the NcSelect uses the id instead of the label when displaying
							value: [{
								id: selectedCatalogi.id ?? '',
								label: selectedCatalogi.title ?? '',
							}],
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
						const selectedMetaData = data.results.find((metadata) => metadata.id.toString() === this.publicationItem.metaData.toString())

						this.metaData = {
							inputLabel: 'MetaData',
							options: data.results.map((metaData) => ({
								id: metaData.id,
								label: metaData.title,
							})),
							// FIXME: for some reason the NcSelect uses the id instead of the label when displaying
							value: {
								id: selectedMetaData.id,
								label: selectedMetaData.title,
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
						...this.publicationItem,
						id: this.publicationItem.id.toString(),
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
