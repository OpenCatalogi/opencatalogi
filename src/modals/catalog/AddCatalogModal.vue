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
			<div v-if="succesMessage" class="successMessage">
				Catalogi met succes aangemaakt
			</div>
			<div v-if="errorMessage" class="errorMessage">
				Oeps er is iets fout gegaan.
				Error Code: {{ errorCode }}
			</div>

			<NcButton :disabled="!name" type="primary" @click="addCatalog">
				Toevoegen
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
	name: 'AddCatalogModal',
	components: {
		NcModal,
		NcTextField,
		NcButton,
		NcLoadingIcon,
	},
	data() {
		return {
			name: '',
			summary: '',
			succesMessage: false,
			errorMessage: false,
			loading: false,
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
					this.catalogLoading = false
					this.succesMessage = true
					setTimeout(() => (this.succesMessage = false), 2500)

				})
				.catch((err) => {
					this.catalogLoading = false
					this.errorMessage = true
					this.errorCode = err
					console.error(err)
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
