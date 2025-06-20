/**
 * MenuModal.vue
 * Modal component for creating and editing menus
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
		:label-id="isEdit ? 'editMenuModal' : 'addMenuModal'"
		@close="closeModal">
		<div class="modal__content">
			<h2>Menu {{ isEdit ? 'bewerken' : 'toevoegen' }}</h2>

			<div v-if="objectStore.getState('menu').success !== null || objectStore.getState('menu').error">
				<NcNoteCard v-if="objectStore.getState('menu').success" type="success">
					<p>{{ isEdit ? 'Menu succesvol bewerkt' : 'Menu succesvol toegevoegd' }}</p>
				</NcNoteCard>
				<NcNoteCard v-if="!objectStore.getState('menu').success" type="error">
					<p>{{ isEdit ? 'Er is iets fout gegaan bij het bewerken van het menu' : 'Er is iets fout gegaan bij het toevoegen van het menu' }}</p>
				</NcNoteCard>
				<NcNoteCard v-if="objectStore.getState('menu').error" type="error">
					<p>{{ objectStore.getState('menu').error }}</p>
				</NcNoteCard>
			</div>
			<div v-if="objectStore.getState('menu').success === null && !objectStore.isLoading('menu')" class="form-group">
				<NcTextField :disabled="objectStore.isLoading('menu')"
					label="Titel*"
					maxlength="255"
					:value.sync="menu.title"
					:error="!!inputValidation.fieldErrors?.['title']"
					:helper-text="inputValidation.fieldErrors?.['title']?.[0]" />
				<NcTextField :disabled="objectStore.isLoading('menu')"
					label="Slug*"
					maxlength="255"
					:value.sync="menu.slug"
					:error="!!inputValidation.fieldErrors?.['slug']"
					:helper-text="inputValidation.fieldErrors?.['slug']?.[0]" />
				<NcTextField :disabled="objectStore.isLoading('menu')"
					label="Link*"
					maxlength="255"
					:value.sync="menu.link"
					:error="!!inputValidation.fieldErrors?.['link']"
					:helper-text="inputValidation.fieldErrors?.['link']?.[0]" />
				<NcTextField :disabled="objectStore.isLoading('menu')"
					label="Beschrijving"
					maxlength="255"
					:value.sync="menu.description"
					:error="!!inputValidation.fieldErrors?.['description']"
					:helper-text="inputValidation.fieldErrors?.['description']?.[0]" />
				<NcTextField :disabled="objectStore.isLoading('menu')"
					label="Icoon"
					maxlength="255"
					:value.sync="menu.icon"
					:error="!!inputValidation.fieldErrors?.['icon']"
					:helper-text="inputValidation.fieldErrors?.['icon']?.[0]" />
				<NcTextField :disabled="objectStore.isLoading('menu')"
					label="Positie*"
					type="number"
					min="0"
					:value="menu.position"
					:error="!!inputValidation.fieldErrors?.['position']"
					:helper-text="inputValidation.fieldErrors?.['position']?.[0]"
					@update:value="handlePositionUpdate" />
				<div class="position-info">
					<p>0 - rechts boven</p>
					<p>1 - navigatiebalk</p>
					<p>2 - footer</p>
				</div>
			</div>
			<div v-if="objectStore.isLoading('menu')" class="loading-status">
				<NcLoadingIcon :size="20" />
				<span>{{ isEdit ? 'Menu wordt bewerkt...' : 'Menu wordt toegevoegd...' }}</span>
			</div>
			<NcButton v-if="objectStore.getState('menu').success === null && !objectStore.isLoading('menu')"
				v-tooltip="inputValidation.errorMessages?.[0]"
				:disabled="!inputValidation.success || objectStore.isLoading('menu')"
				type="primary"
				class="menu-submit-button"
				@click="saveMenu">
				<template #icon>
					<ContentSaveOutline :size="20" />
				</template>
				{{ isEdit ? 'Opslaan' : 'Toevoegen' }}
			</NcButton>
		</div>
	</NcModal>
</template>

<script>
import { NcButton, NcModal, NcTextField, NcLoadingIcon, NcNoteCard } from '@nextcloud/vue'
import { Menu } from '../../entities/index.js'
import _ from 'lodash'

// Icons
import ContentSaveOutline from 'vue-material-design-icons/ContentSaveOutline.vue'

export default {
	name: 'MenuModal',
	components: {
		NcModal,
		NcTextField,
		NcButton,
		NcLoadingIcon,
		NcNoteCard,
		ContentSaveOutline,
	},
	data() {
		return {
			isEdit: !!objectStore.getActiveObject('menu')?.id,
			menu: {
				title: '',
				slug: '',
				link: '',
				description: '',
				icon: '',
				position: 0,
				items: [],
			},
			hasUpdated: false,
		}
	},
	computed: {
		inputValidation() {
			const menuItem = new Menu(this.menu)
			const result = menuItem.validate()

			return {
				success: result.success,
				errorMessages: result?.error?.issues.map((issue) => `${issue.path.join('.')}: ${issue.message}`) || [],
				fieldErrors: result?.error?.formErrors?.fieldErrors || {},
			}
		},
	},
	mounted() {
		if (this.isEdit) {
			this.menu = {
				...this.menu,
				..._.cloneDeep(objectStore.getActiveObject('menu')),
			}
		}
	},
	methods: {
		handlePositionUpdate(value) {
			this.menu.position = parseInt(value, 10) || 0
		},
		closeModal() {
			navigationStore.setModal(false)
			this.hasUpdated = false
			this.menu = {
				title: '',
				slug: '',
				link: '',
				description: '',
				icon: '',
				position: 0,
				items: [],
			}
			objectStore.setState('menu', { success: null, error: null })
		},
		saveMenu() {
			const menuItem = new Menu({
				...this.menu,
				position: parseInt(this.menu.position, 10) || 0,
			})

			if (this.isEdit) {
				objectStore.updateObject('menu', menuItem.id, menuItem)
					.then(() => {
						const self = this
						setTimeout(function() {
							self.closeModal()
						}, 2000)
					})
			} else {
				objectStore.createObject('menu', menuItem)
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

.menu-submit-button {
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

.position-info {
    text-align: left;
    color: var(--color-text-lighter);
    font-size: 0.9em;
    margin-top: -0.5rem;
    margin-bottom: 0.5rem;
}
</style>

<style scoped>
.form-group {
	display: flex;
	flex-direction: column;
	gap: var(--OC-margin-10);
}
</style>
