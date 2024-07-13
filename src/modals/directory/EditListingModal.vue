<script setup>
import { store } from '../../store.js'
</script>

<template>
	<NcModal v-if="store.modal === 'editLising'" ref="modalRef" @close="store.setModal(false)">
		<div v-if="!loading" class="modal__content">
			<h2>Directory bewerken</h2>
			<div class="form-group">
				<NcTextField label="Status" :value.sync="directory.status" />
			</div>
			<div class="form-group">
				<NcTextField label="Last synchronized" :value.sync="directory.lastSync" />
			</div>
			<div class="form-group">
				<NcTextField label="Default" :value.sync="directory.default" />
			</div>
			<NcButton :disabled="!directory.title" type="primary" @click="editDirectory">
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
	</NcModal>
</template>

<script>
import { NcButton, NcModal, NcTextField, NcLoadingIcon, NcNoteCard } from '@nextcloud/vue'

export default {
	name: 'EditListingModal',
	components: {
		NcModal,
		NcTextField,
		NcButton,
		NcLoadingIcon,
		NcNoteCard,
	},
	data() {
		return {
			directory: {
				title: '',
				summary: '',
				description: '',
				search: '',
				metadata: '',
				status: '',
				lastSync: '',
				defaultValue: '',
			},
			succesMessage: false,
			hasUpdated: false,
			loading: false,
			succes: false,
			error: false,
		}
	},
	updated() {
		if (store.modal === 'editDirectory' && this.hasUpdated) {
			if (this.directory.id === store.directoryId) return
			this.hasUpdated = false
		}
		if (store.modal === 'editDirectory' && !this.hasUpdated) {
			this.fetchData(store.directoryId)
			this.hasUpdated = true
		}
	},
	methods: {
		closeModal() {
			store.modal = false
		},
		fetchData(id) {
			this.loading = true
			fetch(
				`/index.php/apps/opencatalogi/api/directory/${id}`,
				{
					method: 'GET',
				},
			)
				.then((response) => {
					response.json().then((data) => {
						this.directory = data
						this.loading = false
					})
				})
				.catch((err) => {
					this.error = err
					this.loading = false
				})
		},
		editDirectory() {
			this.loading = true
			fetch(
				`/index.php/apps/opencatalogi/api/directory/${store.listingId}`,
				{
					method: 'PUT',
					headers: {
						'Content-Type': 'application/json',
					},
					body: JSON.stringify(this.directory),
				},
			).then((response) => {
				// Set propper modal states
				this.loading = false
				this.succes = true
				// Forse a refresh of the list and detaul page
				store.setSelected('directory')
				response.json().then((data) => {
					this.setListingItem(data)
				})
				// Wait and then close the modal
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
