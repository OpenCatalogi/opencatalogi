<script setup>
import { store } from '../../store.js'
</script>
<template>
	<NcModal v-if="store.modal === 'publicationAdd'" ref="modalRef" @close="store.setModal(false)">
		<div v-if="!loading && !successMessage" class="modal__content">
			<h2>Add publication</h2>
			<div class="formContainer">
				<div class="form-group">
					<NcTextField :disabled="publicationLoading" label="Naam" :value.sync="title" />
				</div>
				<div class="form-group">
					<NcTextArea :disabled="publicationLoading" label="Beschrijving" :value.sync="description" />
				</div>
				<div class="form-group">
					<NcTextField :disabled="loading"
						label="Categorie"
						:value.sync="category"
						:loading="publicationLoading" />
				</div>
				<div class="form-group">
					<NcTextField :disabled="loading"
						label="Publicatie"
						:value.sync="publication"
						:loading="publicationLoading" />
				</div>
				<div class="form-group">
					<NcTextField :disabled="loading"
						label="Portaal"
						:value.sync="portal"
						:loading="publicationLoading" />
				</div>
				<div class="form-group">
					<NcTextField :disabled="loading"
						label="Status"
						:value.sync="status"
						:loading="publicationLoading" />
				</div>
				<div class="form-group">
					<NcTextField :disabled="loading"
						label="Gepubliceerd"
						:value.sync="published"
						:loading="publicationLoading" />
				</div>
				<div class="form-group">
					<p>Featured</p>
					<NcCheckboxRadioSwitch :disabled="loading"
						label="Featured"
						:value.sync="featured"
						:loading="publicationLoading" />
				</div>
				<div class="form-group">
					<NcTextField :disabled="loading"
						label="Image"
						:value.sync="image"
						:loading="publicationLoading" />
				</div>
				<div class="form-group">
					<NcTextField :disabled="loading"
						label="Modified"
						:value.sync="modified"
						:loading="publicationLoading" />
				</div>
				<div class="form-group">
					<NcTextField :disabled="loading"
						label="Licentie"
						:value.sync="license"
						:loading="publicationLoading" />
				</div>
				<div class="selectGrid">
					<div class="form-group">
						<NcSelect v-bind="catalogi"
							v-model="catalogi.value"
							input-label="Catalogi"
							:loading="catalogiLoading"
							:disabled="publicationLoading"
							required />
					</div>
					<div class="form-group">
						<NcSelect v-bind="metaData"
							v-model="metaData.value"
							input-label="MetaData"
							:loading="metaDataLoading"
							:disabled="publicationLoading"
							required />
					</div>
				</div>
				<div class="form-group">
					<NcTextArea :error="!dataIsValidJson"
						:disabled="publicationLoading"
						label="Data"
						:value.sync="data" />
				</div>
				<div class="form-group">
					<NcTextArea :error="!attachmentsIsValidJson"
						:disabled="publicationLoading"
						label="Bijlagen"
						:value.sync="attachments" />
				</div>
				<div v-if="successMessage" class="successMessage">
					Succesfully added publication
				</div>
				<div v-if="errorMessage" class="errorMessage">
					Oeps er is iets fout gegaan.
					Error Code: {{ errorCode }}
				</div>
			</div>
			<NcButton :disabled="!title && !catalogi?.value?.id && !metaData?.value?.id || publicationLoading" type="primary" @click="addPublication">
				Submit
			</NcButton>
		</div>
		<div v-if="successMessage" class="successMessage">
			Succesfully added publication
		</div>
		<div v-if="errorMessage" class="errorMessage">
			Oeps er is iets fout gegaan.
			Error Code: {{ errorCode }}
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
	name: 'AddPublicationModal',
	components: {
		NcModal,
		NcTextField,
		NcTextArea,
		NcButton,
		NcSelect,
		NcLoadingIcon,
		NcCheckboxRadioSwitch,
	},
	data() {
		return {
			title: '',
			description: '',
			data: '{}',
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
			errorCode: '',
			successMessage: false,
			errorMessage: false,
			catalogiLoading: false,
			metaDataLoading: false,
			publicationLoading: false,
			hasUpdated: false,
			loading: false,
			dataIsValidJson: false,
			attachmentsIsValidJson: false,

		}
	},
	watch: {
		data: {
			handler(data) {
				this.dataIsValidJson = this.isJsonString(data)
			},
			deep: true,
		},
		attachments: {
			handler(attachments) {
				this.attachmentsIsValidJson = this.isJsonString(attachments)
			},
			deep: true,
		},
	},
	updated() {
		if (store.modal === 'publicationAdd' && !this.hasUpdated) {
			this.fetchCatalogi()
			this.fetchMetaData()
			this.hasUpdated = true
		}
	},
	methods: {
		fetchCatalogi() {
			this.catalogiLoading = true
			fetch('/index.php/apps/opencatalogi/api/catalogi', {
				method: 'GET',
			})
				.then((response) => {
					response.json().then((data) => {

						this.catalogi = {
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

		isJsonString(str) {
			try {
				JSON.parse(str)
			} catch (e) {
				return false
			}
			return true
		},
		addPublication() {
			this.publicationLoading = true
			this.errorMessage = false
			this.loading = true
			fetch(
				'/index.php/apps/opencatalogi/api/publications',
				{
					method: 'POST',
					headers: {
						'Content-Type': 'application/json',
					},
					body: JSON.stringify({
						title: this.title,
						description: this.description,
						catalogi: this.catalogi.value.id,
						metaData: this.metaData.value.id,
						data: JSON.parse(this.data),
						attachments: JSON.parse(this.attachments),
						license: this.license,
						modified: this.modified,
						published: this.published,
						status: this.status,
						featured: this.featured,
						publication: this.publication,
						portal: this.portal,
						category: this.category,
						image: this.image,
					}),
				},
			)
				.then((response) => {
					this.loading = false
					this.succesMessage = true
					setTimeout(() => (store.modal = false), 2500)
					setTimeout(() => (this.succesMessage = false), 2500)
					store.setRefresh(true)
				})
				.catch((err) => {
					this.publicationLoading = false
					this.errorMessage = true
					this.errorCode = err
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

.formContainer>* {
	margin-block-end: 10px;
}

.selectGrid {
	display: grid;
	grid-gap: 5px;
	grid-template-columns: 1fr 1fr;
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
