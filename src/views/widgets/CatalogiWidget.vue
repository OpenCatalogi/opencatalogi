<script setup>
import { catalogiStore, navigationStore } from '../../store/store.js'
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
			return catalogiStore.catalogiList.map((catalogi) => ({
				id: catalogi.id,
				mainText: catalogi.title,
				subText: catalogi.summary,
				avatarUrl: getTheme() === 'light' ? '/apps-extra/opencatalogi/img/database-outline.svg' : '/apps-extra/opencatalogi/img/database-outline_light.svg',
			}))
		},
	},
	mounted() {
		this.fetchData()
	},

	methods: {
		onShow(item) {
			navigationStore.setSelected('publication'); navigationStore.setSelectedCatalogus(item.id)
			window.open('/index.php/apps/opencatalogi', '_self')
		},
		fetchData(search = null) {
			this.loading = true
			catalogiStore.refreshCatalogiList(search)
				.then(() => {
					this.loading = false
				})
		},
	},
}
</script>
