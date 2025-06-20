/**
 * DeleteListingDialog.vue
 * Dialog for deleting listings
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
</script>

<template>
	<NcDialog v-if="navigationStore.dialog === 'deleteListing'"
		ref="dialogRef"
		class="deleteListingDialog"
		label-id="deleteListingDialog"
		@close="closeDialog">
		<div class="dialog__content">
			<h2>Listing verwijderen</h2>
			<div v-if="success !== null || error">
				<NcNoteCard v-if="success" type="success">
					<p>Listing succesvol verwijderd</p>
				</NcNoteCard>
				<NcNoteCard v-if="!success" type="error">
					<p>Er is iets fout gegaan bij het verwijderen van listing</p>
				</NcNoteCard>
				<NcNoteCard v-if="error" type="error">
					<p>{{ error }}</p>
				</NcNoteCard>
			</div>
			<div v-if="success === null" class="form-group">
				<p>Weet je zeker dat je de listing '{{ listing.title }}' wilt verwijderen?</p>
			</div>

			<span class="buttonContainer">
				<NcButton
					@click="navigationStore.setDialog(false)">
					{{ success ? 'Sluiten' : 'Annuleer' }}
				</NcButton>
				<NcButton v-if="success === null"
					:disabled="loading"
					type="error"
					@click="handleDelete">
					<template #icon>
						<span>
							<NcLoadingIcon v-if="loading" :size="20" />
							<Delete v-if="!loading" :size="20" />
						</span>
					</template>
					Verwijderen
				</NcButton>
			</span>
		</div>
	</NcDialog>
</template>

<script>
import {
	NcButton,
	NcDialog,
	NcNoteCard,
	NcLoadingIcon,
} from '@nextcloud/vue'

// icons
import Delete from 'vue-material-design-icons/Delete.vue'

/**
 * Loading state for the component
 * @type {import('vue').Ref<boolean>}
 */
const loading = ref(false)

/**
 * Success state for the component
 * @type {import('vue').Ref<boolean|null>}
 */
const success = ref(null)

/**
 * Error state for the component
 * @type {import('vue').Ref<string|null>}
 */
const error = ref(null)

/**
 * Get the active listing from the store
 * @return {object | null}
 */
const listing = computed(() => objectStore.getActiveObject('listing'))

/**
 * Handle delete action
 * @return {Promise<void>}
 */
const handleDelete = async () => {
	loading.value = true
	try {
		await objectStore.deleteObject('listing', listing.value.id)
		success.value = true
	} catch (error) {
		console.error('Error deleting listing:', error)
		success.value = false
		error.value = error.message
	} finally {
		loading.value = false
	}
}

export default {
	name: 'DeleteListingDialog',
	components: {
		NcDialog,
		NcButton,
		NcNoteCard,
		NcLoadingIcon,
	},
	data() {
		return {
			loading: false,
			success: null,
			error: null,
		}
	},
	methods: {
		closeDialog() {
			this.navigationStore.setDialog(false)
		},
	},
}
</script>

<style scoped>
.dialog__content {
	padding: 20px;
}

.buttonContainer {
	display: flex;
	justify-content: flex-end;
	gap: 10px;
	margin-top: 20px;
}

.form-group {
	display: flex;
	flex-direction: column;
	gap: 10px;
	margin-top: 20px;
}
</style>
