/**
 * ViewThemeModal.vue
 * Modal component for viewing theme details and configuration
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
	<NcModal v-if="navigationStore.modal === 'viewTheme'"
		ref="modalRef"
		label-id="viewThemeModal"
		@close="closeModal">
		<div class="modal__content">
			<h2>{{ theme?.title || 'Theme' }}</h2>
			
			<div v-if="theme" class="themeDetails">
				<div class="detailSection">
					<h3>{{ t('opencatalogi', 'Basic Information') }}</h3>
					<div class="detailGrid">
						<div class="detailItem">
							<strong>{{ t('opencatalogi', 'Title') }}:</strong>
							<span>{{ theme.title || '-' }}</span>
						</div>
						<div v-if="theme.name" class="detailItem">
							<strong>{{ t('opencatalogi', 'Name') }}:</strong>
							<span>{{ theme.name }}</span>
						</div>
						<div v-if="theme.status" class="detailItem">
							<strong>{{ t('opencatalogi', 'Status') }}:</strong>
							<span>{{ theme.status }}</span>
						</div>
						<div v-if="theme.version" class="detailItem">
							<strong>{{ t('opencatalogi', 'Version') }}:</strong>
							<span>{{ theme.version }}</span>
						</div>
						<div v-if="theme.summary" class="detailItem">
							<strong>{{ t('opencatalogi', 'Summary') }}:</strong>
							<span>{{ theme.summary }}</span>
						</div>
						<div v-if="theme.description" class="detailItem">
							<strong>{{ t('opencatalogi', 'Description') }}:</strong>
							<span>{{ theme.description }}</span>
						</div>
					</div>
				</div>

				<div v-if="theme.image || theme.screenshot" class="detailSection">
					<h3>{{ t('opencatalogi', 'Visual Preview') }}</h3>
					<div class="imageContainer">
						<img v-if="theme.image" 
							:src="theme.image" 
							:alt="theme.title || 'Theme preview'"
							class="themeImage" />
						<img v-else-if="theme.screenshot" 
							:src="theme.screenshot" 
							:alt="theme.title || 'Theme screenshot'"
							class="themeImage" />
					</div>
				</div>

				<div v-if="theme.config || theme.settings" class="detailSection">
					<h3>{{ t('opencatalogi', 'Configuration') }}</h3>
					<div class="configContainer">
						<div v-if="theme.config">
							<h4>{{ t('opencatalogi', 'Config') }}</h4>
							<pre>{{ JSON.stringify(theme.config, null, 2) }}</pre>
						</div>
						<div v-if="theme.settings">
							<h4>{{ t('opencatalogi', 'Settings') }}</h4>
							<pre>{{ JSON.stringify(theme.settings, null, 2) }}</pre>
						</div>
					</div>
				</div>

				<div v-if="theme.author || theme.license || theme.repository" class="detailSection">
					<h3>{{ t('opencatalogi', 'Theme Information') }}</h3>
					<div class="detailGrid">
						<div v-if="theme.author" class="detailItem">
							<strong>{{ t('opencatalogi', 'Author') }}:</strong>
							<span>{{ theme.author }}</span>
						</div>
						<div v-if="theme.license" class="detailItem">
							<strong>{{ t('opencatalogi', 'License') }}:</strong>
							<span>{{ theme.license }}</span>
						</div>
						<div v-if="theme.repository" class="detailItem">
							<strong>{{ t('opencatalogi', 'Repository') }}:</strong>
							<span>
								<a :href="theme.repository" target="_blank" rel="noopener noreferrer">
									{{ theme.repository }}
								</a>
							</span>
						</div>
						<div v-if="theme.homepage" class="detailItem">
							<strong>{{ t('opencatalogi', 'Homepage') }}:</strong>
							<span>
								<a :href="theme.homepage" target="_blank" rel="noopener noreferrer">
									{{ theme.homepage }}
								</a>
							</span>
						</div>
						<div v-if="theme.updatedAt" class="detailItem">
							<strong>{{ t('opencatalogi', 'Last Updated') }}:</strong>
							<span>{{ new Date(theme.updatedAt).toLocaleDateString() }}</span>
						</div>
						<div v-if="theme.createdAt" class="detailItem">
							<strong>{{ t('opencatalogi', 'Created') }}:</strong>
							<span>{{ new Date(theme.createdAt).toLocaleDateString() }}</span>
						</div>
					</div>
				</div>

				<div v-if="theme.metadata" class="detailSection">
					<h3>{{ t('opencatalogi', 'Metadata') }}</h3>
					<div class="metadataContainer">
						<pre>{{ JSON.stringify(theme.metadata, null, 2) }}</pre>
					</div>
				</div>
			</div>

			<div v-else class="emptyState">
				<p>{{ t('opencatalogi', 'No theme selected') }}</p>
			</div>

			<div class="modalActions">
				<NcButton type="secondary" @click="openEditModal">
					<template #icon>
						<Pencil :size="20" />
					</template>
					{{ t('opencatalogi', 'Edit Theme') }}
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

export default {
	name: 'ViewThemeModal',
	components: {
		NcModal,
		NcButton,
		Pencil,
	},
	computed: {
		/**
		 * Get the currently active theme from the store
		 * @return {object|null} The active theme object
		 */
		theme() {
			return objectStore.getActiveObject('theme')
		},
	},
	methods: {
		/**
		 * Close the modal and clear the active object
		 * @return {void}
		 */
		closeModal() {
			navigationStore.setModal(false)
			objectStore.clearActiveObject('theme')
		},
		/**
		 * Open the edit modal for the current theme
		 * @return {void}
		 */
		openEditModal() {
			navigationStore.setModal('theme')
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

.themeDetails {
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

.detailSection h4 {
	margin: var(--OC-margin-15) 0 var(--OC-margin-10) 0;
	color: var(--color-text-maxcontrast);
	font-weight: 600;
	font-size: 0.95em;
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

.imageContainer {
	display: flex;
	justify-content: center;
	align-items: center;
	padding: var(--OC-margin-20);
	background-color: var(--color-background-hover);
	border-radius: var(--border-radius);
}

.themeImage {
	max-width: 100%;
	max-height: 300px;
	border-radius: var(--border-radius);
	box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
}

.configContainer {
	display: flex;
	flex-direction: column;
	gap: var(--OC-margin-15);
}

.configContainer pre {
	background-color: var(--color-background-dark);
	border-radius: var(--border-radius);
	padding: var(--OC-margin-15);
	overflow-x: auto;
	margin: 0;
	font-family: 'Courier New', monospace;
	font-size: 0.85em;
	color: var(--color-main-text);
	white-space: pre-wrap;
	word-wrap: break-word;
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