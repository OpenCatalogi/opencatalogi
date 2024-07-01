<template>
	<NcModal v-if="isModalOpen.editMetaDataModal" ref="modalRef" @close="closeModal">
		<div class="modal__content">
			<h2>Edit metaData</h2>
			<div class="form-group">
				<NcTextField label="Naam" :value.sync="metaDataName" />
			</div>
			<div v-if="succesMessage" class="success">
				Succesfully updated metaData
			</div>

			<NcButton :disabled="!metaDataName" type="primary" @click="editMetaData">
				Submit
			</NcButton>
		</div>
	</NcModal>
</template>

<script>
import { NcButton, NcModal, NcTextField, NcTextArea } from '@nextcloud/vue'
import { isModalOpen } from '../modalContext.js'

export default {
	name: 'EditMetaDataModal',
	components: {
		NcModal,
		NcTextField,
		NcTextArea,
		NcButton,
	},
	props: [
		'metaDataName',
	],
	data() {
		return {
			succesMessage: false,
			isModalOpen,

		}
	},
	methods: {
		closeModal() {
			isModalOpen.editMetaDataModal = false
		},
		editMetaData() {
			this.$emit('metaData', this.metaDataName)
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
