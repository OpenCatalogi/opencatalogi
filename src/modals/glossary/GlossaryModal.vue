/**
 * GlossaryModal.vue
 * Modal component for creating and editing glossary items
 * @category Modals
 * @package opencatalogi
 * @author Ruben Linde
 * @copyright 2024
 * @license AGPL-3.0-or-later
 * @version 1.0.0
 * @link https://github.com/opencatalogi/opencatalogi
 */

<script setup>
import { navigationStore, objectStore } from '../../store/store.js'
</script>

<template>
	<NcModal v-if="navigationStore.modal === 'glossary'"
		ref="modalRef"
		:label-id="isEdit ? 'editGlossaryModal' : 'addGlossaryModal'"
		@close="closeModal">
		<div class="modal__content">
			<h2>Term {{ isEdit ? 'bewerken' : 'toevoegen' }}</h2>
			<div v-if="objectStore.getState('glossary').success !== null || objectStore.getState('glossary').error">
				<NcNoteCard v-if="objectStore.getState('glossary').success" type="success">
					<p>{{ isEdit ? 'Term succesvol bewerkt' : 'Term succesvol toegevoegd' }}</p>
				</NcNoteCard>
				<NcNoteCard v-if="!objectStore.getState('glossary').success" type="error">
					<p>{{ isEdit ? 'Er is iets fout gegaan bij het bewerken van de term' : 'Er is iets fout gegaan bij het toevoegen van de term' }}</p>
				</NcNoteCard>
				<NcNoteCard v-if="objectStore.getState('glossary').error" type="error">
					<p>{{ objectStore.getState('glossary').error }}</p>
				</NcNoteCard>
			</div>
			<div v-if="objectStore.getState('glossary').success === null && !objectStore.isLoading('glossary')" class="form-group">
				<NcTextField :disabled="objectStore.isLoading('glossary')"
					label="Titel*"
					maxlength="255"
					:value.sync="glossary.title"
					:error="!!inputValidation.fieldErrors?.['title']"
					:helper-text="inputValidation.fieldErrors?.['title']?.[0]" />
				<NcTextField :disabled="objectStore.isLoading('glossary')"
					label="Samenvatting"
					maxlength="255"
					:value.sync="glossary.summary"
					:error="!!inputValidation.fieldErrors?.['summary']"
					:helper-text="inputValidation.fieldErrors?.['summary']?.[0]" />
				<NcTextField :disabled="objectStore.isLoading('glossary')"
					label="Beschrijving"
					type="textarea"
					:value.sync="glossary.description"
					:error="!!inputValidation.fieldErrors?.['description']"
					:helper-text="inputValidation.fieldErrors?.['description']?.[0]" />
				<NcTextField :disabled="objectStore.isLoading('glossary')"
					label="Externe link"
					:value.sync="glossary.externalLink"
					:error="!!inputValidation.fieldErrors?.['externalLink']"
					:helper-text="inputValidation.fieldErrors?.['externalLink']?.[0]" />
				<NcSelectTags v-model="glossary.keywords"
					:disabled="objectStore.isLoading('glossary')"
					label="Keywords"
					:error="!!inputValidation.fieldErrors?.['keywords']"
					:helper-text="inputValidation.fieldErrors?.['keywords']?.[0]"
					placeholder="Voeg keywords toe" />
			</div>
			<div v-if="objectStore.isLoading('glossary')" class="loading-status">
				<NcLoadingIcon :size="20" />
				<span>{{ isEdit ? 'Term wordt bewerkt...' : 'Term wordt toegevoegd...' }}</span>
			</div>
			<NcButton v-if="objectStore.getState('glossary').success === null && !objectStore.isLoading('glossary')"
				v-tooltip="inputValidation.errorMessages?.[0]"
				:disabled="!inputValidation.success || objectStore.isLoading('glossary')"
				type="primary"
				class="glossary-submit-button"
				@click="saveGlossary">
				<template #icon>
					<ContentSaveOutline :size="20" />
				</template>
				{{ isEdit ? 'Opslaan' : 'Toevoegen' }}
			</NcButton>
		</div>
	</NcModal>
</template>

<script>
import { NcButton, NcModal, NcTextField, NcLoadingIcon, NcNoteCard, NcSelectTags } from '@nextcloud/vue'
import ContentSaveOutline from 'vue-material-design-icons/ContentSaveOutline.vue'

import { Glossary } from '../../entities/index.js'

export default {
	name: 'GlossaryModal',
	components: {
		NcModal,
		NcTextField,
		NcButton,
		NcLoadingIcon,
		NcNoteCard,
		NcSelectTags,
		ContentSaveOutline,
	},
	data() {
		return {
			glossary: {
				title: '',
				summary: '',
				description: '',
				externalLink: '',
				keywords: [],
			},
			hasUpdated: false,
		}
	},
	computed: {
		isEdit() {
			return !!objectStore.getActiveObject('glossary')
		},
		inputValidation() {
			const glossaryItem = new Glossary(this.glossary)
			const result = glossaryItem.validate()

			return {
				success: result.success,
				errorMessages: result?.error?.issues.map((issue) => `${issue.path.join('.')}: ${issue.message}`) || [],
				fieldErrors: result?.error?.formErrors?.fieldErrors || {},
			}
		},
	},
	updated() {
		if (navigationStore.modal === 'glossary' && !this.hasUpdated) {
			if (this.isEdit) {
				const activeGlossary = objectStore.getActiveObject('glossary')
				this.glossary = { ...activeGlossary }
			}
			this.hasUpdated = true
		}
	},
	methods: {
		closeModal() {
			navigationStore.setModal(false)
			this.hasUpdated = false
			this.glossary = {
				title: '',
				summary: '',
				description: '',
				externalLink: '',
				keywords: [],
			}
			objectStore.setState('glossary', { success: null, error: null })
		},
		saveGlossary() {
			const glossaryItem = new Glossary(this.glossary)

			if (this.isEdit) {
				objectStore.updateObject('glossary', glossaryItem.id, glossaryItem)
					.then(() => {
						const self = this
						setTimeout(function() {
							self.closeModal()
						}, 2000)
					})
			} else {
				objectStore.createObject('glossary', glossaryItem)
					.then(() => {
						const self = this
						setTimeout(function() {
							self.closeModal()
						}, 2000)
					})
			}
		},
	},
}
</script>

<style>
.modal__content {
    margin: var(--OC-margin-50);
    text-align: center;
}

.glossary-submit-button {
    margin-block-start: 1rem;
}

.loading-status {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
    margin: 1rem 0;
    color: var(--color-text-lighter);
}
</style>

<style scoped>
.form-group {
	display: flex;
	flex-direction: column;
	gap: var(--OC-margin-10);
}
</style>
