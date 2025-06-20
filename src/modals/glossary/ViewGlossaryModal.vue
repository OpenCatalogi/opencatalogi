/**
 * ViewGlossaryModal.vue
 * Modal component for viewing glossary term details
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
	<NcModal v-if="navigationStore.modal === 'viewGlossary'"
		ref="modalRef"
		label-id="viewGlossaryModal"
		@close="closeModal">
		<div class="modal__content">
			<h2>{{ term?.title || 'Glossary Term' }}</h2>
			
			<div v-if="term" class="glossaryDetails">
				<div class="detailSection">
					<h3>{{ t('opencatalogi', 'Basic Information') }}</h3>
					<div class="detailGrid">
						<div class="detailItem">
							<strong>{{ t('opencatalogi', 'Title') }}:</strong>
							<span>{{ term.title || '-' }}</span>
						</div>
						<div class="detailItem">
							<strong>{{ t('opencatalogi', 'Status') }}:</strong>
							<span>{{ term.published ? 'Published' : 'Draft' }}</span>
						</div>
						<div v-if="term.summary" class="detailItem">
							<strong>{{ t('opencatalogi', 'Summary') }}:</strong>
							<span>{{ term.summary }}</span>
						</div>
						<div v-if="term.description" class="detailItem">
							<strong>{{ t('opencatalogi', 'Description') }}:</strong>
							<span>{{ term.description }}</span>
						</div>
						<div v-if="term.externalLink" class="detailItem">
							<strong>{{ t('opencatalogi', 'External Link') }}:</strong>
							<span>
								<a :href="term.externalLink" target="_blank" rel="noopener noreferrer">
									{{ term.externalLink }}
								</a>
							</span>
						</div>
					</div>
				</div>

				<div v-if="term.keywords?.length" class="detailSection">
					<h3>{{ t('opencatalogi', 'Keywords') }}</h3>
					<div class="keywordsList">
						<span v-for="keyword in term.keywords" 
							:key="keyword" 
							class="keywordTag">
							{{ keyword }}
						</span>
					</div>
				</div>

				<div v-if="term.relatedTerms?.length" class="detailSection">
					<h3>{{ t('opencatalogi', 'Related Terms') }}</h3>
					<div class="relatedTermsList">
						<NcButton v-for="relatedTerm in term.relatedTerms"
							:key="relatedTerm.id"
							type="secondary"
							@click="selectTerm(relatedTerm)">
							{{ relatedTerm.title }}
						</NcButton>
					</div>
				</div>

				<div v-if="term.metadata" class="detailSection">
					<h3>{{ t('opencatalogi', 'Metadata') }}</h3>
					<div class="metadataContainer">
						<pre>{{ JSON.stringify(term.metadata, null, 2) }}</pre>
					</div>
				</div>
			</div>

			<div v-else class="emptyState">
				<p>{{ t('opencatalogi', 'No glossary term selected') }}</p>
			</div>

			<div class="modalActions">
				<NcButton type="secondary" @click="openEditModal">
					<template #icon>
						<Pencil :size="20" />
					</template>
					{{ t('opencatalogi', 'Edit') }}
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
	name: 'ViewGlossaryModal',
	components: {
		NcModal,
		NcButton,
		Pencil,
	},
	computed: {
		/**
		 * Get the currently active glossary term from the store
		 * @return {object|null} The active glossary term object
		 */
		term() {
			return objectStore.getActiveObject('glossary')
		},
	},
	methods: {
		/**
		 * Close the modal and clear the active object
		 * @return {void}
		 */
		closeModal() {
			navigationStore.setModal(false)
			objectStore.clearActiveObject('glossary')
		},
		/**
		 * Open the edit modal for the current term
		 * @return {void}
		 */
		openEditModal() {
			navigationStore.setModal('glossary')
		},
		/**
		 * Select a related term and view its details
		 * @param {object} term - The term to select
		 * @return {void}
		 */
		selectTerm(term) {
			objectStore.setActiveObject('glossary', term)
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

.glossaryDetails {
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

.keywordsList {
	display: flex;
	flex-wrap: wrap;
	gap: var(--OC-margin-10);
}

.keywordTag {
	background-color: var(--color-primary-light);
	color: var(--color-primary-text);
	padding: var(--OC-margin-5) var(--OC-margin-10);
	border-radius: var(--border-radius-pill);
	font-size: 0.85em;
	font-weight: 500;
}

.relatedTermsList {
	display: flex;
	flex-wrap: wrap;
	gap: var(--OC-margin-10);
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