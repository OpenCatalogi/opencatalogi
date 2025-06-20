/**
 * PageModal.vue
 * Component for adding and editing pages
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
	<NcModal
		ref="modalRef"
		:label-id="isEdit ? 'editPageModal' : 'addPageModal'"
		@close="closeModal">
		<div class="modal__content">
			<h2>Pagina {{ isEdit ? 'bewerken' : 'toevoegen' }}</h2>
			<div v-if="objectStore.getState('page').success !== null || objectStore.getState('page').error">
				<NcNoteCard v-if="objectStore.getState('page').success" type="success">
					<p>{{ isEdit ? 'Pagina succesvol bewerkt' : 'Pagina succesvol toegevoegd' }}</p>
				</NcNoteCard>
				<NcNoteCard v-if="!objectStore.getState('page').success" type="error">
					<p>{{ isEdit ? 'Er is iets fout gegaan bij het bewerken van de pagina' : 'Er is iets fout gegaan bij het toevoegen van pagina' }}</p>
				</NcNoteCard>
				<NcNoteCard v-if="objectStore.getState('page').error" type="error">
					<p>{{ objectStore.getState('page').error }}</p>
				</NcNoteCard>
			</div>
			<div v-if="objectStore.getState('page').success === null" class="formContainer">
				<NcTextField
					:disabled="objectStore.isLoading('page')"
					label="Titel"
					:value.sync="page.title"
					:error="!!inputValidation.fieldErrors?.['title']"
					:helper-text="inputValidation.fieldErrors?.['title']?.[0]" />
				<NcTextField
					:disabled="objectStore.isLoading('page')"
					label="Slug"
					:value.sync="page.slug"
					:error="!!inputValidation.fieldErrors?.['slug']"
					:helper-text="inputValidation.fieldErrors?.['slug']?.[0]" />
			</div>
			<NcButton v-if="objectStore.getState('page').success === null"
				v-tooltip="inputValidation.errorMessages?.[0]"
				:disabled="!inputValidation.success || objectStore.isLoading('page')"
				type="primary"
				@click="savePage">
				<template #icon>
					<NcLoadingIcon v-if="objectStore.isLoading('page')" :size="20" />
					<Plus v-if="!objectStore.isLoading('page')" :size="20" />
				</template>
				{{ isEdit ? 'Opslaan' : 'Toevoegen' }}
			</NcButton>
		</div>
	</NcModal>
</template>

<script>
import {
	NcButton,
	NcLoadingIcon,
	NcModal,
	NcNoteCard,
	NcTextField,
} from '@nextcloud/vue'

import Plus from 'vue-material-design-icons/Plus.vue'

import { Page } from '../../entities/index.js'

export default {
	name: 'PageModal',
	components: {
		NcModal,
		NcTextField,
		NcButton,
		NcLoadingIcon,
		NcNoteCard,
		// Icons
		Plus,
	},
	data() {
		return {
			page: {
				title: '',
				slug: '',
			},
			hasUpdated: false,
		}
	},
	computed: {
		isEdit() {
			return !!objectStore.getActiveObject('page')
		},
		inputValidation() {
			const pageItem = new Page({
				...this.page,
			})

			const result = pageItem.validate()

			return {
				success: result.success,
				errorMessages: result?.error?.issues.map((issue) => `${issue.path.join('.')}: ${issue.message}`) || [],
				fieldErrors: result?.error?.formErrors?.fieldErrors || {},
			}
		},
	},
	updated() {
		if (navigationStore.modal === 'page' && !this.hasUpdated) {
			if (this.isEdit) {
				const activePage = objectStore.getActiveObject('page')
				this.page = { ...activePage }
			}
			this.hasUpdated = true
		}
	},
	methods: {
		closeModal() {
			navigationStore.setModal(false)
			this.hasUpdated = false
			this.page = {
				title: '',
				slug: '',
			}
			// Reset the object store state
			objectStore.setState('page', { success: null, error: null })
		},
		savePage() {
			const pageItem = new Page({
				...this.page,
			})

			if (this.isEdit) {
				objectStore.updateObject('page', pageItem.id, pageItem)
					.then(() => {
						// Wait for the user to read the feedback then close the model
						const self = this
						setTimeout(function() {
							self.closeModal()
						}, 2000)
					})
			} else {
				objectStore.createObject('page', pageItem)
					.then(() => {
						// Wait for the user to read the feedback then close the model
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

.formContainer > * {
  margin-block-end: 10px;
}

.success {
  color: green;
}

.pageSpacing {
	display: flex;
	flex-direction: column;
	gap: 5px;
}
</style>
