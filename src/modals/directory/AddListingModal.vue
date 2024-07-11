<script setup>
import { store } from '../../store.js'
</script>

<template>
	<NcModal v-if="store.modal === 'addLising'" ref="modalRef" @close="store.setModal(false)">
		<div v-if="!loading" class="modal__content">
			<h2>Directory toevoegen</h2>
			<div class="form-group">
				<NcTextField label="Url" :value.sync="search" />
			</div>

			<NcButton :disabled="!title" type="primary" @click="addDirectory">
				Submit
			</NcButton>
		</div>
		<NcLoadingIcon
			v-if="loading"
			:size="100" />
		<NcNoteCard v-if="succes" type="success">
			<p>Listing succesvol toegevoegd</p>
		</NcNoteCard>
		<NcNoteCard v-if="error" type="error">
			<p>{{ error }}</p>
		</NcNoteCard>
	</NcModal>
</template>

<script>
import { NcButton, NcModal, NcTextField, NcLoadingIcon, NcNoteCard } from '@nextcloud/vue'

export default {
	name: 'AddListingModal',
	components: {
		NcModal,
		NcTextField,
		NcButton,
		NcLoadingIcon,
		NcNoteCard,
	},
	data() {
		return {
			title: '',
			summary: '',
			description: '',
			search: '',
			metadata: '',
			status: '',
			lastSync: '',
			defaultValue: '',
			succesMessage: false,
			loading: false,
			succes: false,
			error: false,
		}
	},
	methods: {
		closeModal() {
			store.modal = false
		},
		addDirectory() {
			this.loading = true
			this.$emit('metadata', this.title)
			fetch(
				'/index.php/apps/opencatalogi/api/directory',
				{
					method: 'POST',
					headers: {
						'Content-Type': 'application/json',
					},
					body: JSON.stringify({
						title: this.title,
						summary: this.summary,
						description: this.description,
						search: this.search,
						metadata: this.metadata,
						status: this.status,
						lastSync: this.lastSync,
						default: this.defaultValue,
					}),
				},
			)
				.then((response) => {
					// Set propper modal states
					this.loading = false
					this.succes = true
					// Forse a refresh of the list and detaul page
					store.setSelected(false)
					store.setSelected('directory')
					store.setListingItem(false)
					store.setListingItem(false)
					// Wait and then close the modal
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
    margin: var(--zaa-margin-50);
    text-align: center;
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
