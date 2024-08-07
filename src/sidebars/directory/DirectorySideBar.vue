<script setup>
import { navigationStore, directoryStore } from '../../store/store.js'
</script>

<template>
	<NcAppSidebar
		name="Listing"
		subname="Listing Summery">
		<NcEmptyContent v-if="!directoryStore.listingItem.id || navigationStore.selected != 'directory'"
			class="detailContainer"
			name="Geen listing"
			description="Nog geen listing geselecteerd, listings kan je ontdekken via (externe) directories.">
			<template #icon>
				<LayersOutline />
			</template>
			<template #action>
				<NcButton type="primary" @click="navigationStore.setModal('addDirectory')">
					<template #icon>
						<Plus :size="20" />
					</template>
					Directory inlezen
				</NcButton>
				<NcButton @click="openLink('https://conduction.gitbook.io/opencatalogi-nextcloud/beheerders/directory', '_blank')">
					<template #icon>
						<HelpCircleOutline :size="20" />
					</template>
					Meer informatie over de directory
				</NcButton>
			</template>
		</NcEmptyContent>
	</NcAppSidebar>
</template>
<script>

import { NcAppSidebar, NcAppSidebarTab, NcTextField, NcNoteCard, NcListItem, NcActionButton, NcEmptyContent, NcButton } from '@nextcloud/vue'
import Magnify from 'vue-material-design-icons/Magnify.vue'
import LayersOutline from 'vue-material-design-icons/LayersOutline.vue'
import FileOutline from 'vue-material-design-icons/FileOutline.vue'
import Pencil from 'vue-material-design-icons/Pencil.vue'
import Publish from 'vue-material-design-icons/Publish.vue'
import Delete from 'vue-material-design-icons/Delete.vue'
import Plus from 'vue-material-design-icons/Plus.vue'
import HelpCircleOutline from 'vue-material-design-icons/HelpCircleOutline.vue'

export default {
	name: 'DirectorySideBar',
	components: {
		NcAppSidebar,
		NcAppSidebarTab,
		NcTextField,
		NcNoteCard,
		NcListItem,
		NcActionButton,
		NcEmptyContent,
		NcButton,
		// Icons
		Magnify,
		LayersOutline,
		FileOutline,
		Pencil,
		Publish,
		Delete,
		Plus,
		HelpCircleOutline,
	},
	data() {
		return {
		}
	},
	methods: {
		openLink(url, type = '') {
			window.open(url, type)
		},
	},
}
</script>
