/**
 * UnpublishedAttachmentsWidget.vue
 * Widget component for displaying unpublished attachments
 * @category Components
 * @package opencatalogi
 * @author Ruben Linde
 * @copyright 2024
 * @license AGPL-3.0-or-later
 * @version 1.0.0
 * @link https://github.com/opencatalogi/opencatalogi
 */

<script setup>
import { objectStore } from '../../store/store.js'
</script>

<template>
	<NcDashboardWidget :items="items"
		:loading="loading"
		:item-menu="itemMenu">
		<template #empty-content>
			<NcEmptyContent :title="t('opencatalogi', 'Geen concept bijlagen gevonden')">
				<template #icon>
					<FolderIcon />
				</template>
			</NcEmptyContent>
		</template>
	</NcDashboardWidget>
</template>

<script>
// Components
import { NcDashboardWidget, NcEmptyContent } from '@nextcloud/vue'

// Icons
import FolderIcon from 'vue-material-design-icons/Folder.vue'

import { getTheme } from '../../services/getTheme.js'

export default {
	name: 'UnpublishedAttachmentsWidget',

	components: {
		NcDashboardWidget,
		NcEmptyContent,
	},

	props: {
		title: {
			type: String,
			required: true,
		},
	},

	data() {
		return {
			loading: false,
			itemMenu: {},
		}
	},
	computed: {
		items() {
			return objectStore.getCollection('attachment').results
				.filter((attachment) => attachment.status === 'Concept')
				.map((attachment) => ({
					id: attachment.id,
					mainText: attachment.title,
					subText: attachment.summary,
					avatarUrl: getTheme() === 'light' ? '/apps-extra/opencatalogi/img/file-outline.svg' : '/apps-extra/opencatalogi/img/file-outline_light.svg',
				}))
		},
	},
	mounted() {
		this.fetchData()
	},
	methods: {
		/**
		 * Fetch the attachment data
		 * @return {Promise<void>}
		 */
		async fetchData() {
			this.loading = true
			await objectStore.fetchCollection('attachment')
			this.loading = false
		},
	},
}
</script>
