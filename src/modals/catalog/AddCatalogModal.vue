<script setup>
import { store } from '../../store.js'
</script>

<template>
	<NcModal v-if="store.modal === 'catalogusAdd'" ref="modalRef" @close="store.setModal(false)">
		<div v-if="!loading" class="modal__content">
			<h2>Catalogus toevoegen</h2>
			<div class="form-group">
				<NcTextField :disabled="catalogLoading"
					label="Naam"
					maxlength="255"
					:value.sync="name"
					required />
				<NcTextField :disabled="catalogLoading"
					label="Samenvatting"
					maxlength="255"
					:value.sync="summary" />
			</div>
			<NcButton :disabled="!name" type="primary" @click="addCatalog">
				Toevoegen
			</NcButton>
		</div>
		<NcLoadingIcon
			v-if="loading"
			:size="100" />
		<NcNoteCard v-if="succes" type="success">
			<p>Meta data succesvol toegevoegd</p>
		</NcNoteCard>
		<NcNoteCard v-if="error" type="error">
			<p>{{ error }}</p>
		</NcNoteCard>
	</NcModal>
</template>

<script>
import { NcButton, NcModal, NcTextField, NcLoadingIcon, NcNoteCard } from '@nextcloud/vue'

export default {
	name: 'AddCatalogModal',
	components: {
		NcModal,
		NcTextField,
		NcButton,
		NcLoadingIcon,
		NcNoteCard,
	},
	data() {
		return {
			name: '',
			summary: '',
			loading: false,
			succes: false,
			error: false,
			catalogLoading: false,
			errorCode: '',
		}
	},
	methods: {
		closeModal() {
			store.modal = false
		},
		addCatalog() {
			this.catalogLoading = true
			this.errorMessage = false
			fetch(
				'/index.php/apps/opencatalogi/api/catalogi',
				{
					method: 'POST',
					headers: {
						'Content-Type': 'application/json',
					},
					body: JSON.stringify({
						name: this.name,
						summary: this.summary,
					}),
				},
			)
				.then((response) => {
					this.loading = false
					this.succes = true
					setTimeout(() => (this.closeModal()), 2500)

				})
				.catch((err) => {
					this.error = err
					this.loading = false
				})
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
