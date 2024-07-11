<script setup>
import { store } from '../../store.js'
</script>

<template>
	<NcModal v-if="store.modal === 'addDirectory'" ref="modalRef" @close="store.setModal(false)">
		<div v-if="!loading" class="modal__content">
			<h2>Directory toevoeggen</h2>
			<div class="form-group">
				<NcTextField label="Titel" :value.sync="title" />
			</div>
			<div class="form-group">
				<NcTextArea label="Samenvatting" :value.sync="summary" />
			</div>
			<div class="form-group">
				<NcTextArea label="Beschrijving" :value.sync="description" />
			</div>
			<div class="form-group">
				<NcTextField label="Search" :value.sync="search" />
			</div>
			<div class="form-group">
				<NcTextField label="MetaData" :value.sync="metadata" />
			</div>
			<div class="form-group">
				<NcTextField label="Status" :value.sync="status" />
			</div>
			<div class="form-group">
				<NcTextField label="Last synchronized" :value.sync="lastSync" />
			</div>
			<div class="form-group">
				<NcTextField label="Last synchronized" :value.sync="lastSync" />
			</div>
			<div class="form-group">
				<NcTextField label="Default" :value.sync="defaultValue" />
			</div>

			<NcButton :disabled="!title" type="primary" @click="addDirectory">
				Submit
			</NcButton>
		</div>
		<NcLoadingIcon
			v-if="loading"
			:size="100" />
	</NcModal>
</template>

<script>
import { NcButton, NcModal, NcTextField, NcTextArea, NcLoadingIcon } from '@nextcloud/vue'

export default {
	name: 'AddDirectoryModal',
	components: {
		NcModal,
		NcTextField,
		NcTextArea,
		NcButton,
		NcLoadingIcon,
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
		}
	},
	methods: {
		closeModal() {
			store.modal = false
		},
		addDirectory() {
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
					this.closeModal()
				})
				.catch((err) => {
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

.zaakDetailsContainer {
    margin-block-start: var(--zaa-margin-20);
    margin-inline-start: var(--zaa-margin-20);
    margin-inline-end: var(--zaa-margin-20);
}

.success {
    color: green;
}
</style>
