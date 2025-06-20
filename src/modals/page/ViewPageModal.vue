/**
 * ViewPageModal.vue
 * Modal component for viewing page details and content
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
	<NcModal v-if="navigationStore.modal === 'viewPage'"
		ref="modalRef"
		label-id="viewPageModal"
		@close="closeModal">
		<div class="modal__content">
			<h2>{{ page?.title || 'Page' }}</h2>
			
			<div v-if="page" class="pageDetails">
				<div class="detailSection">
					<h3>{{ t('opencatalogi', 'Basic Information') }}</h3>
					<div class="detailGrid">
						<div class="detailItem">
							<strong>{{ t('opencatalogi', 'Title') }}:</strong>
							<span>{{ page.title || '-' }}</span>
						</div>
						<div v-if="page.slug" class="detailItem">
							<strong>{{ t('opencatalogi', 'Slug') }}:</strong>
							<span>{{ page.slug }}</span>
						</div>
						<div v-if="page.description" class="detailItem">
							<strong>{{ t('opencatalogi', 'Description') }}:</strong>
							<span>{{ page.description }}</span>
						</div>
						<div v-if="page.summary" class="detailItem">
							<strong>{{ t('opencatalogi', 'Summary') }}:</strong>
							<span>{{ page.summary }}</span>
						</div>
						<div v-if="page.updatedAt" class="detailItem">
							<strong>{{ t('opencatalogi', 'Last Updated') }}:</strong>
							<span>{{ new Date(page.updatedAt).toLocaleDateString() }}</span>
						</div>
						<div v-if="page.createdAt" class="detailItem">
							<strong>{{ t('opencatalogi', 'Created') }}:</strong>
							<span>{{ new Date(page.createdAt).toLocaleDateString() }}</span>
						</div>
					</div>
				</div>

				<div v-if="page.contents?.length" class="detailSection">
					<h3>{{ t('opencatalogi', 'Content Items') }} ({{ page.contents.length }})</h3>
					<div class="contentItemsList">
						<div v-for="(content, index) in page.contents" 
							:key="content.id || index" 
							class="contentItem">
							<div class="contentItemHeader">
								<strong>{{ content.title || content.name || `Content ${index + 1}` }}</strong>
								<span v-if="content.type" class="contentItemType">
									{{ content.type }}
								</span>
							</div>
							<div v-if="content.description" class="contentItemDescription">
								{{ content.description }}
							</div>
							<div v-if="content.content" class="contentItemContent">
								<strong>Content:</strong>
								<div class="contentPreview">
									{{ content.content.substring(0, 200) }}{{ content.content.length > 200 ? '...' : '' }}
								</div>
							</div>
							<div v-if="content.order !== undefined" class="contentItemOrder">
								<strong>Order:</strong> {{ content.order }}
							</div>
						</div>
					</div>
				</div>

				<div v-else class="detailSection">
					<h3>{{ t('opencatalogi', 'Content Items') }}</h3>
					<p class="emptyContentItems">{{ t('opencatalogi', 'No content items configured') }}</p>
				</div>

				<div v-if="page.metadata" class="detailSection">
					<h3>{{ t('opencatalogi', 'Metadata') }}</h3>
					<div class="metadataContainer">
						<pre>{{ JSON.stringify(page.metadata, null, 2) }}</pre>
					</div>
				</div>
			</div>

			<div v-else class="emptyState">
				<p>{{ t('opencatalogi', 'No page selected') }}</p>
			</div>

			<div class="modalActions">
				<NcButton type="secondary" @click="openEditModal">
					<template #icon>
						<Pencil :size="20" />
					</template>
					{{ t('opencatalogi', 'Edit Page') }}
				</NcButton>
				<NcButton type="secondary" @click="openAddContentModal">
					<template #icon>
						<Plus :size="20" />
					</template>
					{{ t('opencatalogi', 'Add Content') }}
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
	name: 'ViewPageModal',
	components: {
		NcModal,
		NcButton,
		Pencil,
		Plus,
	},
	computed: {
		/**
		 * Get the currently active page from the store
		 * @return {object|null} The active page object
		 */
		page() {
			return objectStore.getActiveObject('page')
		},
	},
	methods: {
		/**
		 * Close the modal and clear the active object
		 * @return {void}
		 */
		closeModal() {
			navigationStore.setModal(false)
			objectStore.clearActiveObject('page')
		},
		/**
		 * Open the edit modal for the current page
		 * @return {void}
		 */
		openEditModal() {
			navigationStore.setModal('page')
		},
		/**
		 * Open the add content modal
		 * @return {void}
		 */
		openAddContentModal() {
			navigationStore.setModal('pageContentForm')
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

.pageDetails {
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

.contentItemsList {
	display: flex;
	flex-direction: column;
	gap: var(--OC-margin-15);
}

.contentItem {
	padding: var(--OC-margin-15);
	background-color: var(--color-background-hover);
	border-radius: var(--border-radius);
	border-left: 3px solid var(--color-primary);
}

.contentItemHeader {
	display: flex;
	justify-content: space-between;
	align-items: center;
	margin-bottom: var(--OC-margin-5);
}

.contentItemHeader strong {
	color: var(--color-main-text);
	font-size: 1em;
}

.contentItemType {
	font-size: 0.85em;
	color: var(--color-text-lighter);
	background-color: var(--color-background-dark);
	padding: var(--OC-margin-5);
	border-radius: var(--border-radius);
}

.contentItemDescription {
	margin-bottom: var(--OC-margin-5);
	color: var(--color-text-lighter);
	font-size: 0.9em;
}

.contentItemContent {
	margin-bottom: var(--OC-margin-5);
	font-size: 0.85em;
}

.contentItemContent strong {
	color: var(--color-text-maxcontrast);
}

.contentPreview {
	margin-top: var(--OC-margin-5);
	padding: var(--OC-margin-10);
	background-color: var(--color-background-dark);
	border-radius: var(--border-radius);
	font-family: monospace;
	white-space: pre-wrap;
	word-wrap: break-word;
}

.contentItemOrder {
	font-size: 0.85em;
}

.contentItemOrder strong {
	color: var(--color-text-maxcontrast);
}

.emptyContentItems {
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