<script setup>
import { navigationStore, menuStore } from '../../store/store.js'
import { createZodErrorHandler } from '../../services/formatZodErrors.js'
import { EventBus } from '../../eventBus.js'
</script>

<template>
	<NcDialog :name="IS_EDIT ? 'Edit Menu Item' : 'Add Menu Item'"
		size="normal"
		:can-close="false">
		<NcNoteCard v-if="success" type="success">
			<p>Menu item succesvol {{ IS_EDIT ? 'bewerkt' : 'toegevoegd' }}</p>
		</NcNoteCard>
		<NcNoteCard v-if="error" type="error">
			<p>{{ error }}</p>
		</NcNoteCard>

		<div v-if="!success" class="formContainer">
			<NcTextField
				:disabled="loading"
				label="Naam"
				:value.sync="menuItem.name"
				:error="!!inputValidation.getError(`items.${index}.name`)"
				:helper-text="inputValidation.getError(`items.${index}.name`)" />

			<NcTextField
				:disabled="loading"
				label="Description"
				:value.sync="menuItem.description"
				:error="!!inputValidation.getError(`items.${index}.description`)"
				:helper-text="inputValidation.getError(`items.${index}.description`)" />

			<NcTextField
				:disabled="loading"
				label="Slug"
				:value.sync="menuItem.slug"
				:error="!!inputValidation.getError(`items.${index}.slug`)"
				:helper-text="inputValidation.getError(`items.${index}.slug`)" />

			<NcTextField
				:disabled="loading"
				label="Link"
				:value.sync="menuItem.link"
				:error="!!inputValidation.getError(`items.${index}.link`)"
				:helper-text="inputValidation.getError(`items.${index}.link`)" />

			<NcSelect v-bind="iconOptions"
				v-model="iconOptions.value"
				input-label="Icon"
				:disabled="loading" />
		</div>

		<template #actions>
			<NcButton
				@click="closeModal">
				<template #icon>
					<Cancel :size="20" />
				</template>
				{{ success ? 'Sluiten' : 'Annuleren' }}
			</NcButton>
			<NcButton v-if="!success"
				v-tooltip="inputValidation.flatErrorMessages[0]"
				:disabled="loading || !inputValidation.success"
				type="primary"
				@click="editMenu()">
				<template #icon>
					<NcLoadingIcon v-if="loading" :size="20" />
					<ContentSaveOutline v-if="!loading && menuStore.menuItem?.id" :size="20" />
					<Plus v-if="!loading && !menuStore.menuItem?.id" :size="20" />
				</template>
				{{ menuStore.menuItem?.id ? 'Opslaan' : 'Toevoegen' }}
			</NcButton>
		</template>
	</NcDialog>
</template>

<script>
import {
	NcButton,
	NcDialog,
	NcLoadingIcon,
	NcNoteCard,
	NcTextField,
	NcSelect,
} from '@nextcloud/vue'

import ContentSaveOutline from 'vue-material-design-icons/ContentSaveOutline.vue'
import Cancel from 'vue-material-design-icons/Cancel.vue'
import Plus from 'vue-material-design-icons/Plus.vue'

import { Menu } from '../../entities/index.js'
import _ from 'lodash'

/**
 * Component for editing menu items
 */
export default {
	name: 'EditMenuItemModal',
	components: {
		NcDialog,
		NcButton,
		NcLoadingIcon,
		NcNoteCard,
		NcTextField,
		NcSelect,
		// Icons
		ContentSaveOutline,
		Cancel,
		Plus,
	},
	data() {
		return {
			IS_EDIT: menuStore.menuItemsItemId !== null,
			index: menuStore.menuItemsItemId ?? menuStore.menuItem.items.length,
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
			success: null,
			loading: false,
			error: false,
			hasUpdated: false,
			closeDialogTimeout: null,
		}
	},
	computed: {
		inputValidation() {
			// Create the updated menu item with icon
			const updatedMenuItem = {
				...this.menuItem,
				icon: this.iconOptions.value?.value || '',
			}

			// Determine the new items array based on whether we're editing or adding
			const updatedItems = this.IS_EDIT
				? [
					...menuStore.menuItem.items.slice(0, this.index),
					updatedMenuItem,
					...menuStore.menuItem.items.slice(this.index + 1),
				]
				: [...menuStore.menuItem.items, updatedMenuItem]

			const menuItem = new Menu({
				...menuStore.menuItem,
				items: updatedItems,
			})

			const result = menuItem.validate()

			return createZodErrorHandler(result)
		},
	},
	mounted() {
		this.initializeMenuItem()
	},
	methods: {
		/**
		 * Initialize menu item data from store
		 */
		initializeMenuItem() {
			if (this.IS_EDIT) {
				this.menuItem = {
					...this.menuItem,
					...menuStore.menuItem.items[menuStore.menuItemsItemId],
				}

				this.iconOptions.value = this.iconOptions.options.find(option => option.value === this.menuItem.icon)
			}
		},
		/**
		 * Close the dialog and reset state
		 */
		closeModal() {
			navigationStore.setModal(false)
			clearTimeout(this.closeModalTimeout)
			menuStore.menuItemsItemId = null
		},
		/**
		 * Save menu item changes
		 */
		async editMenu() {
			this.loading = true

			const cloneMenu = _.cloneDeep(menuStore.menuItem)

			// Create the updated menu item with icon
			const updatedMenuItem = {
				...this.menuItem,
				icon: this.iconOptions.value?.value || '',
			}

			// Determine the new items array based on whether we're editing or adding
			const updatedItems = this.IS_EDIT
				? [
					...cloneMenu.items.slice(0, this.index),
					updatedMenuItem,
					...cloneMenu.items.slice(this.index + 1),
				]
				: [...cloneMenu.items, updatedMenuItem]

			const menuItem = new Menu({
				...cloneMenu,
				items: updatedItems,
			})

			menuStore.saveMenu(menuItem)
				.then(({ response }) => {
					this.success = response.ok
					this.error = false
					response.ok && (this.closeModalTimeout = setTimeout(this.closeModal, 2000))

					// Emit a specific event through the eventBus
					// which gets picked up by the details page
					EventBus.$emit('edit-menu-item-item-success')
				}).catch((error) => {
					this.success = false
					this.error = error.message || 'An error occurred while saving the menu'
				}).finally(() => {
					this.loading = false
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
