<script setup>
import { store } from '../../store.js'
</script>

<template>
	<NcModal v-if="store.modal === 'catalogEdit'" ref="modalRef" @close="store.setModal(false)">
		<div class="modal__content">
			<h2>Catalogus bewerken</h2>
			<div class="form-group">
				<NcTextField :disabled="catalogLoading"
					label="Naam"
					maxlength="255"
					:value.sync="catalogi.name"
					required />
				<NcTextField :disabled="catalogLoading"
					label="Samenvatting"
					maxlength="255"
					:value.sync="catalogi.summary" />
			</div>
			<NcButton :disabled="loading" type="primary" @click="editCatalog">
				Opslaan
			</NcButton>
		</div>

		<NcNoteCard v-if="succes" type="success">
			<p>Meta data succesvol toegevoegd</p>
		</NcNoteCard>
		<NcNoteCard v-if="error" type="error">
			<p>{{ error }}</p>
		</NcNoteCard>
	</NcModal>
</template>

<script>
import { NcButton, NcModal, NcTextField, NcNoteCard } from '@nextcloud/vue'

export default {
	name: 'EditCatalogModal',
	components: {
		NcModal,
		NcTextField,
		NcButton,
		NcNoteCard,
	},
	data() {
		return {
			catalogi: {
				name: '',
				summary: '',
				_schema: '',
			},
			loading: false,
			editLoading: false,
			succes: false,
			error: false,
			catalogLoading: false,
			errorCode: '',
			hasUpdated: false,
		}
	},
	updated() {
		if (store.modal === 'catalogEdit' && this.hasUpdated) {
			if (this.catalogi._id === store.catalogiItem._id) return
			this.hasUpdated = false
		}
		if (store.modal === 'catalogEdit' && !this.hasUpdated) {
			this.catalogi = store.catalogiItem
			this.fetchData(store.catalogiItem._id)
			this.hasUpdated = true
		}
	},
	methods: {
		closeModal() {
			store.modal = false
		},
		fetchData(catalogId) {
			this.loading = true
			fetch(
				`/index.php/apps/opencatalogi/api/catalogi/${catalogId}`,
				{
					method: 'GET',
				},
			)
				.then((response) => {
					response.json().then((data) => {
						this.catalogi = data
					})
					this.loading = false
				})
				.catch((err) => {
					console.error(err)
					this.loading = false
				})
		},
		editCatalog() {
			this.editLoading = true
			this.errorMessage = false
			fetch(
				`/index.php/apps/opencatalogi/api/catalogi/${this.catalogi._id}`,
				{
					method: 'POST',
					headers: {
						'Content-Type': 'application/json',
					},
					body: JSON.stringify(this.catalogi),
				},
			)
				.then((response) => {
					this.editLoading = false
					this.succes = true
					setTimeout(() => (this.closeModal()), 2500)
				})
				.catch((err) => {
					this.error = err
					this.editLoading = false
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
