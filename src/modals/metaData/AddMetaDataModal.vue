<script setup>
import { store } from '../../store.js'
</script>

<template>
	<NcModal
		v-if="store.modal === 'addMetaData'"
		ref="modalRef"
		@close="store.setModal(false)">
		<div v-if="!loading" class="modal__content">
			<h2>MetaData toevoegen</h2>
			<div class="form-group">
				<NcTextField label="Titel" :value.sync="title" required="true" />
			</div>
			<div class="form-group">
				<NcTextField label="Versie" :value.sync="version" />
			</div>
			<div class="form-group">
				<NcTextArea label="Beschrijving" :value.sync="description" />
			</div>
			<div class="form-group">
				<NcTextArea label="Properties" :value.sync="properties" />
			</div>
			<div v-if="succesMessage" class="success">
				Succesfully added MetaData
			</div>

			<NcButton :disabled="!title" type="primary" @click="addMetaData">
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
	name: 'AddMetaDataModal',
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
			version: '0.0.1',
			description: '',
			properties: '',
			succesMessage: false,
		}
	},
	methods: {
		closeModal() {
			store.modal = false
		},
		addMetaData() {
			this.$emit('metadata', this.title)
			fetch(
				'/index.php/apps/opencatalogi/api/metadata',
				{
					method: 'POST',
					headers: {
						'Content-Type': 'application/json',
					},
					body: JSON.stringify({
						title: this.title,
						version: this.version,
						description: this.description,
						properties: this.properties,
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
