<script setup>
import { store } from '../../store.js'
</script>

<template>
	<NcModal
		v-if="store.modal === 'editMetaData'"
		ref="modalRef"
		@close="store.setModal(false)">
		<div v-if="!loading" class="modal__content">
			<h2>MetaData bewerken</h2>
			<div class="form-group">
				<NcTextField label="Titel" :value.sync="metaData.title" required="true" />
			</div>
			<div class="form-group">
				<NcTextField label="Versie" :value.sync="metaData.version" />
			</div>
			<div class="form-group">
				<NcTextArea label="Beschrijving" :value.sync="metaData.description" />
			</div>
			<div class="form-group">
				<NcTextArea label="Properties" :value.sync="metaData.properties" />
			</div>
			<div v-if="succesMessage" class="success">
				Succesfully updated metaData
			</div>

			<NcButton :disabled="!metaData.title" type="primary" @click="editMetaData">
				Opslaan
			</NcButton>
		</div>
		<NcLoadingIcon
			v-if="loading"
			:size="100" />
		<NcNoteCard v-if="succes" type="success">
			<p>Meta data succesvol gewijzigd</p>
		</NcNoteCard>
		<NcNoteCard v-if="error" type="error">
			<p>{{ error }}</p>
		</NcNoteCard>
	</NcModal>
</template>

<script>
import { NcButton, NcModal, NcTextField, NcTextArea, NcLoadingIcon, NcNoteCard } from '@nextcloud/vue'

export default {
	name: 'EditMetaDataModal',
	components: {
		NcModal,
		NcTextField,
		NcTextArea,
		NcButton,
		NcLoadingIcon,
		NcNoteCard,
	},
	data() {
		return {
			name: '',
			summery: '',
			metaData: {
				title: '',
				version: '',
				description: '',
				properties: '',
			},
			hasUpdated: false,
			loading: false,
			succes: false,
			error: false,
		}
	},
	updated() {
		if (store.modal === 'editMetaData' && this.hasUpdated) {
			if (this.metaData._id === store.metaDataId) return
			this.hasUpdated = false
		}
		if (store.modal === 'editMetaData' && !this.hasUpdated) {
			this.fetchData(store.metaDataId)
			this.hasUpdated = true
		}
	},
	methods: {
		fetchData(id) {
			this.metaDataLoading = true
			fetch(
				`/index.php/apps/opencatalogi/api/metadata/${id}`,
				{
					method: 'GET',
				},
			)
				.then((response) => {
					response.json().then((data) => {
						this.metaData = data
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
		editMetaData() {
			this.metaDataLoading = true
			fetch(
				`/index.php/apps/opencatalogi/api/metadata/${store.metaDataId}`,
				{
					method: 'PUT',
					headers: {
						'Content-Type': 'application/json',
					},
					body: JSON.stringify(this.metaData),
				},
			).then((response) => {
				this.loading = false
				this.succes = true
				setTimeout(() => (this.closeModal()), 2500)
			}).catch((err) => {
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
