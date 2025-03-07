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
					:error="!!inputValidation.fieldErrors?.['title']"
					:helper-text="inputValidation.fieldErrors?.['title']?.[0]" />
				<NcTextField :disabled="loading"
					label="Samenvatting"
					maxlength="255"
					:value.sync="publicationStore.attachmentItem.summary"
					:error="!!inputValidation.fieldErrors?.['summary']"
					:helper-text="inputValidation.fieldErrors?.['summary']?.[0]" />
				<NcTextArea :disabled="loading"
					label="Beschrijving"
					maxlength="255"
					:value.sync="publicationStore.attachmentItem.description"
					:error="!!inputValidation.fieldErrors?.['description']"
					:helper-text="inputValidation.fieldErrors?.['description']?.[0]" />
				<NcSelect v-bind="labelOptions"
					v-model="publicationStore.attachmentItem.labels" />
				<NcTextField :disabled="loading"
					label="Toegangs URL"
					maxlength="255"
					:value.sync="publicationStore.attachmentItem.accessUrl"
					:error="!!inputValidation.fieldErrors?.['accessUrl']"
					:helper-text="inputValidation.fieldErrors?.['accessUrl']?.[0]" />
				<NcTextField :disabled="loading"
					label="Download URL"
					maxlength="255"
					:value.sync="publicationStore.attachmentItem.downloadUrl"
					:error="!!inputValidation.fieldErrors?.['downloadUrl']"
					:helper-text="inputValidation.fieldErrors?.['downloadUrl']?.[0]" />
			</div>
			<NcButton v-if="success === null"
				v-tooltip="inputValidation.errorMessages?.[0]"
				:disabled="!inputValidation.success"
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

import { Attachment } from '../../entities/index.js'

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
	computed: {
		inputValidation() {
			const catalogiItem = new Attachment({
				...publicationStore.attachmentItem,
			})

			const result = catalogiItem.validate()

			return {
				success: result.success,
				errorMessages: result?.error?.issues.map((issue) => `${issue.path.join('.')}: ${issue.message}`) || [],
				fieldErrors: result?.error?.formErrors?.fieldErrors || {},
			}
		},
	},
	methods: {
		editAttachment() {
			this.loading = true
			this.error = false

			const newAttachmentItem = new Attachment({
				...publicationStore.attachmentItem,
				published: publicationStore.attachmentItem.published === '' ? null : publicationStore.attachmentItem.published,
			})

			publicationStore.editAttachment(newAttachmentItem)
				.then(({ response }) => {
					this.loading = false
					this.success = response.ok
					// Let's refresh the catalogiList
					if (publicationStore.publicationItem) {
						publicationStore.getPublicationAttachments(publicationStore.publicationItem?.id)
					}
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
