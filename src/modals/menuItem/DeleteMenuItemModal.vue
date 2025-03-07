<script setup>
import { menuStore, navigationStore } from '../../store/store.js'
import { EventBus } from '../../eventBus.js'
</script>

<template>
	<NcDialog name="Delete Menu Item"
		size="normal"
		:can-close="false">
		<p v-if="success === null">
			Weet je zeker dat je het menu item <b>{{ menuStore.menuItem?.items[menuStore.menuItemItemsIndex]?.name }}</b> wilt verwijderen? Dit kan niet ongedaan worden gemaakt.
		</p>

		<NcNoteCard v-if="success" type="success">
			<p>Menu item succesvol verwijderd</p>
		</NcNoteCard>
		<NcNoteCard v-if="error" type="error">
			<p>{{ error }}</p>
		</NcNoteCard>

		<template #actions>
			<NcButton @click="closeModal">
				<template #icon>
					<Cancel :size="20" />
				</template>
				{{ success === null ? 'Cancel' : 'Close' }}
			</NcButton>
			<NcButton
				v-if="success === null"
				:disabled="loading"
				type="error"
				@click="deleteMenu()">
				<template #icon>
					<NcLoadingIcon v-if="loading" :size="20" />
					<TrashCanOutline v-if="!loading" :size="20" />
				</template>
				Delete
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
} from '@nextcloud/vue'
import _ from 'lodash'

import Cancel from 'vue-material-design-icons/Cancel.vue'
import TrashCanOutline from 'vue-material-design-icons/TrashCanOutline.vue'
import { Menu } from '../../entities/index.js'

/**
 * Component for deleting menu items
 */
export default {
	name: 'DeleteMenuItemModal',
	components: {
		NcDialog,
		NcButton,
		NcLoadingIcon,
		NcNoteCard,
		// Icons
		TrashCanOutline,
		Cancel,
	},
	data() {
		return {
			success: null,
			loading: false,
			error: false,
			closeModalTimeout: null,
		}
	},
	methods: {
		/**
		 * Closes the delete dialog and resets state
		 */
		closeModal() {
			navigationStore.setModal(false)
			clearTimeout(this.closeModalTimeout)
			menuStore.menuItemItemsIndex = null
		},
		/**
		 * Deletes the selected menu item
		 */
		async deleteMenu() {
			this.loading = true

			const menuItemClone = _.cloneDeep(menuStore.menuItem)
			menuItemClone.items.splice(menuStore.menuItemItemsIndex, 1)

			const newMenuItem = new Menu(menuItemClone)

			menuStore.saveMenu(newMenuItem)
				.then(({ response }) => {
					this.success = response.ok
					this.error = false
					response.ok && (this.closeModalTimeout = setTimeout(this.closeModal, 2000))

					// Emit a specific event through the eventBus
					// which gets picked up by the details page
					EventBus.$emit('delete-menu-item-item-success')
				}).catch((error) => {
					this.success = false
					this.error = error.message || 'An error occurred while deleting the menu'
				}).finally(() => {
					this.loading = false
				})
		},
	},
}
</script>
