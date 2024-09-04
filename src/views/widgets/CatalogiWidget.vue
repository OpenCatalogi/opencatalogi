<script setup>
import { catalogiStore, navigationStore } from '../../store/store.js'
</script>

<template>
	<NcDashboardWidget :items="items"
		:loading="loading"
		:item-menu="itemMenu"
		@show="onShow">
		<template #empty-content>
			<NcEmptyContent :title="t('opencatalogi', 'No gifs found')">
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
				avatarUrl: '/apps-extra/opencatalogi/img/database-outline.svg',
			}))
		},
	},
	mounted() {
		this.fetchData()
	},

	methods: {
		onShow(item) {
			navigationStore.setSelected('publication'); navigationStore.setSelectedCatalogus(item.id)
			window.open('/apps/opencatalogi/catalogi', '_self')
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
