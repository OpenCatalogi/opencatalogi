<template>
	<NcModal v-if="store.modal === 'publicationEdit'" ref="modalRef" @close="closeModal">
		<div class="modal__content">
			<h2>Edit publication</h2>
			<div class="form-group">
				<NcTextField label="Naam" :value.sync="publicationName" />
			</div>
			<div v-if="succesMessage" class="success">
				Succesfully updated publication
			</div>

			<NcButton :disabled="!publicationName" type="primary" @click="editPublication">
				Submit
			</NcButton>
		</div>
	</NcModal>
</template>

<script>
import { NcButton, NcModal, NcTextField, NcTextArea } from '@nextcloud/vue'
import { store } from '../../store.js'

export default {
	name: 'EditPublicationModal',
	components: {
		NcModal,
		NcTextField,
		NcTextArea,
		NcButton,
		store
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
		editPublication() {
			this.$emit('publication', this.publicationName)
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
