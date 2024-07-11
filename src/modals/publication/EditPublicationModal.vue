<script setup>
import { store } from '../../store.js'
</script>
<template>
	<NcModal
		v-if="store.modal === 'publicationEdit'"
		ref="modalRef"
		@close="store.setModal(false)">
		<div v-if="!loading" class="modal__content">
			<h2>Edit publication</h2>
			<div v-if="!publicationLoading">
				<div class="form-group">
					<NcTextField :disabled="loading"
						label="Naam"
						:value.sync="publication.title"
						:loading="publicationLoading" />
				</div>
				<div class="form-group">
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
				<div class="selectGrid">
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
				</div>
				<div class="form-group">
					<NcTextArea :disabled="loading" label="Data" :value.sync="publication.data" />
				</div>

				<div class="form-group">
					<NcTextArea :disabled="publicationLoading" label="Bijlagen" :value.sync="attachments" />
				</div>
				<div v-if="succesMessage" class="success">
					Succesfully updated publication
				</div>
			</div>
			<NcLoadingIcon
				v-if="publicationLoading"
				:size="100"
				appearance="dark"
				name="Publicatie details aan het laden" />

			<NcButton :disabled="!publication.title" type="primary" @click="updatePublication(publication.id)">
				Submit
			</NcButton>
		</div>
		<NcLoadingIcon
			v-if="loading"
			:size="100" />
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
			succesMessage: false,
			catalogiLoading: false,
			metaDataLoading: false,
			hasUpdated: false,
			publicationLoading: false,
		}
	},
	updated() {
		if (store.modal === 'publicationEdit' && this.hasUpdated) {
			if (this.publication.id === store.publicationId) return
			this.hasUpdated = false
		}
		if (store.modal === 'publicationEdit' && !this.hasUpdated) {
			this.fetchCatalogi()
			this.fetchMetaData()
			this.fetchData(store.publicationId)
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

						this.catalogi = {
							value: this.catalogi.value,
							inputLabel: 'Catalogi',
							options: Object.entries(data.results).map((catalog) => ({
								id: catalog[1]._id,
								label: catalog[1].name,
							})),

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

						this.metaData = {
							inputLabel: 'MetaData',
							options: Object.entries(data.results).map((metaData) => ({
								id: metaData[1].id ?? metaData[1]._id,
								label: metaData[1].title ?? metaData[1].name,
							})),

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
					this.closeModal()
				})
				.catch((err) => {
					this.loading = false
					console.error(err)
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
