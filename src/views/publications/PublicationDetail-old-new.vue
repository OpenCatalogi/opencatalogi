<!-- eslint-disable -->

/**
 * PublicationDetail.vue
 * Component for displaying publication details
 * @category Components
 * @package opencatalogi
 * @author Ruben Linde
 * @copyright 2024
 * @license AGPL-3.0-or-later
 * @version 1.0.0
 * @link https://github.com/opencatalogi/opencatalogi
 */

<script setup>
import { ref, computed, onMounted } from 'vue'
import { objectStore, navigationStore, catalogiStore } from '../../store/store.js'
import { NcEmptyContent, NcLoadingIcon, NcButton, NcActionButton } from '@nextcloud/vue'
import FileIcon from 'vue-material-design-icons/File.vue'
import PlusIcon from 'vue-material-design-icons/Plus.vue'
import PencilIcon from 'vue-material-design-icons/Pencil.vue'
import ContentCopyIcon from 'vue-material-design-icons/ContentCopy.vue'
import DeleteIcon from 'vue-material-design-icons/Delete.vue'

/**
 * Loading state for the component
 * @type {import('vue').Ref<boolean>}
 */
const loading = ref(false)

/**
 * Get the active publication from the store
 * @return {object | null}
 */
const publication = computed(() => objectStore.getActiveObject('publication'))

/**
 * Get all attachments for the active publication
 * @return {Array<object>}
 */
const attachments = computed(() => {
	const activePublication = publication.value
	if (!activePublication) return []
	return objectStore.getCollection('attachment').results.filter(attachment =>
		attachment.publicationId === activePublication.id,
	)
})

/**
 * Fetch the publication and its attachments
 * @return {Promise<void>}
 */
const fetchData = async () => {
	loading.value = true
	try {
		await Promise.all([
			objectStore.fetchCollection('publication'),
			objectStore.fetchCollection('attachment'),
		])
	} finally {
		loading.value = false
	}
}

/**
 * Handle edit action
 * @return {void}
 */
const handleEdit = () => {
	navigationStore.setDialog('editPublication')
}

/**
 * Handle copy action
 * @return {void}
 */
const handleCopy = () => {
	navigationStore.setDialog('copyObject', {
		objectType: 'publication',
		dialogName: 'copyObject',
		displayName: 'Publicatie',
	})
}

/**
 * Handle delete action
 * @return {void}
 */
const handleDelete = () => {
	navigationStore.setDialog('deleteObject', {
		objectType: 'publication',
		dialogName: 'deletePublication',
		displayName: 'Publicatie',
	})
}

/**
 * Handle add attachment action
 * @return {void}
 */
const handleAddAttachment = () => {
	navigationStore.setDialog('addAttachment')
}

// Fetch data when component is mounted
onMounted(() => {
	fetchData()
})
</script>

<template>
	<div class="publication-detail">
		<div v-if="loading" class="publication-detail__loading">
			<NcLoadingIcon :size="20" />
		</div>
		<NcEmptyContent v-else-if="!publication" :title="t('opencatalogi', 'Geen publicatie geselecteerd')">
			<template #icon>
				<FileIcon />
			</template>
		</NcEmptyContent>
		<div v-else class="publication-detail__content">
			<div class="publication-detail__header">
				<h2 class="publication-detail__title">
					{{ publication.title }}
				</h2>
				<div class="publication-detail__actions">
					<NcActionButton close-after-click @click="handleEdit">
						<template #icon>
							<PencilIcon />
						</template>
						{{ t('opencatalogi', 'Bewerken') }}
					</NcActionButton>
					<NcActionButton close-after-click @click="handleCopy">
						<template #icon>
							<ContentCopyIcon />
						</template>
						{{ t('opencatalogi', 'Kopiëren') }}
					</NcActionButton>
					<NcActionButton close-after-click @click="handleDelete">
						<template #icon>
							<DeleteIcon />
						</template>
						{{ t('opencatalogi', 'Verwijderen') }}
					</NcActionButton>
				</div>
			</div>
			<div class="publication-detail__attachments">
				<div class="publication-detail__attachments-header">
					<h3 class="publication-detail__attachments-title">
						{{ t('opencatalogi', 'Bijlagen') }}
					</h3>
					<NcButton @click="handleAddAttachment">
						<template #icon>
							<PlusIcon />
						</template>
						{{ t('opencatalogi', 'Bijlage toevoegen') }}
					</NcButton>
				</div>
				<div v-if="attachments.length === 0" class="publication-detail__attachments-empty">
					{{ t('opencatalogi', 'Geen bijlagen gevonden') }}
				</div>
				<div v-else class="publication-detail__attachments-list">
					<NcListItem v-for="attachment in attachments"
						:key="attachment.id"
						:title="attachment.title"
						:subtitle="attachment.description"
						:to="'/attachments/' + attachment.id">
						<template #icon>
							<FileIcon />
						</template>
					</NcListItem>
				</div>
			</div>
		</div>
	</div>
</template>

<style scoped>
.publication-detail {
	padding: 20px;
}

.publication-detail__loading {
	display: flex;
	justify-content: center;
	align-items: center;
	height: 100%;
}

.publication-detail__content {
	display: flex;
	flex-direction: column;
	gap: 20px;
}

.publication-detail__header {
	display: flex;
	justify-content: space-between;
	align-items: center;
}

.publication-detail__title {
	margin: 0;
}

.publication-detail__actions {
	display: flex;
	gap: 10px;
}

.publication-detail__attachments {
	display: flex;
	flex-direction: column;
	gap: 10px;
}

.publication-detail__attachments-header {
	display: flex;
	justify-content: space-between;
	align-items: center;
}

.publication-detail__attachments-title {
	margin: 0;
}

.publication-detail__attachments-empty {
	text-align: center;
	color: var(--color-text-lighter);
}

.publication-detail__attachments-list {
	display: flex;
	flex-direction: column;
	gap: 10px;
}
</style>
