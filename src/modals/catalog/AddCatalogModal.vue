<script setup>
import { catalogiStore, navigationStore } from '../../store/store.js'
</script>

<template>
	<NcModal v-if="navigationStore.modal === 'addCatalog'" ref="modalRef" @close="navigationStore.setModal(false)">
		<div class="modal__content">
			<h2>Catalogus toevoegen</h2>
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
				<NcTextField :disabled="loading"
					label="Beschrijving"
					maxlength="255"
					:value.sync="catalogi.description" />
				<NcTextField :disabled="loading"
					label="Image"
					maxlength="255"
					:value.sync="catalogi.image" />
				<NcTextField :disabled="loading"
					label="Search"
					maxlength="255"
					:value.sync="catalogi.search" />
			</div>
			<NcButton
				v-if="!succes"
				:disabled="!name || loading"
				type="primary"
				@click="addCatalog">
				<template #icon>
					<NcLoadingIcon v-if="loading" :size="20" />
					<ContentSaveOutline v-if="!loading" :size="20" />
				</template>
				Toevoegen
			</NcButton>
		</div>
	</NcModal>
</template>

<script>
import { NcButton, NcModal, NcTextField, NcLoadingIcon, NcNoteCard } from '@nextcloud/vue'
import ContentSaveOutline from 'vue-material-design-icons/ContentSaveOutline.vue'

export default {
	name: 'AddCatalogModal',
	components: {
		NcModal,
		NcTextField,
		NcButton,
		NcLoadingIcon,
		NcNoteCard,
		// Icons
		ContentSaveOutline,
	},
	data() {
		return {
			catalogi: {
				title: '',
				summary: '',
				description: '',
				image: '',
				search: '',
			},
			loading: false,
			succes: false,
			error: false,
			errorCode: '',
		}
	},
	methods: {
		closeModal() {
			navigationStore.modal = false
		},
		addCatalog() {
			this.loading = true
			this.error = false
			fetch(
				'/index.php/apps/opencatalogi/api/catalogi',
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
					catalogiStore.refreshCatalogiList()
					response.json().then((data) => {
						catalogiStore.setCatalogiItem(data)
					})
					// Wait for the user to read the feedback then close the model
					const self = this
					setTimeout(function() {
						self.succes = false
						navigationStore.setModal(false)
					}, 2000)
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
