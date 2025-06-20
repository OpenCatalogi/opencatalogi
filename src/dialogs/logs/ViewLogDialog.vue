<script setup>
import { navigationStore, objectStore } from '../../store/store.js'
</script>

<template>
	<NcDialog
		v-if="navigationStore.dialog === 'viewLog'"
		name="Log bekijken"
		:can-close="false">
		<div v-if="objectStore.getState('log').success !== null || objectStore.getState('log').error">
			<NcNoteCard v-if="objectStore.getState('log').success" type="success">
				<p>Log succesvol bekeken</p>
			</NcNoteCard>
			<NcNoteCard v-if="!objectStore.getState('log').success" type="error">
				<p>Er is iets fout gegaan bij het bekijken van log</p>
			</NcNoteCard>
			<NcNoteCard v-if="objectStore.getState('log').error" type="error">
				<p>{{ objectStore.getState('log').error }}</p>
			</NcNoteCard>
		</div>
		<div v-if="objectStore.isLoading('log')" class="loading-status">
			<NcLoadingIcon :size="20" />
			<span>Log wordt geladen...</span>
		</div>
		<div v-if="objectStore.getState('log').success === null && !objectStore.isLoading('log')" class="log-content">
			<pre>{{ objectStore.getActiveObject('log')?.content }}</pre>
		</div>
		<template #actions>
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

/**
 * View Log Dialog Component
 * @module Dialogs
 * @package
 * @author Ruben Linde
 * @copyright 2024
 * @license AGPL-3.0-or-later
 * @version 1.0.0
 * @see {@link https://github.com/opencatalogi/opencatalogi}
 */
export default {
	name: 'ViewLogDialog',
	components: {
		NcDialog,
		NcButton,
		NcNoteCard,
		NcLoadingIcon,
		// Icons
		Cancel,
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

.log-content {
    text-align: left;
    margin: 1rem 0;
    padding: 1rem;
    background-color: var(--color-background-dark);
    border-radius: var(--border-radius);
    max-height: 60vh;
    overflow: auto;
}

.log-content pre {
    margin: 0;
    white-space: pre-wrap;
    word-wrap: break-word;
}
</style>
