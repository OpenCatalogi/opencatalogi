<script setup>
import { UIStore, publicationStore } from '../../store/store.js'
</script>
<template>
	<NcModal v-if="UIStore.modal === 'publicationAdd'" ref="modalRef" @close="UIStore.setModal(false)">
		<div class="modal__content">
			<h2>Publicatie toevoegen</h2>
			<NcNoteCard v-if="succes" type="success">
				<p>Publicatie succesvol toegevoegd</p>
			</NcNoteCard>
			<NcNoteCard v-if="error" type="error">
				<p>{{ error }}</p>
			</NcNoteCard>
			<div class="formContainer">
				<div v-if="!succes" class="form-group">
					<NcTextField :disabled="publicationLoading" label="Naam" :value.sync="title" />
					<NcTextArea :disabled="publicationLoading" label="Beschrijving" :value.sync="description" />
					<NcTextField :disabled="loading"
						label="Categorie"
						:value.sync="category"
						:loading="publicationLoading" />
					<NcTextField :disabled="loading"
						label="Publicatie"
						:value.sync="publication"
						:loading="publicationLoading" />
					<NcTextField :disabled="loading"
						label="Portaal"
						:value.sync="portal"
						:loading="publicationLoading" />
					<NcTextField :disabled="loading"
						label="Status"
						:value.sync="status"
						:loading="publicationLoading" />
					<NcTextField :disabled="loading"
						label="Gepubliceerd"
						:value.sync="published"
						:loading="publicationLoading" />
					<p>Featured</p>
					<NcCheckboxRadioSwitch :disabled="loading"
						label="Featured"
						:value.sync="featured"
						:loading="publicationLoading" />
					<NcTextField :disabled="loading"
						label="Image"
						:value.sync="image"
						:loading="publicationLoading" />
					<NcTextField :disabled="loading"
						label="Modified"
						:value.sync="modified"
						:loading="publicationLoading" />
					<NcTextField :disabled="loading"
						label="Licentie"
						:value.sync="license"
						:loading="publicationLoading" />
					<NcSelect v-bind="catalogi"
						v-model="catalogi.value"
						input-label="Catalogi"
						:loading="catalogiLoading"
						:disabled="publicationLoading"
						required />
					<NcSelect v-bind="metaData"
						v-model="metaData.value"
						input-label="MetaData"
						:loading="metaDataLoading"
						:disabled="publicationLoading"
						required />
				</div>
			</div>
			<NcButton
				v-if="!succes"
				:disabled="(!title && !catalogi?.value?.id && !metaData?.value?.id) || loading"
				type="primary"
				@click="addPublication()">
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
	NcSelect,
	NcLoadingIcon,
	NcCheckboxRadioSwitch,
	NcNoteCard,
} from '@nextcloud/vue'
import ContentSaveOutline from 'vue-material-design-icons/ContentSaveOutline.vue'

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
		NcNoteCard,
		// Icons
		ContentSaveOutline,
	},
	data() {
		return {

			title: '',
			description: '',
			catalogi: {},
			metaData: {},
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
			catalogiLoading: false,
			metaDataLoading: false,
			publicationLoading: false,
			hasUpdated: false,
			loading: false,
			succes: false,
			error: false,
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
		if (UIStore.modal === 'publicationAdd' && !this.hasUpdated) {
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
		isJsonString(str) {
			try {
				JSON.parse(str)
			} catch (e) {
				return false
			}
			return true
		},
		addPublication() {
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
					this.succes = true
					// Lets refresh the catalogiList
					publicationStore.refreshPublicationList()
					response.json().then((data) => {
						publicationStore.setPublicationItem(data)
					})
					UIStore.setSelected('publication')
					// Clean it all up
					setTimeout(() => UIStore.setModal(false), 2500)
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
