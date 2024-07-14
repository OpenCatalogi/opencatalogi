<script setup>
import { store } from '../../store.js'
</script>

<template>
	<NcModal
		v-if="store.modal === 'addMetaData'"
		ref="modalRef"
		@close="store.setModal(false)">
		<div class="modal__content">
			<h2>MetaData toevoegen</h2>
			<div v-if="!loading && !succes" class="form_wrapper">
				<div class="form-group">
					<NcTextField label="Titel" :value.sync="metaData.title" required="true" />
				</div>
				<div class="form-group">
					<NcTextField label="Versie" :value.sync="metaData.version" />
				</div>
				<div class="form-group">
					<NcTextArea label="Beschrijving" :value.sync="metaData.description" />
				</div>
				<NcButton :disabled="!metaData.title" type="primary" @click="addMetaData">
					Submit
				</NcButton>
			</div>
			<NcLoadingIcon
				v-if="loading"
				:size="100" />
			<NcNoteCard v-if="succes" type="success">
				<p>Meta data succesvol toegevoegd</p>
			</NcNoteCard>
			<NcNoteCard v-if="error" type="error">
				<p>{{ error }}</p>
			</NcNoteCard>
		</div>
	</NcModal>
</template>

<script>
import { NcButton, NcModal, NcTextField, NcTextArea, NcLoadingIcon, NcNoteCard } from '@nextcloud/vue'

export default {
	name: 'AddMetaDataModal',
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
			metaDataList: [],
			loading: false,
			succes: false,
			error: false,
		}
	},
	methods: {
		closeModal() {
			// Reset the form the form
			this.succes = false
			this.error = false
			this.loading = false
			this.metaData = { title: '', version: '', description: '' }
			store.modal = false
		},
		addMetaData() {
			this.loading = true
			fetch(
				'/index.php/apps/opencatalogi/api/metadata',
				{
					method: 'POST',
					headers: {
						'Content-Type': 'application/json',
					},
					body: JSON.stringify(this.metaData),
				},
			)
				.then((response) => {
					// Set the form
					this.loading = false
					this.succes = true
					// Work the data
					response.json().then((data) => {
						store.setMetaDataItem(data)
					})
					store.clearSearch()
					store.setSelected('metaData')
					// Update the list
					setTimeout(() => (
						this.closeModal()
					), 2500)
				})
				.catch((err) => {
					this.metaDataLoading = false
					this.error = err
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
