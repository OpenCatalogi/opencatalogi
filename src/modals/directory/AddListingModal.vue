<script setup>
import { store } from '../../store.js'
</script>

<template>
	<NcModal v-if="store.modal === 'directoryAdd'" ref="modalRef" @close="store.setModal(false)">
		<div class="modal__content">
			<h2>Add externalCatalog</h2>
			<div class="form-group">
				<NcTextField label="Naam" />
			</div>
			<div v-if="succesMessage" class="success">
				Succesfully added externalCatalog
			</div>

			<NcButton :disabled="!name" type="primary" @click="addExternalCatalog">
				Submit
			</NcButton>
		</div>
	</NcModal>
</template>

<script>
import { NcButton, NcModal, NcTextField } from '@nextcloud/vue'

export default {
	name: 'AddExternalCatalogModal',
	components: {
		NcModal,
		NcTextField,
		NcButton,
	},
	data() {
		return {
			succesMessage: false
		}
	},
	methods: {
		addExternalCatalog() {
			this.$emit('externalCatalog', this.name)
			this.succesMessage = true
			setTimeout(() => this.succesMessage = false, 2500)
			this.name = ''
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
