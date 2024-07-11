<script setup>
import { store } from '../../store.js'
</script>

<template>
	<NcModal v-if="store.modal === 'editDirectory'" ref="modalRef" @close="store.setModal(false)">
		<div v-if="!loading" class="modal__content">
			<h2>Directory bewerken</h2>
			<div class="form-group">
				<NcTextField label="Titel" :value.sync="directory.title" />
			</div>
			<div class="form-group">
				<NcTextArea label="Samenvatting" :value.sync="directory.summary" />
			</div>
			<div class="form-group">
				<NcTextArea label="Beschrijving" :value.sync="directory.description" />
			</div>
			<div class="form-group">
				<NcTextField label="Search" :value.sync="directory.search" />
			</div>
			<div class="form-group">
				<NcTextField label="MetaData" :value.sync="directory.metadata" />
			</div>
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
	</NcModal>
</template>

<script>
import { NcButton, NcModal, NcTextField, NcLoadingIcon } from '@nextcloud/vue'

export default {
	name: 'EditDirectoryModal',
	components: {
		NcModal,
		NcTextField,
		NcButton,
		NcLoadingIcon,
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
					console.error(err)
					this.loading = false
				})
		},
		editDirectory() {
			this.loading = true
			fetch(
				`/index.php/apps/opencatalogi/api/directory/${store.directoryId}`,
				{
					method: 'PUT',
					headers: {
						'Content-Type': 'application/json',
					},
					body: JSON.stringify(this.directory),
				},
			).then((response) => {
				this.closeModal()
			}).catch((err) => {
				console.error(err)
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
