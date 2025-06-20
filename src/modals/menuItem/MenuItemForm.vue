<script setup>
import { objectStore, navigationStore } from '../../store/store.js'
import { createZodErrorHandler } from '../../services/formatZodErrors.js'
import { EventBus } from '../../eventBus.js'
</script>

<template>
	<NcModal ref="modalRef"
		:label-id="isEdit ? 'editMenuItem' : 'addMenuItem'"
		@close="closeModal">
		<div class="modal__content">
			<h2>{{ isEdit ? 'Edit Menu Item' : 'Add Menu Item' }}</h2>

			<div v-if="objectStore.getState('menu').success !== null || objectStore.getState('menu').error">
				<NcNoteCard v-if="objectStore.getState('menu').success" type="success">
					<p>Menu item succesvol {{ isEdit ? 'bewerkt' : 'toegevoegd' }}</p>
				</NcNoteCard>
				<NcNoteCard v-if="objectStore.getState('menu').error" type="error">
					<p>{{ objectStore.getState('menu').error }}</p>
				</NcNoteCard>
			</div>

			<div v-if="objectStore.getState('menu').success === null" class="form-container">
				<NcTextField
					:disabled="objectStore.isLoading('menu')"
					label="Naam"
					:value.sync="menuItem.name"
					:error="!!inputValidation.getError(`items.${index}.name`)"
					:helper-text="inputValidation.getError(`items.${index}.name`)" />

				<NcTextField
					:disabled="objectStore.isLoading('menu')"
					label="Description"
					:value.sync="menuItem.description"
					:error="!!inputValidation.getError(`items.${index}.description`)"
					:helper-text="inputValidation.getError(`items.${index}.description`)" />

				<NcTextField
					:disabled="objectStore.isLoading('menu')"
					label="Slug"
					:value.sync="menuItem.slug"
					:error="!!inputValidation.getError(`items.${index}.slug`)"
					:helper-text="inputValidation.getError(`items.${index}.slug`)" />

				<NcTextField
					:disabled="objectStore.isLoading('menu')"
					label="Link"
					:value.sync="menuItem.link"
					:error="!!inputValidation.getError(`items.${index}.link`)"
					:helper-text="inputValidation.getError(`items.${index}.link`)" />

				<NcSelect v-bind="iconOptions"
					v-model="iconOptions.value"
					input-label="Icon"
					:disabled="objectStore.isLoading('menu')" />
			</div>

			<NcButton v-if="objectStore.getState('menu').success === null"
				v-tooltip="inputValidation.flatErrorMessages[0]"
				:disabled="objectStore.isLoading('menu') || !inputValidation.success"
				type="primary"
				@click="saveMenuItem">
				<template #icon>
					<NcLoadingIcon v-if="objectStore.isLoading('menu')" :size="20" />
					<ContentSaveOutline v-if="!objectStore.isLoading('menu') && isEdit" :size="20" />
					<Plus v-if="!objectStore.isLoading('menu') && !isEdit" :size="20" />
				</template>
				{{ isEdit ? 'Opslaan' : 'Toevoegen' }}
			</NcButton>
		</div>
	</NcModal>
</template>

<script>
import {
	NcButton,
	NcModal,
	NcLoadingIcon,
	NcNoteCard,
	NcTextField,
	NcSelect,
} from '@nextcloud/vue'
import { Menu } from '../../entities/index.js'
import _ from 'lodash'

import ContentSaveOutline from 'vue-material-design-icons/ContentSaveOutline.vue'
import Plus from 'vue-material-design-icons/Plus.vue'

export default {
	name: 'MenuItemForm',
	components: {
		NcModal,
		NcButton,
		NcLoadingIcon,
		NcNoteCard,
		NcTextField,
		NcSelect,
		// Icons
		ContentSaveOutline,
		Plus,
	},
	data() {
		return {
			isEdit: !!objectStore.getActiveObject('menuItem'),
			index: objectStore.getActiveObject('menuItem')?.index ?? objectStore.getActiveObject('menu').items.length,
			menuItem: {
				name: '',
				slug: '',
				link: '',
				description: '',
				icon: '',
				items: [],
			},
			iconOptions: {
				options: [
					{ label: 'arrow right', value: 'ARROW_RIGHT' },
					{ label: 'chevron right', value: 'CHEVRON_RIGHT' },
					{ label: 'chevron left', value: 'CHEVRON_LEFT' },
					{ label: 'close', value: 'CLOSE' },
					{ label: 'close small', value: 'CLOSE_SMALL' },
					{ label: 'contact', value: 'CONTACT' },
					{ label: 'document', value: 'DOCUMENT' },
					{ label: 'ellipse', value: 'ELLIPSE' },
					{ label: 'external link', value: 'EXTERNAL_LINK' },
					{ label: 'external link blue', value: 'EXTERNAL_LINK_BLUE' },
					{ label: 'external link pink', value: 'EXTERNAL_LINK_PINK' },
					{ label: 'filter', value: 'FILTER' },
					{ label: 'info', value: 'INFO' },
					{ label: 'info blue', value: 'INFO_BLUE' },
					{ label: 'list', value: 'LIST' },
					{ label: 'list blue', value: 'LIST_BLUE' },
					{ label: 'logo', value: 'LOGO' },
					{ label: 'menu', value: 'MENU' },
					{ label: 'question mark', value: 'QUESTION_MARK' },
					{ label: 'question mark vng', value: 'QUESTION_MARK_VNG' },
					{ label: 'search', value: 'SEARCH' },
					{ label: 'github', value: 'GITHUB' },
					{ label: 'common ground', value: 'COMMON_GROUND' },
					{ label: 'key', value: 'KEY' },
					{ label: 'person add', value: 'PERSON_ADD' },
					{ label: 'world', value: 'WORLD' },
					{ label: 'user', value: 'USER' },
					{ label: 'users', value: 'USERS' },
					{ label: 'building', value: 'BUILDING' },
					{ label: 'truck', value: 'TRUCK' },
					{ label: 'cube', value: 'CUBE' },
					{ label: 'hand holding', value: 'HAND_HOLDING' },
					{ label: 'house', value: 'HOUSE' },
					{ label: 'phone', value: 'PHONE' },
				],
				value: '',
			},
			closeModalTimeout: null,
		}
	},
	computed: {
		menuObject() {
			return objectStore.getActiveObject('menu')
		},
		inputValidation() {
			// Create the updated menu item with icon
			const updatedMenuItem = {
				...this.menuItem,
				icon: this.iconOptions.value?.value || '',
			}

			// Determine the new items array based on whether we're editing or adding
			const updatedItems = this.isEdit
				? [
					...this.menuObject.items.slice(0, this.index),
					updatedMenuItem,
					...this.menuObject.items.slice(this.index + 1),
				]
				: [...this.menuObject.items, updatedMenuItem]

			const menuItem = new Menu({
				...this.menuObject,
				items: updatedItems,
			})

			const result = menuItem.validate()

			return createZodErrorHandler(result)
		},
	},
	mounted() {
		if (this.isEdit) {
			const activeMenuItem = objectStore.getActiveObject('menuItem')
			const menuItem = this.menuObject.items.find(item => item.id === activeMenuItem.id)

			if (menuItem) {
				this.menuItem = {
					...this.menuItem,
					...menuItem,
				}

				this.iconOptions.value = this.iconOptions.options.find(option => option.value === menuItem.icon)
			}
		}
	},
	methods: {
		closeModal() {
			navigationStore.setModal(false)
			objectStore.clearActiveObject('menuItem')
			objectStore.setState('menu', { success: null, error: null })
			clearTimeout(this.closeModalTimeout)
		},
		async saveMenuItem() {
			objectStore.setState('menu', { success: null, error: null, loading: true })

			const menuClone = _.cloneDeep(this.menuObject)

			const updatedMenuItem = {
				...this.menuItem,
				icon: this.iconOptions.value?.value || '',
			}

			if (this.isEdit) {
				// Find the index of the item we're editing
				const itemIndex = menuClone.items.findIndex(item => item.id === objectStore.getActiveObject('menuItem').id)
				if (itemIndex !== -1) {
					// Replace the existing item while preserving its ID
					menuClone.items[itemIndex] = {
						...updatedMenuItem,
						id: objectStore.getActiveObject('menuItem').id,
					}
				}
			} else {
				// Add new item with a new ID
				menuClone.items.push({
					...updatedMenuItem,
					id: Math.random().toString(36).substring(2, 12),
				})
			}

			const newMenu = new Menu(menuClone)

			objectStore.updateObject('menu', this.menuObject.id, newMenu)
				.then(() => {
					objectStore.setState('menu', { success: true })
					this.closeModalTimeout = setTimeout(this.closeModal, 2000)
					EventBus.$emit('edit-menu-item-success')
				})
				.catch((error) => {
					objectStore.setState('menu', { error: error.message || 'An error occurred while saving the menu' })
				})
				.finally(() => {
					objectStore.setState('menu', { loading: false })
				})
		},
		prettifyJson() {
			this.menuItem.items = JSON.stringify(JSON.parse(this.menuItem.items), null, 2)
		},
		verifyJsonValidity(jsonInput) {
			if (jsonInput === '') return true
			try {
				JSON.parse(jsonInput)
				return true
			} catch (e) {
				return false
			}
		},
	},
}
</script>

<style scoped>
.modal__content {
	margin: var(--OC-margin-50);
	text-align: center;
}

.form-container {
	display: flex;
	flex-direction: column;
}
</style>
