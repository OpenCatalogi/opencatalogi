<script setup>
import { navigationStore, objectStore } from '../../store/store.js'
</script>

<template>
	<NcDialog
		v-if="navigationStore.dialog === 'deleteMultipleThemes'"
		name="Thema's verwijderen"
		:can-close="false">
		<div v-if="objectStore.getState('theme').success !== null || objectStore.getState('theme').error">
			<NcNoteCard v-if="objectStore.getState('theme').success" type="success">
				<p>Thema's succesvol verwijderd</p>
			</NcNoteCard>
			<NcNoteCard v-if="!objectStore.getState('theme').success" type="error">
				<p>Er is iets fout gegaan bij het verwijderen van thema's</p>
			</NcNoteCard>
			<NcNoteCard v-if="objectStore.getState('theme').error" type="error">
				<p>{{ objectStore.getState('theme').error }}</p>
			</NcNoteCard>
		</div>
		<div v-if="objectStore.isLoading('theme')" class="loading-status">
			<NcLoadingIcon :size="20" />
			<span>Thema's worden verwijderd...</span>
		</div>
		<p v-if="objectStore.getState('theme').success === null && !objectStore.isLoading('theme')">
			Wil je de geselecteerde thema's definitief verwijderen? Deze actie kan niet ongedaan worden gemaakt.
		</p>
		<template v-if="objectStore.getState('theme').success === null && !objectStore.isLoading('theme')" #actions>
			<NcButton
				:disabled="objectStore.isLoading('theme')"
				icon=""
				@click="navigationStore.setDialog(false)">
				<template #icon>
					<Cancel :size="20" />
				</template>
				Annuleer
			</NcButton>
			<NcButton
				:disabled="objectStore.isLoading('theme')"
				icon="Delete"
				type="error"
				@click="deleteThemes()">
				<template #icon>
					<Delete :size="20" />
				</template>
				Verwijderen
			</NcButton>
		</template>
		<template v-else #actions>
			<NcButton
				icon=""
				@click="navigationStore.setDialog(false)">
				<template #icon>
					<Cancel :size="20" />
				</template>
				Sluiten
			</NcButton>
		</template>
	</NcDialog>
</template>

<script>
import { NcButton, NcDialog, NcNoteCard, NcLoadingIcon } from '@nextcloud/vue'

import Cancel from 'vue-material-design-icons/Cancel.vue'
import Delete from 'vue-material-design-icons/Delete.vue'

/**
 * Delete Multiple Themes Dialog Component
 * @module Dialogs
 * @package
 * @author Ruben Linde
 * @copyright 2024
 * @license AGPL-3.0-or-later
 * @version 1.0.0
 * @see {@link https://github.com/opencatalogi/opencatalogi}
 */
export default {
	name: 'DeleteMultipleThemesDialog',
	components: {
		NcDialog,
		NcButton,
		NcNoteCard,
		NcLoadingIcon,
		// Icons
		Cancel,
		Delete,
	},
	methods: {
		/**
		 * Delete the selected themes
		 *
		 * @return {void}
		 */
		deleteThemes() {
			const selectedThemes = objectStore.getSelectedObjects('theme')
			if (!selectedThemes?.length) return

			Promise.all(selectedThemes.map(theme =>
				objectStore.deleteObject('theme', theme.id),
			))
				.then(() => {
					// Wait for the user to read the feedback then close the dialog
					setTimeout(() => {
						objectStore.setState('theme', { success: null, error: null })
						navigationStore.setDialog(false)
					}, 2000)
				})
		},
	},
}
</script>

<style>
.modal__content {
    margin: var(--OC-margin-50);
    text-align: center;
}

.zaakDetailsContainer {
    margin-block-start: var(--OC-margin-20);
    margin-inline-start: var(--OC-margin-20);
    margin-inline-end: var(--OC-margin-20);
}

.success {
    color: green;
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
