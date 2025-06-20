/**
 * AddAttachmentModal.vue
 * Modal for adding attachments
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
 * New attachment data
 * @type {import('vue').Ref<Object>}
 */
const newAttachment = ref({
	title: '',
	description: '',
	tags: [],
	published: false
})

/**
 * Get the active publication from the store
 * @returns {Object|null}
 */
const publication = computed(() => objectStore.getActiveObject('publication'))

/**
 * Handle save action
 * @returns {Promise<void>}
 */
const handleSave = async () => {
	loading.value = true
	try {
		await objectStore.createObject('attachment', {
			...newAttachment.value,
			publicationId: publication.value?.id
		})
		success.value = true
	} catch (error) {
		console.error('Error creating attachment:', error)
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
</script>

<template>
	<NcModal v-if="navigationStore.modal === 'addAttachment'"
		ref="modalRef"
		class="addAttachmentModal"
		label-id="addAttachmentModal"
		@close="handleCancel">
		<div class="modal__content">
			<h2>Bijlage toevoegen</h2>
			<div v-if="success !== null || error">
				<NcNoteCard v-if="success" type="success">
					<p>Bijlage succesvol toegevoegd</p>
				</NcNoteCard>
				<NcNoteCard v-if="!success" type="error">
					<p>Er is iets fout gegaan bij het toevoegen van bijlage</p>
				</NcNoteCard>
				<NcNoteCard v-if="error" type="error">
					<p>{{ error }}</p>
				</NcNoteCard>
			</div>
			<div v-if="success === null" class="form-group">
				<NcTextField
					v-model="newAttachment.title"
					label="Titel"
					:disabled="loading"
					:loading="loading" />
				<NcTextField
					v-model="newAttachment.description"
					label="Beschrijving"
					:disabled="loading"
					:loading="loading" />
				<NcSelectTags
					v-model="newAttachment.tags"
					label="Tags"
					:disabled="loading"
					:loading="loading" />
				<NcCheckboxRadioSwitch
					v-model="newAttachment.published"
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
							<Plus v-if="!loading" :size="20" />
						</span>
					</template>
					Toevoegen
				</NcButton>
			</span>
		</div>
	</NcModal>
</template>

<script>

// icons
import Plus from 'vue-material-design-icons/Plus.vue'

export default {
	name: 'AddAttachmentModal',
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
