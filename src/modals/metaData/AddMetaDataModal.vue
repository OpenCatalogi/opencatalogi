<script setup>
import { navigationStore, metadataStore } from '../../store/store.js'
</script>

<template>
	<NcModal v-if="navigationStore.modal === 'addMetaData'"
		ref="modalRef"
		label-id="addMetaDataModal"
		@close="closeModal">
		<div class="modal__content">
			<h2>Publicatie type toevoegen</h2>
			<div v-if="success !== null || error">
				<NcNoteCard v-if="success" type="success">
					<p>Publicatie type succesvol toegevoegd</p>
				</NcNoteCard>
				<NcNoteCard v-if="!success" type="error">
					<p>Er is iets fout gegaan bij het toevoegen van publicatie type</p>
				</NcNoteCard>
				<NcNoteCard v-if="error" type="error">
					<p>{{ error }}</p>
				</NcNoteCard>
			</div>
			<div v-if="success === null" class="form-group">
				<NcTextField
					label="Titel"
					required="true"
					:value.sync="metaData.title" />
				<NcTextField
					label="Versie"
					:value.sync="metaData.version" />
				<NcTextField :disabled="loading"
					label="Samenvatting*"
					required="true"
					:value.sync="metaData.summary" />
				<NcTextArea
					label="Beschrijving"
					:disabled="loading"
					:value.sync="metaData.description" />
			</div>
			<NcButton v-if="success === null"
				:disabled="!metaData.title || loading"
				type="primary"
				@click="addMetaData">
				<template #icon>
					<NcLoadingIcon v-if="loading" :size="20" />
					<Plus v-if="!loading" :size="20" />
				</template>
				Toevoegen
			</NcButton>
		</div>
	</NcModal>
</template>

<script>
import { NcButton, NcModal, NcTextField, NcTextArea, NcLoadingIcon, NcNoteCard } from '@nextcloud/vue'
import Plus from 'vue-material-design-icons/Plus.vue'

export default {
	name: 'AddMetaDataModal',
	components: {
		NcModal,
		NcTextField,
		NcTextArea,
		NcButton,
		NcLoadingIcon,
		NcNoteCard,
		// Icons
		Plus,
	},
	data() {
		return {

			metaData: {
				title: '',
				version: '',
				description: '',
				summary: '',
				required: '',
			},
			loading: false,
			success: null,
			error: false,
		}
	},
	methods: {
		closeModal() {
			this.success = null
			this.metaData = {
				title: '',
				version: '',
				description: '',
				summary: '',
				required: '',
			}
			navigationStore.setModal(false)
		},
		addMetaData() {
			this.loading = true

			// Prevent setting source on any way.
			if (this.metadata?.source !== undefined) {
				delete this.metadata.source
			}

			fetch(
				'/index.php/apps/opencatalogi/api/metadata',
				{
					method: 'POST',
					headers: {
						'Content-Type': 'application/json',
					},
					body: JSON.stringify({
						...this.metaData,
					}),
				},
			)
				.then((response) => {
					// Set the form
					this.loading = false
					this.success = response.ok
					// Lets refresh the catalogiList
					metadataStore.refreshMetaDataList()
					response.json().then((data) => {
						metadataStore.setMetaDataItem(data)
					})
					navigationStore.setSelected('metaData')
					// Update the list
					const self = this
					setTimeout(function() {
						self.closeModal()
					}, 2000)
				})
				.catch((err) => {
					this.metaDataLoading = false
					this.error = err
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
