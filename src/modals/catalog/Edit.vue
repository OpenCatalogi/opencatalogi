<template>
	<NcModal v-if="store.modal === 'catalogEdit'" ref="modalRef" @close="closeModal">
		<div class="modal__content">
			<h2>Edit catalog</h2>
			<div class="form-group">
				<NcTextField label="Naam" />
			</div>
			<div v-if="succesMessage" class="success">
				Succesfully updated catalog
			</div>

			<NcButton :disabled="!catalogName" type="primary" @click="editCatalog">
				Submit
			</NcButton>
		</div>
	</NcModal>
</template>

<script>
import { NcButton, NcModal, NcTextField, NcTextArea } from '@nextcloud/vue'
import { store } from '../../store.js'

export default {
	name: 'EditCatalogModal',
	components: {
		NcModal,
		NcTextField,
		NcTextArea,
		NcButton,
		store
	},
	data() {
		return {
			succesMessage: false,

		}
	},
	methods: {
		closeModal() {
			store.modal = false
		},
		editCatalog() {
			this.$emit('catalog', this.catalogName)
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
