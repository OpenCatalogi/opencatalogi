<script setup>
import { store } from '../../store.js'
</script>
<template>
	<NcModal
		v-if="store.modal === 'publicationEdit'"
		ref="modalRef"
		@close="store.setModal(false)">
		<div class="modal__content">
			<h2>Edit publication</h2>
			<NcNoteCard v-if="succes" type="success">
				<p>Publicatie succesvol bewerkt</p>
			</NcNoteCard>
			<NcNoteCard v-if="error" type="error">
				<p>{{ error }}</p>
			</NcNoteCard>
			<div class="form-group">
				<NcTextField :disabled="loading"
					label="Naam"
					:value.sync="publication.title"
					:loading="publicationLoading" />
				<NcTextArea :disabled="loading" label="Beschrijving" :value.sync="publication.description" />
			</div>
			<div class="form-group">
				<NcTextField :disabled="loading"
					label="Categorie"
					:value.sync="publication.category"
					:loading="publicationLoading" />
			</div>
			<div class="form-group">
				<NcTextField :disabled="loading"
					label="Publicatie"
					:value.sync="publication.publication"
					:loading="publicationLoading" />
			</div>
			<div class="form-group">
				<NcTextField :disabled="loading"
					label="Portaal"
					:value.sync="publication.portal"
					:loading="publicationLoading" />
			</div>
			<div class="form-group">
				<NcTextField :disabled="loading"
					label="Status"
					:value.sync="publication.status"
					:loading="publicationLoading" />
			</div>
			<div class="form-group">
				<NcTextField :disabled="loading"
					label="Gepubliceerd"
					:value.sync="publication.published"
					:loading="publicationLoading" />
			</div>
			<div class="form-group">
				<p>Featured</p>
				<NcCheckboxRadioSwitch :disabled="loading"
					label="Featured"
					:value.sync="publication.featured"
					:loading="publicationLoading" />
			</div>
			<div class="form-group">
				<NcTextField :disabled="loading"
					label="Image"
					:value.sync="publication.image"
					:loading="publicationLoading" />
			</div>
			<div class="form-group">
				<NcTextField :disabled="loading"
					label="Modified"
					:value.sync="publication.modified"
					:loading="publicationLoading" />
			</div>
			<div class="form-group">
				<NcTextField :disabled="loading"
					label="Licentie"
					:value.sync="publication.license"
					:loading="publicationLoading" />
			</div>
			<div class="form-group">
				<NcSelect v-bind="catalogi"
					v-model="catalogi.value"
					input-label="Catalogi"
					:loading="catalogiLoading"
					:disabled="loading"
					required />
			</div>
			<div class="form-group">
				<NcSelect
					v-bind="metaData"
					v-model="metaData.value"
					input-label="MetaData"
					:loading="catalogiLoading"
					:disabled="true" />
			</div>
			<div class="form-group">
				<NcTextArea :disabled="loading" label="Data" :value.sync="publication.data" />
			</div>

			<div class="form-group">
				<NcTextArea :disabled="loading" label="Bijlagen" :value.sync="publication.attachments" />
			</div>
			<NcButton v-if="!succes" :disabled="!publication.title" type="primary" @click="updatePublication()">
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
	NcSelect,
	NcLoadingIcon,
	NcCheckboxRadioSwitch,
	NcNoteCard,
} from '@nextcloud/vue'

export default {
	name: 'EditPublicationModal',
	components: {
		NcModal,
		NcTextField,
		NcTextArea,
		NcCheckboxRadioSwitch,
		NcButton,
		NcSelect,
		NcLoadingIcon,
		NcNoteCard,
	},
	data() {
		return {
			publication: {
				title: '',
				description: '',
				catalogi: {},
				metaData: {},
				data: '',
				attachments: '',
				license: '',
				modified: '',
				published: '',
				status: '',
				featured: '',
				publication: '',
				portal: '',
				category: '',
				image: '',
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
			succes: false,
			error: false,
			catalogiLoading: false,
			metaDataLoading: false,
		}
	},
	updated() {
		if (store.modal === 'publicationEdit' && this.hasUpdated) {
			if (this.publication.id === store.publicationItem.id) return
			this.hasUpdated = false
		}
		if (store.modal === 'publicationEdit' && !this.hasUpdated) {
			this.fetchCatalogi()
			this.fetchMetaData()
			this.fetchData(store.publicationItem.id)
			this.hasUpdated = true
		}
	},
	methods: {
		fetchData(id) {
			this.publicationLoading = true
			fetch(
				`/index.php/apps/opencatalogi/api/publications/${id}`,
				{
					method: 'GET',
				},
			)
				.then((response) => {
					response.json().then((data) => {
						this.publication = data
						this.publication.data = JSON.stringify(data.data)
						this.publication.attachments = JSON.stringify(data.attachments)
						this.catalogi.value = [data.catalogi]
						this.metaData.value = [data.metaData]
					})
					this.publicationLoading = false
				})
				.catch((err) => {
					console.error(err)
					this.publicationLoading = false
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
		closeModal() {
			store.modal = false
		},
		updatePublication(id) {
			this.loading = true
			fetch(
				`/index.php/apps/opencatalogi/api/publications/${id}`,
				{
					method: 'PUT',
					headers: {
						'Content-Type': 'application/json',
					},
					body: JSON.stringify({
						modified: this.publication.modified,
						license: this.publication.license,
						published: this.publication.published,
						status: this.publication.status,
						featured: this.publication.featured,
						publication: this.publication.publication,
						portal: this.publication.portal,
						category: this.publication.category,
						image: this.publication.image,
						title: this.publication.title,
						description: this.publication.description,
						catalogi: this.publication.catalogi,
						metaData: this.publication.metaData,
						data: JSON.parse(this.publication.data),
						attachments: JSON.parse(this.publication.attachments),
					}),
				},
			)
				.then((response) => {
					this.loading = false
					this.succes = true
					// Lets refresh the catalogiList
					store.refreshPublicationList()
					response.json().then((data) => {
						store.setpublicationItem(data)
					})
					store.setSelected('publication')
					setTimeout(() => (this.closeModal()), 2500)
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
</style>
