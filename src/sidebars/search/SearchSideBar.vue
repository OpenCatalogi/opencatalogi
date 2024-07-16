<script setup>
import { store } from '../../store.js'
</script>

<template>
	<NcAppSidebar
		name="Zoek opdracht"
		subtitle="baldie"
		subname="Via deze pagina kunt u zoeken binnen het bij u bekende federatieve netwerk">
		<NcAppSidebarTab name="Zoeken" id="search-tab" :order="1">
			<template #icon>
				<Magnify :size="20" />
			</template>
			<NcTextField class="searchField"
				:value.sync="store.search"
				label="Search" />
		</NcAppSidebarTab>
		<NcAppSidebarTab name="Catalogi" id="settings-tab" :order="2">
			<template #icon>
				<DatabaseOutline :size="20" />
			</template>
			Catalogi tab content
		</NcAppSidebarTab>
		<NcAppSidebarTab name="Metdata" id="share-tab" :order="3">
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
			starred: false,
		}
	},
}
</script>
