<script setup>
import { store } from '../../store.js'
</script>
<template>
	<NcModal v-if="store.modal === 'publicationAdd'" ref="modalRef" @close="store.setModal(false)">
		<div class="modal__content">
			<h2>Add publication</h2>
			<div class="formContainer">
				<div class="form-group">
					<NcTextField :disabled="publicationLoading" label="Naam" :value.sync="title" />
				</div>
				<div class="form-group">
					<NcTextArea :disabled="publicationLoading" label="Beschrijving" :value.sync="description" />
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
					<NcTextArea :disabled="publicationLoading" label="Data" :value.sync="data" />
				</div>
				<div v-if="succesMessage" class="success">
					Succesfully added publication
				</div>
			</div>
			<NcButton :disabled="!title && !catalogi?.value?.id && !metaData?.value?.id || publicationLoading" type="primary" @click="addPublication">
				Submit
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
} from '@nextcloud/vue'

export default {
	name: 'AddPublicationModal',
	components: {
		NcModal,
		NcTextField,
		NcTextArea,
		NcButton,
		NcSelect,
	},
	data() {
		return {
			title: '',
			description: '',
			data: '',
			catalogi: {},
			metaData: {},
			succesMessage: false,
			catalogiLoading: false,
			metaDataLoading: false,
			publicationLoading: false,
			hasUpdated: false,
		}
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
			fetch('/index.php/apps/opencatalog/catalogi/api', {
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
			fetch('/index.php/apps/opencatalog/metadata/api', {
				method: 'GET',
			})
				.then((response) => {
					response.json().then((data) => {

						this.metaData = {
							options: Object.entries(data.results).map((metaData) => ({
								id: metaData[1]._id,
								label: metaData[1].name,
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
		addPublication() {
			this.publicationLoading = true
			fetch(
				'/index.php/apps/opencatalog/publications/api',
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
					}),
				},
			)
				.then((response) => {
					this.succesMessage = true
					this.publicationLoading = false
					setTimeout(() => (this.succesMessage = false), 2500)
				})
				.catch((err) => {
					this.publicationLoading = false
					console.error(err)
				})
		},
	},
}
</script>

<style>
.modal__content {
	margin: var(--zaa-margin-50);
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
	margin-block-start: var(--zaa-margin-20);
	margin-inline-start: var(--zaa-margin-20);
	margin-inline-end: var(--zaa-margin-20);
}

.success {
	color: green;
}
</style>
