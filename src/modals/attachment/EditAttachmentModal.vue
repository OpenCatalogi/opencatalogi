/**
 * EditAttachmentModal.vue
 * Modal for editing attachments
 * @category Components
 * @package opencatalogi
 * @author Ruben Linde
 * @copyright 2024
 * @license AGPL-3.0-or-later
 * @version 1.0.0
 * @link https://github.com/opencatalogi/opencatalogi
 */

 <!-- It is possible that this modal is redundant. So I will disable eslint for this file. -->

<script setup>
/* eslint-disable */
import { ref, computed } from 'vue'
import { objectStore, navigationStore } from '../../store/store.js'
import { NcButton, NcModal, NcTextField, NcSelectTags, NcCheckboxRadioSwitch, NcNoteCard, NcLoadingIcon } from '@nextcloud/vue'

/**
 * Loading state for the component
 * @type {import('vue').Ref<boolean>}
 */
const loading = ref(false)

/**
 * Success state for the component
 * @type {import('vue').Ref<boolean|null>}
 */
const success = ref(null)

/**
 * Error state for the component
 * @type {import('vue').Ref<string|null>}
 */
const error = ref(null)

/**
 * Get the active publication from the store
 * @returns {Object|null}
 */
const publication = computed(() => objectStore.getActiveObject('publication'))

/**
 * Get the active attachment from the store
 * @returns {Object|null}
 */
const attachment = computed(() => objectStore.getActiveObject('attachment'))

/**
 * Handle save action
 * @returns {Promise<void>}
 */
const handleSave = async () => {
	loading.value = true
	try {
		await objectStore.updateObject('attachment', attachment.value)
		success.value = true
	} catch (error) {
		console.error('Error updating attachment:', error)
		success.value = false
		error.value = error.message
	} finally {
		loading.value = false
	}
}

/**
 * Handle cancel action
 * @returns {void}
 */
const handleCancel = () => {
	navigationStore.setModal(false)
}

/**
 * Handle file selection
 * @param {Event} event - The file input event
 * @returns {Promise<void>}
 */
const handleFileSelect = async (event) => {
	const file = event.target.files[0]
	if (file) {
		await objectStore.uploadFile('attachment', file)
	}
}

/**
 * Get available tags
 * @returns {Promise<string[]>}
 */
const getTags = async () => {
	try {
		const response = await objectStore.fetchCollection('tag')
		return response.results.map(tag => tag.name)
	} catch (error) {
		console.error('Error fetching tags:', error)
		return []
	}
}
</script>

<template>
	<NcModal v-if="navigationStore.modal === 'editAttachment'"
		ref="modalRef"
		class="editAttachmentModal"
		label-id="editAttachmentModal"
		@close="handleCancel">
		<div class="modal__content">
			<h2>Bijlage bewerken</h2>
			<div v-if="success !== null || error">
				<NcNoteCard v-if="success" type="success">
					<p>Bijlage succesvol bijgewerkt</p>
				</NcNoteCard>
				<NcNoteCard v-if="!success" type="error">
					<p>Er is iets fout gegaan bij het bijwerken van bijlage</p>
				</NcNoteCard>
				<NcNoteCard v-if="error" type="error">
					<p>{{ error }}</p>
				</NcNoteCard>
			</div>
			<div v-if="success === null" class="form-group">
				<NcTextField
					v-model="attachment.title"
					label="Titel"
					:disabled="loading"
					:loading="loading" />
				<NcTextField
					v-model="attachment.description"
					label="Beschrijving"
					:disabled="loading"
					:loading="loading" />
				<NcSelectTags
					v-model="attachment.tags"
					label="Tags"
					:disabled="loading"
					:loading="loading" />
				<NcCheckboxRadioSwitch
					v-model="attachment.published"
					:disabled="loading"
					:loading="loading">
					Gepubliceerd
				</NcCheckboxRadioSwitch>
			</div>

			<span class="buttonContainer">
				<NcButton
					@click="handleCancel">
					{{ success ? 'Sluiten' : 'Annuleer' }}
				</NcButton>
				<NcButton v-if="success === null"
					:disabled="loading"
					type="primary"
					@click="handleSave">
					<template #icon>
						<span>
							<NcLoadingIcon v-if="loading" :size="20" />
							<ContentSave v-if="!loading" :size="20" />
						</span>
					</template>
					Opslaan
				</NcButton>
			</span>
		</div>
	</NcModal>
</template>

<script>


// icons
import ContentSave from 'vue-material-design-icons/ContentSave.vue'

export default {
	name: 'EditAttachmentModal',
	components: {
		NcModal,
		NcTextField,
		NcSelectTags,
		NcCheckboxRadioSwitch,
		NcButton,
		NcNoteCard,
		NcLoadingIcon,
	},
	data() {
		return {
			loading: false,
			success: null,
			error: null
		}
	},
	methods: {
		closeModal() {
			this.navigationStore.setModal(false)
		}
	}
}
</script>

<style scoped>
.modal__content {
	padding: 20px;
}

.buttonContainer {
	display: flex;
	justify-content: flex-end;
	gap: 10px;
	margin-top: 20px;
}

.form-group {
	display: flex;
	flex-direction: column;
	gap: 10px;
	margin-top: 20px;
}
</style>
