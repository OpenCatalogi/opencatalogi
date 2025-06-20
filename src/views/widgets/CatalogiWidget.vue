/**
 * CatalogiWidget.vue
 * Widget component for displaying catalogs
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
			<NcEmptyContent :title="t('opencatalogi', 'Geen catalogi gevonden')">
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
	name: 'CatalogiWidget',
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
					text: 'Catalogus bekijken',
					icon: 'icon-open-in-app',
				},
			},
		}
	},
	computed: {
		items() {
			return objectStore.getCollection('catalog').results.map((catalog) => ({
				id: catalog.id,
				mainText: catalog.title,
				subText: catalog.summary,
				avatarUrl: getTheme() === 'light' ? '/apps-extra/opencatalogi/img/database-outline.svg' : '/apps-extra/opencatalogi/img/database-outline_light.svg',
			}))
		},
	},
	mounted() {
		this.fetchData()
	},
	methods: {
		/**
		 * Handle showing a catalog
		 * @param {object} item - The catalog item to show
		 * @return {void}
		 */
		onShow(item) {
			navigationStore.setSelected('publication')
			navigationStore.setSelectedCatalogus(item.id)
			window.open('/index.php/apps/opencatalogi', '_self')
		},
		/**
		 * Fetch the catalog data
		 * @param {string|null} search - Optional search term
		 * @return {Promise<void>}
		 */
		async fetchData(search = null) {
			this.loading = true
			await objectStore.fetchCollection('catalog', search)
			this.loading = false
		},
	},
}
</script>
