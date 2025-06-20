/**
 * CopyMenuDialog.vue
 * Dialog for copying menus
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
	<NcDialog v-if="navigationStore.dialog === 'copyMenu'"
		ref="dialogRef"
		class="copyMenuDialog"
		label-id="copyMenuDialog"
		@close="closeDialog">
		<div class="dialog__content">
			<h2>Menu kopiëren</h2>
			<div v-if="success !== null || error">
				<NcNoteCard v-if="success" type="success">
					<p>Menu succesvol gekopieerd</p>
				</NcNoteCard>
				<NcNoteCard v-if="!success" type="error">
					<p>Er is iets fout gegaan bij het kopiëren van menu</p>
				</NcNoteCard>
				<NcNoteCard v-if="error" type="error">
					<p>{{ error }}</p>
				</NcNoteCard>
			</div>
			<div v-if="success === null" class="form-group">
				<p>Weet je zeker dat je het menu '{{ menu.title }}' wilt kopiëren?</p>
			</div>

			<span class="buttonContainer">
				<NcButton
					@click="navigationStore.setDialog(false)">
					{{ success ? 'Sluiten' : 'Annuleer' }}
				</NcButton>
				<NcButton v-if="success === null"
					:disabled="loading"
					type="primary"
					@click="handleCopy">
					<template #icon>
						<span>
							<NcLoadingIcon v-if="loading" :size="20" />
							<ContentCopy v-if="!loading" :size="20" />
						</span>
					</template>
					Kopiëren
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
import ContentCopy from 'vue-material-design-icons/ContentCopy.vue'

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
 * Get the active menu from the store
 * @return {object | null}
 */
const menu = computed(() => objectStore.getActiveObject('menu'))

/**
 * Handle copy action
 * @return {Promise<void>}
 */
const handleCopy = async () => {
	loading.value = true
	try {
		const newMenu = {
			...menu.value,
			id: null,
			title: `${menu.value.title} (kopie)`,
		}
		await objectStore.createObject('menu', newMenu)
		success.value = true
	} catch (error) {
		console.error('Error copying menu:', error)
		success.value = false
		error.value = error.message
	} finally {
		loading.value = false
	}
}

export default {
	name: 'CopyMenuDialog',
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
