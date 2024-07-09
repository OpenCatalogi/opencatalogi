<script setup>
import { store } from '../../store.js'
</script>

<template>
	<NcModal v-if="store.modal === 'directoryEdit'" ref="modalRef" @close="store.setModal(false)">
		<div class="modal__content">
			<h2>Edit externalCatalog</h2>
			<div class="form-group">
				<NcTextField label="Naam"  />
			</div>
			<div v-if="succesMessage" class="success">
				Succesfully updated externalCatalog
			</div>

			<NcButton :disabled="!externalCatalogName" type="primary" @click="editExternalCatalog">
				Submit
			</NcButton>
		</div>
	</NcModal>
</template>

<script>
import { NcButton, NcModal, NcTextField } from '@nextcloud/vue'

export default {
	name: 'EditExternalCatalogModal',
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
		closeModal() {
			store.modal = false
		},
		editExternalCatalog() {
			this.$emit('externalCatalog', this.externalCatalogName)
			this.succesMessage = true
			setTimeout(() => this.succesMessage = false, 2500)
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
