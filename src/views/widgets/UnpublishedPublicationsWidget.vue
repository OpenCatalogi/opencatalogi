/**
 * UnpublishedPublicationsWidget.vue
 * Widget component for displaying unpublished publications
 * @category Components
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
	<NcDashboardWidget :items="items"
		:loading="loading"
		:item-menu="itemMenu"
		@show="onShow">
		<template #empty-content>
			<NcEmptyContent :title="t('opencatalogi', 'Geen concept publicaties gevonden')">
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
	name: 'UnpublishedPublicationsWidget',
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
			itemMenu: {
				show: {
					text: 'Publicatie bekijken',
					icon: 'icon-open-in-app',
				},
			},
		}
	},
	computed: {
		items() {
			return objectStore.getCollection('publication').results
				.filter((publication) => publication.status === 'Concept')
				.map((publication) => ({
					id: publication.id,
					mainText: publication.title,
					subText: publication.summary,
					avatarUrl: getTheme() === 'light' ? '/apps-extra/opencatalogi/img/database-eye-outline.svg' : '/apps-extra/opencatalogi/img/database-eye-outline_light.svg',
				}))
		},
	},
	mounted() {
		this.fetchData()
	},
	methods: {
		/**
		 * Handle showing a publication
		 * @param {object} item - The publication item to show
		 * @return {void}
		 */
		onShow(item) {
			navigationStore.setSelected('publication')
			navigationStore.setSelectedCatalogus(item.id)
			window.open('/index.php/apps/opencatalogi', '_self')
		},
		/**
		 * Fetch the publication data
		 * @return {Promise<void>}
		 */
		async fetchData() {
			this.loading = true
			await objectStore.fetchCollection('publication')
			this.loading = false
		},
	},
}
</script>
