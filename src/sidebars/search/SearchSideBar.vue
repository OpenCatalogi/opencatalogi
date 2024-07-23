<script setup>
import { useSearchStore } from '../../store/store.js'
</script>

<template>
	<NcAppSidebar
		name="Zoek opdracht"
		subtitle="baldie"
		subname="Via deze pagina kunt u zoeken binnen het bij u bekende federatieve netwerk">
		<NcAppSidebarTab id="search-tab" name="Zoeken" :order="1">
			<template #icon>
				<Magnify :size="20" />
			</template>
			<NcTextField class="searchField"
				:value.sync="searchStore.search"
				label="Search" />
		</NcAppSidebarTab>
		<NcAppSidebarTab id="settings-tab" name="Catalogi" :order="2">
			<template #icon>
				<DatabaseOutline :size="20" />
			</template>
			Catalogi tab content
		</NcAppSidebarTab>
		<NcAppSidebarTab id="share-tab" name="Metdata" :order="3">
			<template #icon>
				<FileTreeOutline :size="20" />
			</template>
			Metdata tab content
		</NcAppSidebarTab>
	</NcAppSidebar>
</template>
<script>

import { NcAppSidebar, NcAppSidebarTab, NcTextField } from '@nextcloud/vue'
import Magnify from 'vue-material-design-icons/Magnify.vue'
import DatabaseOutline from 'vue-material-design-icons/DatabaseOutline.vue'
import FileTreeOutline from 'vue-material-design-icons/FileTreeOutline.vue'

export default {
	name: 'SearchSideBar',
	components: {
		NcAppSidebar,
		NcAppSidebarTab,
		NcTextField,
		// Icons
		Magnify,
		DatabaseOutline,
		FileTreeOutline,
	},
	data() {
		return {
			searchStore: useSearchStore(),
			starred: false,
		}
	},
}
</script>
