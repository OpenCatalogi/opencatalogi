<template>
	<NcModal v-if="isModalOpen.addPublicationModal" ref="modalRef" @close="closeModal">
		<div class="modal__content">
			<h2>Add publication</h2>
			<div class="form-group">
				<NcTextField label="Naam" :value.sync="name" />
			</div>
			<div v-if="succesMessage" class="success">
				Succesfully added publication
			</div>

			<NcButton :disabled="!name" type="primary" @click="addPublication">
				Submit
			</NcButton>
		</div>
	</NcModal>
</template>

<script>
import { NcButton, NcModal, NcTextField, NcTextArea } from '@nextcloud/vue'
import { isModalOpen } from '../modalContext.js'

export default {
	name: 'AddPublicationModal',
	components: {
		NcModal,
		NcTextField,
		NcTextArea,
		NcButton,
	},
	data() {
		return {
			name: '',
			succesMessage: false,
			isModalOpen,

		}
	},
	methods: {
		closeModal() {
			isModalOpen.addPublicationModal = false
		},
		addPublication() {
			this.$emit('publication', this.name)
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
