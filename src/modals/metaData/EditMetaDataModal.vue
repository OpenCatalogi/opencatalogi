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
			metaData: {
				title: '',
				version: '',
				description: '',
			},
			hasUpdated: false,
			loading: false,
			succes: false,
			error: false,
		}
	},
	updated() {
		if (store.modal === 'editMetaData' && this.hasUpdated) {
			if (this.metaData._id === store.metaDataItem?._id) return
			this.hasUpdated = false
		}
		if (store.modal === 'editMetaData' && !this.hasUpdated) {
			this.fetchData(store.metaDataItem?._id)
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
					this.loading = false
				})
				.catch((err) => {
					this.error = err
					this.loading = false
				})
		},
		closeModal() {
			store.modal = false
		},
		editMetaData() {
			this.loading = true
			fetch(
				`/index.php/apps/opencatalogi/api/metadata/${store.metaDataItem?._id}`,
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
				store.setSelected('metaData')
				response.json().then((data) => {
					store.setMetaDataItem(data)
				})
				setTimeout(() => (this.closeModal()), 2500)
				// Reset the form the form
				this.succes = false
				this.metaData = { title: '', version: '', description: '' }
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
