<script setup>
import { useUIStore, useMetadataStore } from '../../store/store.js'
</script>

<template>
	<NcModal
		v-if="UIStore.modal === 'editMetaData'"
		ref="modalRef"
		@close="UIStore.setModal(false)">
		<div class="modal__content">
			<h2>MetaData bewerken</h2>
			<NcNoteCard v-if="succes" type="success">
				<p>Meta data succesvol gewijzigd</p>
			</NcNoteCard>
			<NcNoteCard v-if="error" type="error">
				<p>{{ error }}</p>
			</NcNoteCard>
			<div v-if="!succes" class="form-group">
				<NcTextField label="Titel" :disabled="loading" :value.sync="metadataStore.metaDataItem.title" />
				<NcTextField label="Versie" :disabled="loading" :value.sync="metadataStore.metaDataItem.version" />
				<NcTextField label="Samenvatting" :disabled="loading" :value.sync="metadataStore.metaDataItem.summery" />
				<NcTextArea label="Beschrijving" :disabled="loading" :value.sync="metadataStore.metaDataItem.description" />
			</div>
			<NcButton
				v-if="!succes"
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
			UIStore: useUIStore(),
			metadataStore: useMetadataStore(),
			loading: false,
			succes: false,
			error: false,
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
						this.metadataStore.setMetaDataItem(data)
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
				`/index.php/apps/opencatalogi/api/metadata/${this.metadataStore.metaDataItem?.id}`,
				{
					method: 'PUT',
					headers: {
						'Content-Type': 'application/json',
					},
					body: JSON.stringify(this.metadataStore.metaDataItem),
				},
			).then((response) => {
				this.loading = false
				this.succes = true
				// Lets refresh the catalogiList
				this.metadataStore.refreshMetaDataList()
				response.json().then((data) => {
					this.metadataStore.setMetaDataItem(data)
				})
				this.UIStore.setSelected('metaData')
				// Wait for the user to read the feedback then close the model
				const self = this
				setTimeout(function() {
					self.succes = false
					this.UIStore.setModal(false)
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
