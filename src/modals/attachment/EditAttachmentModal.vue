<script setup>
import { navigationStore, publicationStore } from '../../store/store.js'
</script>

<template>
	<NcModal v-if="navigationStore.modal === 'EditAttachment'"
		ref="modalRef"
		label-id="EditAttachmentModal"
		@close="navigationStore.setModal(false)">
		<div class="modal__content">
			<h2>Bijlage bewerken</h2>
			<div v-if="success !== null || error">
				<NcNoteCard v-if="success" type="success">
					<p>Bijlage succesvol bewerkt</p>
				</NcNoteCard>
				<NcNoteCard v-if="!success" type="error">
					<p>Er is iets fout gegaan bij het bewerken van bijlage</p>
				</NcNoteCard>
				<NcNoteCard v-if="error" type="error">
					<p>{{ error }}</p>
				</NcNoteCard>
			</div>
			<div v-if="success === null" class="form-group">
				<NcTextField :disabled="loading"
					label="Titel"
					maxlength="255"
					:value.sync="publicationStore.attachmentItem.title"
					required />
				<NcTextField :disabled="loading"
					label="Samenvatting"
					maxlength="255"
					:value.sync="publicationStore.attachmentItem.summary" />
				<NcTextArea :disabled="loading"
					label="Beschrijving"
					maxlength="255"
					:value.sync="publicationStore.attachmentItem.description" />
				<NcSelect v-bind="labelOptions"
					v-model="publicationStore.attachmentItem.labels" />
				<NcTextField :disabled="loading"
					label="Toegangs URL"
					maxlength="255"
					:value.sync="publicationStore.attachmentItem.accessUrl" />
				<NcTextField :disabled="loading"
					label="Download URL"
					maxlength="255"
					:value.sync="publicationStore.attachmentItem.downloadUrl" />
			</div>
			<NcButton
				v-if="success === null"
				:disabled="!publicationStore.attachmentItem.title || loading"
				type="primary"
				@click="editAttachment()">
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
import { NcButton, NcModal, NcTextField, NcTextArea, NcNoteCard, NcLoadingIcon, NcSelect } from '@nextcloud/vue'
import ContentSaveOutline from 'vue-material-design-icons/ContentSaveOutline.vue'

export default {
	name: 'EditAttachmentModal',
	components: {
		NcModal,
		NcTextField,
		NcTextArea,
		NcButton,
		NcNoteCard,
		NcLoadingIcon,
		NcSelect,
		// Icons
		ContentSaveOutline,
	},
	data() {
		return {

			loading: false,
			success: null,
			error: false,
			labelOptions: {
				inputLabel: 'Labels',
				multiple: true,
				options: ['Besluit', 'Convenant', 'Document', 'Informatieverzoek', 'Inventarisatielijst'],
			},
		}
	},
	methods: {
		editAttachment() {
			this.loading = true
			this.error = false
			fetch(
				`/index.php/apps/opencatalogi/api/attachments/${publicationStore.attachmentItem.id}`,
				{
					method: 'PUT',
					headers: {
						'Content-Type': 'application/json',
					},
					body: JSON.stringify({
						...publicationStore.attachmentItem,
						published: publicationStore.attachmentItem.published === '' ? null : publicationStore.attachmentItem.published,
					}),

				},
			)
				.then((response) => {
					this.loading = false
					this.success = response.ok
					// Lets refresh the catalogiList
					if (publicationStore.publicationItem) {
						publicationStore.getPublicationAttachments(publicationStore.publicationItem?.id)
					}
					response.json().then((data) => {
						publicationStore.setAttachmentItem(data)
					})
					// Wait for the user to read the feedback then close the model
					const self = this
					setTimeout(function() {
						self.success = null
						navigationStore.setModal(false)
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
