<script setup>
import { store } from '../../store.js'
</script>

<template>
	<NcModal v-if="store.modal === 'editCatalog'" ref="modalRef" @close="store.setModal(false)">
		<div class="modal__content">
			<h2>Catalogus bewerken</h2>
			<NcNoteCard v-if="succes" type="success">
				<p>Catalogus succesvol toegevoegd</p>
			</NcNoteCard>
			<NcNoteCard v-if="error" type="error">
				<p>{{ error }}</p>
			</NcNoteCard>
			<div v-if="!succes" class="form-group">
				<NcTextField :disabled="loading"
					label="Naam"
					maxlength="255"
					:value.sync="catalogi.name"
					required />
				<NcTextField :disabled="loading"
					label="Samenvatting"
					maxlength="255"
					:value.sync="catalogi.summary" />
			</div>
			<NcButton
				v-if="!succes"
				:disabled="loading"
				type="primary"
				@click="editCatalog()">
				<template #icon>
					<NcLoadingIcon v-if="loading" :size="20" />
					<ContentSaveOutline v-if="!loading" :size="20" />
				</template>
				Opslaan
			</NcButton>
		</div>
	</NcModal>
</template>

<script>
import { NcButton, NcModal, NcTextField, NcNoteCard, NcLoadingIcon } from '@nextcloud/vue'
import ContentSaveOutline from 'vue-material-design-icons/ContentSaveOutline.vue'

export default {
	name: 'EditCatalogModal',
	components: {
		NcModal,
		NcTextField,
		NcButton,
		NcNoteCard,
		NcLoadingIcon,
		// Icons
		ContentSaveOutline,
	},
	data() {
		return {
			catalogi: {
				name: '',
				summary: '',
			},
			loading: false,
			succes: false,
			error: false,
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
			this.fetchData(store.catalogiItem.id)
			this.hasUpdated = true
		}
	},
	mounted() {
		this.catalogi = store.catalogiItem
	},
	methods: {
		closeModal() {
			store.modal = false
		},
		fetchData(catalogId) {
			this.loading = true
			fetch(
				`/index.php/apps/opencatalogi/api/catalogi/${store.catalogiItem.id}`,
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
				`/index.php/apps/opencatalogi/api/catalogi/${store.catalogiItem.id}`,
				{
					method: 'POST',
					headers: {
						'Content-Type': 'application/json',
					},
					body: JSON.stringify(this.catalogi),
				},
			)
				.then((response) => {
					this.loading = false
					this.succes = true
					// Lets refresh the catalogiList
					store.refreshCatalogiList()
					response.json().then((data) => {
						store.setCatalogiItem(data)
					})
					setTimeout(() => (this.closeModal()), 2500)
				})
				.catch((err) => {
					this.loading = false
					this.error = err
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
