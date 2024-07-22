<script setup>
import { store } from '../../store/store.js'
</script>

<template>
	<NcModal v-if="store.modal === 'editCatalog'" ref="modalRef" @close="store.setModal(false)">
		<div class="modal__content">
			<h2>Catalogus bewerken</h2>
			<NcNoteCard v-if="succes" type="success">
				<p>Catalogus succesvol bewerkt</p>
			</NcNoteCard>
			<NcNoteCard v-if="error" type="error">
				<p>{{ error }}</p>
			</NcNoteCard>
			<div v-if="!succes" class="form-group">
				<NcTextField :disabled="loading"
					label="Naam"
					maxlength="255"
					:value.sync="store.catalogiItem.name"
					required />
				<NcTextField :disabled="loading"
					label="Samenvatting"
					maxlength="255"
					:value.sync="store.catalogiItem.summary" />
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
			loading: false,
			succes: false,
			error: false,
		}
	},
	methods: {
		closeModal() {
			store.modal = false
		},
		editCatalog() {
			this.loading = true
			this.error = false
			fetch(
				`/index.php/apps/opencatalogi/api/catalogi/${store.catalogiItem.id}`,
				{
					method: 'PUT',
					headers: {
						'Content-Type': 'application/json',
					},
					body: JSON.stringify(store.catalogiItem),
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
					// Wait for the user to read the feedback then close the model
					const self = this
					setTimeout(function() {
						self.succes = false
						store.setModal(false)
					}, 2000)
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
