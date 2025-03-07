<script setup>
import { publicationStore } from '../../store/store.js'
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
		fetchData() {
			this.loading = true
			publicationStore.getConceptAttachments()
				.then(() => {
					this.loading = false
				})
		},
	},
}
</script>
