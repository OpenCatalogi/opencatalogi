<script setup>
import { store } from '../../store.js'
</script>

<template>
	<NcModal v-if="store.modal === 'editDirectory'" ref="modalRef" @close="store.setModal(false)">
		<div v-if="!loading" class="modal__content">
			<h2>Directory bewerken</h2>
			<div class="form-group">
				<NcTextField :disabled="loading"
					label="Locatie"
					maxlength="255"
					:value.sync="url"
					required />
			</div>
			<NcButton :disabled="!url" type="primary" @click="editDirectory">
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
	name: 'EditDirectoryModal',
	components: {
		NcModal,
		NcTextField,
		NcButton,
		NcLoadingIcon,
	},
	data() {
		return {
			url: '',
			succesMessage: false,
			loading: false,
		}
	},
	methods: {
		closeModal() {
			store.modal = false
		},
		editDirectory() {
			this.closeModal()
		},
	},
}
</script>

<style>
.modal__content {
    margin: var(--OC-margin-50);
    text-align: center;
}

.zaakDetailsContainer {
    margin-block-start: var(--OC-margin-20);
    margin-inline-start: var(--OC-margin-20);
    margin-inline-end: var(--OC-margin-20);
}

.success {
    color: green;
}
</style>
