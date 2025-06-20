/**
 * EditListingModal.vue
 * Modal for editing a listing
 * @category Components
 * @package opencatalogi
 * @author Ruben Linde
 * @copyright 2024
 * @license AGPL-3.0-or-later
 * @version 1.0.0
 * @link https://github.com/opencatalogi/opencatalogi
 */

<script setup>
import { ref, computed } from 'vue'
import { objectStore, navigationStore } from '../../store/store.js'
import { NcButton, NcInputField, NcTags } from '@nextcloud/vue'

/**
 * Loading state for the component
 * @type {import('vue').Ref<boolean>}
 */
const loading = ref(false)

/**
 * Get the active directory from the store
 * @return {object | null}
 */
const directory = computed(() => objectStore.getActiveObject('directory'))

/**
 * Handle save action
 * @return {Promise<void>}
 */
const handleSave = async () => {
	loading.value = true
	try {
		await objectStore.updateObject('directory', directory.value)
		await objectStore.fetchCollection('directory')
		navigationStore.setModal(false)
	} catch (error) {
		console.error('Error saving directory:', error)
	} finally {
		loading.value = false
	}
}

/**
 * Handle cancel action
 * @return {void}
 */
const handleCancel = () => {
	navigationStore.setModal(false)
}
</script>

<template>
	<div class="edit-listing-modal">
		<NcInputField
			:value.sync="directory.title"
			:label="t('opencatalogi', 'Titel')"
			:disabled="loading" />
		<NcInputField
			:value.sync="directory.summary"
			:label="t('opencatalogi', 'Samenvatting')"
			:disabled="loading" />
		<NcInputField
			:value.sync="directory.description"
			:label="t('opencatalogi', 'Beschrijving')"
			:disabled="loading" />
		<NcTags
			v-model="directory.labels"
			:label="t('opencatalogi', 'Labels')"
			:disabled="loading" />
		<div class="edit-listing-modal__actions">
			<NcButton :disabled="loading" @click="handleCancel">
				{{ t('opencatalogi', 'Annuleren') }}
			</NcButton>
			<NcButton type="primary" :disabled="loading" @click="handleSave">
				{{ t('opencatalogi', 'Opslaan') }}
			</NcButton>
		</div>
	</div>
</template>

<style scoped>
.edit-listing-modal {
	padding: 20px;
}

.edit-listing-modal__actions {
	display: flex;
	justify-content: flex-end;
	gap: 10px;
	margin-top: 20px;
}
</style>
