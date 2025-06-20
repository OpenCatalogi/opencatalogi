/**
 * DeleteCatalogiPublicationType.vue
 * Dialog for deleting a publication type from a catalog
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
import { NcButton } from '@nextcloud/vue'

/**
 * Loading state for the component
 * @type {import('vue').Ref<boolean>}
 */
const loading = ref(false)

/**
 * Get the active catalog from the store
 * @return {object | null}
 */
const catalog = computed(() => objectStore.getActiveObject('catalog'))

/**
 * Get the active publication type from the store
 * @return {object | null}
 */
const publicationType = computed(() => objectStore.getActiveObject('publicationType'))

/**
 * Handle delete action
 * @return {Promise<void>}
 */
const handleDelete = async () => {
	loading.value = true
	try {
		const catalogClone = { ...catalog.value }
		catalogClone.publicationTypes = catalogClone.publicationTypes
			.filter((id) => id !== publicationType.value.id)

		await objectStore.updateObject('catalog', catalogClone)
		navigationStore.setDialog(false)
	} catch (error) {
		console.error('Error deleting publication type from catalog:', error)
	} finally {
		loading.value = false
	}
}

/**
 * Handle cancel action
 * @return {void}
 */
const handleCancel = () => {
	navigationStore.setDialog(false)
}
</script>

<template>
	<div class="delete-catalogi-publication-type-dialog">
		<p>
			{{ t('opencatalogi', 'Wil je') }} <b>{{ publicationType?.title }}</b> {{ t('opencatalogi', 'verwijderen van') }} <b>{{ catalog?.title }}</b>?
		</p>
		<div class="delete-catalogi-publication-type-dialog__actions">
			<NcButton :disabled="loading" @click="handleCancel">
				{{ t('opencatalogi', 'Annuleren') }}
			</NcButton>
			<NcButton type="primary" :disabled="loading" @click="handleDelete">
				{{ t('opencatalogi', 'Verwijderen') }}
			</NcButton>
		</div>
	</div>
</template>

<style scoped>
.delete-catalogi-publication-type-dialog {
	padding: 20px;
}

.delete-catalogi-publication-type-dialog__actions {
	display: flex;
	justify-content: flex-end;
	gap: 10px;
	margin-top: 20px;
}
</style>
