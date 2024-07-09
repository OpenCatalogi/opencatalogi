<script setup>
import { store } from '../../store.js'
</script>

<template>
	<NcModal v-if="store.modal === 'listinEdit'" ref="modalRef" @close="store.setModal(false)">
		<div v-if="!loading" class="modal__content">
			<h2>Edit externalCatalog</h2>
			<div class="form-group">
				<NcTextField label="Naam" />
			</div>
			<div v-if="succesMessage" class="success">
				Succesfully updated externalCatalog
			</div>

			<NcButton :disabled="!externalCatalogName" type="primary" @click="editExternalCatalog">
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
	name: 'EditListingModal',
	components: {
		NcModal,
		NcTextField,
		NcButton,
		NcLoadingIcon,
	},
	data() {
		return {
			succesMessage: false,
			loading: false,
		}
	},
	methods: {
		closeModal() {
			store.modal = false
		},
		editExternalCatalog() {
			this.closeModal()
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
