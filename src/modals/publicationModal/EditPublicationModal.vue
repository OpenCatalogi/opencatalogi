<template>
	<NcModal v-if="isModalOpen.editPublicationModal" ref="modalRef" @close="closeModal">
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
import { isModalOpen } from '../modalContext.js'

export default {
	name: 'EditPublicationModal',
	components: {
		NcModal,
		NcTextField,
		NcTextArea,
		NcButton,
	},
	props: [
		'publicationName',
	],
	data() {
		return {
			succesMessage: false,
			isModalOpen,

		}
	},
	methods: {
		closeModal() {
			isModalOpen.editPublicationModal = false
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
