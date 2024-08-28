<script setup>
import { navigationStore, metadataStore } from '../../store/store.js'
</script>

<template>
	<NcModal v-if="navigationStore.modal === 'editMetaData'"
		ref="modalRef"
		label-id="editMetaDataModal"
		@close="navigationStore.setModal(false)">
		<div class="modal__content">
			<h2>Publicatie type bewerken</h2>
			<div v-if="success !== null || error">
				<NcNoteCard v-if="success" type="success">
					<p>Publicatie type succesvol bewerkt</p>
				</NcNoteCard>
				<NcNoteCard v-if="!success" type="error">
					<p>Er is iets fout gegaan bij het bewerken van metadata</p>
				</NcNoteCard>
				<NcNoteCard v-if="error" type="error">
					<p>{{ error }}</p>
				</NcNoteCard>
			</div>
			<div v-if="success == null" class="form-group">
				<NcTextField
					label="Titel"
					:disabled="loading"
					:value.sync="metadataStore.metaDataItem.title" />
				<NcTextField
					label="Versie"
					:disabled="loading"
					:value.sync="metadataStore.metaDataItem.version" />
				<NcTextField
					label="Samenvatting*"
					required="true"
					:disabled="loading"
					:value.sync="metadataStore.metaDataItem.summary" />
				<NcTextArea
					label="Beschrijving"
					:disabled="loading"
					:value.sync="metadataStore.metaDataItem.description" />
			</div>
			<NcButton
				v-if="success == null"
				:disabled="!metadataStore.metaDataItem.title || loading"
				type="primary"
				@click="editMetaData">
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
import { NcButton, NcModal, NcTextField, NcTextArea, NcLoadingIcon, NcNoteCard } from '@nextcloud/vue'
import ContentSaveOutline from 'vue-material-design-icons/ContentSaveOutline.vue'

export default {
	name: 'EditMetaDataModal',
	components: {
		NcModal,
		NcTextField,
		NcTextArea,
		NcButton,
		NcLoadingIcon,
		NcNoteCard,
		// Icons
		ContentSaveOutline,
	},
	data() {
		return {
			loading: false,
			success: null,
			error: false,
			metadataRequired: '',
		}
	},
	methods: {
		fetchData(id) {
			this.loading = true
			fetch(
				`/index.php/apps/opencatalogi/api/metadata/${id}`,
				{
					method: 'GET',
				},
			)
				.then((response) => {
					response.json().then((data) => {
						metadataStore.setMetaDataItem(data)
						this.metadataRequired = data.required.toString()
					})
					this.loading = false
				})
				.catch((err) => {
					this.error = err
					this.loading = false
				})
		},
		editMetaData() {
			this.loading = true

			fetch(
				`/index.php/apps/opencatalogi/api/metadata/${metadataStore.metaDataItem?.id}`,
				{
					method: 'PUT',
					headers: {
						'Content-Type': 'application/json',
					},
					body: JSON.stringify({
						...metadataStore.metaDataItem,
						required: this.metadataRequired.split(/, */g),
					}),
				},
			).then((response) => {
				this.loading = false
				this.success = response.ok
				// Lets refresh the catalogiList
				metadataStore.refreshMetaDataList()
				response.json().then((data) => {
					metadataStore.setMetaDataItem(data)
				})
				navigationStore.setSelected('metaData')
				// Wait for the user to read the feedback then close the model
				const self = this
				setTimeout(function() {
					self.success = null
					navigationStore.setModal(false)
				}, 2000)
			}).catch((err) => {
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
