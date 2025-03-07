<script setup>
import { pageStore, navigationStore } from '../../store/store.js'
</script>

<template>
	<NcDialog name="Delete Page"
		size="normal"
		:can-close="false">
		<p v-if="success === null">
			Do you want to permanently delete <b>{{ pageStore.pageItem?.name }}</b>? This action cannot be undone.
		</p>

		<NcNoteCard v-if="success" type="success">
			<p>Page successfully deleted</p>
		</NcNoteCard>
		<NcNoteCard v-if="error" type="error">
			<p>{{ error }}</p>
		</NcNoteCard>

		<template #actions>
			<NcButton @click="closeDialog">
				<template #icon>
					<Cancel :size="20" />
				</template>
				{{ success === null ? 'Cancel' : 'Close' }}
			</NcButton>
			<NcButton
				v-if="success === null"
				:disabled="loading"
				type="error"
				@click="deletePage()">
				<template #icon>
					<NcLoadingIcon v-if="loading" :size="20" />
					<TrashCanOutline v-if="!loading" :size="20" />
				</template>
				Delete
			</NcButton>
		</template>
	</NcDialog>
</template>

<script>
import {
	NcButton,
	NcDialog,
	NcLoadingIcon,
	NcNoteCard,
} from '@nextcloud/vue'

import Cancel from 'vue-material-design-icons/Cancel.vue'
import TrashCanOutline from 'vue-material-design-icons/TrashCanOutline.vue'

/**
 * Component for deleting pages
 */
export default {
	name: 'DeletePage',
	components: {
		NcDialog,
		NcButton,
		NcLoadingIcon,
		NcNoteCard,
		// Icons
		TrashCanOutline,
		Cancel,
	},
	data() {
		return {
			success: null,
			loading: false,
			error: false,
			closeModalTimeout: null,
		}
	},
	methods: {
		closeDialog() {
			navigationStore.setModal(null)
			clearTimeout(this.closeModalTimeout)
			this.success = null
			this.loading = false
			this.error = false
		},
		async deletePage() {
			this.loading = true

			pageStore.deletePage(pageStore.pageItem.id)
				.then(({ response }) => {
					this.success = response.ok
					this.error = false
					response.ok && (this.closeModalTimeout = setTimeout(this.closeDialog, 2000))
				}).catch((error) => {
					this.success = false
					this.error = error.message || 'An error occurred while deleting the page'
				}).finally(() => {
					this.loading = false
				})
		},
	},
}
</script>
