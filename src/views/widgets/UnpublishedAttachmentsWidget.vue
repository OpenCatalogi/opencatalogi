<script setup>
import { navigationStore, publicationStore } from '../../store/store.js'
</script>

<template>
	<NcDashboardWidget :items="items"
		:loading="loading"
		:item-menu="itemMenu"
		@show="onShow">
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
			return publicationStore.conceptAttachments.map((attachment) => ({
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
		onShow(item) {
			navigationStore.setSelected('publication'); navigationStore.setSelectedCatalogus(item.id)
			window.open('/index.php/apps/opencatalogi', '_self')
		},
		fetchData() {
			this.loading = true
			publicationStore.getConceptAttachments()
				.then(({ response, data }) => {
					this.loading = false
				})
		},
	},
}
</script>
