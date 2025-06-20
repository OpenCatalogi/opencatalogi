/**
 * ViewMenuModal.vue
 * Modal component for viewing menu details and menu items
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
	<NcModal v-if="navigationStore.modal === 'viewMenu'"
		ref="modalRef"
		label-id="viewMenuModal"
		@close="closeModal">
		<div class="modal__content">
			<h2>{{ menu?.title || 'Menu' }}</h2>
			
			<div v-if="menu" class="menuDetails">
				<div class="detailSection">
					<h3>{{ t('opencatalogi', 'Basic Information') }}</h3>
					<div class="detailGrid">
						<div class="detailItem">
							<strong>{{ t('opencatalogi', 'Title') }}:</strong>
							<span>{{ menu.title || '-' }}</span>
						</div>
						<div v-if="menu.slug" class="detailItem">
							<strong>{{ t('opencatalogi', 'Slug') }}:</strong>
							<span>{{ menu.slug }}</span>
						</div>
						<div v-if="menu.link" class="detailItem">
							<strong>{{ t('opencatalogi', 'Link') }}:</strong>
							<span>
								<a :href="menu.link" target="_blank" rel="noopener noreferrer">
									{{ menu.link }}
								</a>
							</span>
						</div>
						<div v-if="menu.description" class="detailItem">
							<strong>{{ t('opencatalogi', 'Description') }}:</strong>
							<span>{{ menu.description }}</span>
						</div>
						<div v-if="menu.icon" class="detailItem">
							<strong>{{ t('opencatalogi', 'Icon') }}:</strong>
							<span>{{ menu.icon }}</span>
						</div>
						<div class="detailItem">
							<strong>{{ t('opencatalogi', 'Position') }}:</strong>
							<span>
								<span v-if="menu.position === 0">Header ({{ menu.position }})</span>
								<span v-else-if="menu.position === 1">Navigation Bar ({{ menu.position }})</span>
								<span v-else-if="menu.position === 2">Footer ({{ menu.position }})</span>
								<span v-else>{{ menu.position }} - Undefined</span>
							</span>
						</div>
						<div v-if="menu.updatedAt" class="detailItem">
							<strong>{{ t('opencatalogi', 'Last Updated') }}:</strong>
							<span>{{ new Date(menu.updatedAt).toLocaleDateString() }}</span>
						</div>
					</div>
				</div>

				<div v-if="menu.items?.length" class="detailSection">
					<h3>{{ t('opencatalogi', 'Menu Items') }} ({{ menu.items.length }})</h3>
					<div class="menuItemsList">
						<div v-for="(item, index) in menu.items" 
							:key="item.id || index" 
							class="menuItem">
							<div class="menuItemHeader">
								<strong>{{ item.title || item.name || `Item ${index + 1}` }}</strong>
								<span v-if="item.order !== undefined" class="menuItemOrder">
									Order: {{ item.order }}
								</span>
							</div>
							<div v-if="item.description" class="menuItemDescription">
								{{ item.description }}
							</div>
							<div v-if="item.url || item.link" class="menuItemLink">
								<strong>Link:</strong> 
								<a :href="item.url || item.link" target="_blank" rel="noopener noreferrer">
									{{ item.url || item.link }}
								</a>
							</div>
							<div v-if="item.icon" class="menuItemIcon">
								<strong>Icon:</strong> {{ item.icon }}
							</div>
							<div v-if="item.type" class="menuItemType">
								<strong>Type:</strong> {{ item.type }}
							</div>
						</div>
					</div>
				</div>

				<div v-else class="detailSection">
					<h3>{{ t('opencatalogi', 'Menu Items') }}</h3>
					<p class="emptyMenuItems">{{ t('opencatalogi', 'No menu items configured') }}</p>
				</div>

				<div v-if="menu.metadata" class="detailSection">
					<h3>{{ t('opencatalogi', 'Metadata') }}</h3>
					<div class="metadataContainer">
						<pre>{{ JSON.stringify(menu.metadata, null, 2) }}</pre>
					</div>
				</div>
			</div>

			<div v-else class="emptyState">
				<p>{{ t('opencatalogi', 'No menu selected') }}</p>
			</div>

			<div class="modalActions">
				<NcButton type="secondary" @click="openEditModal">
					<template #icon>
						<Pencil :size="20" />
					</template>
					{{ t('opencatalogi', 'Edit Menu') }}
				</NcButton>
				<NcButton type="secondary" @click="openAddItemModal">
					<template #icon>
						<Plus :size="20" />
					</template>
					{{ t('opencatalogi', 'Add Item') }}
				</NcButton>
				<NcButton @click="closeModal">
					{{ t('opencatalogi', 'Close') }}
				</NcButton>
			</div>
		</div>
	</NcModal>
</template>

<script>
import { NcButton, NcModal } from '@nextcloud/vue'
import Pencil from 'vue-material-design-icons/Pencil.vue'
import Plus from 'vue-material-design-icons/Plus.vue'

export default {
	name: 'ViewMenuModal',
	components: {
		NcModal,
		NcButton,
		Pencil,
		Plus,
	},
	computed: {
		/**
		 * Get the currently active menu from the store
		 * @return {object|null} The active menu object
		 */
		menu() {
			return objectStore.getActiveObject('menu')
		},
	},
	methods: {
		/**
		 * Close the modal and clear the active object
		 * @return {void}
		 */
		closeModal() {
			navigationStore.setModal(false)
			objectStore.clearActiveObject('menu')
		},
		/**
		 * Open the edit modal for the current menu
		 * @return {void}
		 */
		openEditModal() {
			navigationStore.setModal('menu')
		},
		/**
		 * Open the add menu item modal
		 * @return {void}
		 */
		openAddItemModal() {
			navigationStore.setModal('menuItemForm')
		},
	},
}
</script>

<style scoped>
.modal__content {
	margin: var(--OC-margin-50);
	text-align: left;
	max-width: 80vw;
	max-height: 80vh;
	overflow-y: auto;
}

.menuDetails {
	display: flex;
	flex-direction: column;
	gap: var(--OC-margin-20);
	margin-top: var(--OC-margin-20);
}

.detailSection {
	border: 1px solid var(--color-border);
	border-radius: var(--border-radius);
	padding: var(--OC-margin-20);
}

.detailSection h3 {
	margin: 0 0 var(--OC-margin-15) 0;
	color: var(--color-primary);
	font-weight: bold;
}

.detailGrid {
	display: grid;
	grid-template-columns: 1fr;
	gap: var(--OC-margin-10);
}

.detailItem {
	display: flex;
	flex-direction: column;
	gap: var(--OC-margin-5);
}

.detailItem strong {
	color: var(--color-text-maxcontrast);
	font-size: 0.9em;
}

.detailItem span {
	color: var(--color-main-text);
}

.detailItem a {
	color: var(--color-primary);
	text-decoration: none;
}

.detailItem a:hover {
	text-decoration: underline;
}

.menuItemsList {
	display: flex;
	flex-direction: column;
	gap: var(--OC-margin-15);
}

.menuItem {
	padding: var(--OC-margin-15);
	background-color: var(--color-background-hover);
	border-radius: var(--border-radius);
	border-left: 3px solid var(--color-primary);
}

.menuItemHeader {
	display: flex;
	justify-content: space-between;
	align-items: center;
	margin-bottom: var(--OC-margin-5);
}

.menuItemHeader strong {
	color: var(--color-main-text);
	font-size: 1em;
}

.menuItemOrder {
	font-size: 0.85em;
	color: var(--color-text-lighter);
	background-color: var(--color-background-dark);
	padding: var(--OC-margin-5);
	border-radius: var(--border-radius);
}

.menuItemDescription {
	margin-bottom: var(--OC-margin-5);
	color: var(--color-text-lighter);
	font-size: 0.9em;
}

.menuItemLink,
.menuItemIcon,
.menuItemType {
	margin-bottom: var(--OC-margin-5);
	font-size: 0.85em;
}

.menuItemLink strong,
.menuItemIcon strong,
.menuItemType strong {
	color: var(--color-text-maxcontrast);
}

.menuItemLink a {
	color: var(--color-primary);
	text-decoration: none;
}

.menuItemLink a:hover {
	text-decoration: underline;
}

.emptyMenuItems {
	text-align: center;
	color: var(--color-text-lighter);
	font-style: italic;
	padding: var(--OC-margin-20);
}

.metadataContainer {
	background-color: var(--color-background-dark);
	border-radius: var(--border-radius);
	padding: var(--OC-margin-15);
	overflow-x: auto;
}

.metadataContainer pre {
	margin: 0;
	font-family: 'Courier New', monospace;
	font-size: 0.85em;
	color: var(--color-main-text);
	white-space: pre-wrap;
	word-wrap: break-word;
}

.emptyState {
	text-align: center;
	padding: var(--OC-margin-50);
	color: var(--color-text-lighter);
}

.modalActions {
	display: flex;
	justify-content: flex-end;
	gap: var(--OC-margin-10);
	margin-top: var(--OC-margin-20);
	padding-top: var(--OC-margin-20);
	border-top: 1px solid var(--color-border);
}

@media (min-width: 768px) {
	.detailGrid {
		grid-template-columns: repeat(2, 1fr);
	}
}
</style> 