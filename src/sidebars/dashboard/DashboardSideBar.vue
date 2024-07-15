<script setup>
import { store } from '../../store.js'
</script>

<template>
	<NcAppSidebar
		name="Snelle start"
		subname="Schakel snel naar waar u nodig bent">
		<NcAppSidebarTab name="Zoeken" id="search-tab" :order="1">
			<template #icon>
				<Magnify :size="20" />
			</template>
			Zoek snel in het voor uw beschickbare federatieve netwerk
			<NcTextField class="searchField"
				:value.sync="store.search"
				label="Search" />
		</NcAppSidebarTab>
		<NcAppSidebarTab name="Publicaties" id="settings-tab" :order="2">
			<template #icon>
				<ListBoxOutline :size="20" />
			</template>
			Welke publicaties vereisen uw aandacht?
			<NcNoteCard type="success">
				<p>Er zijn op dit moment geen publicaties die uw aandacht vereisen</p>
			</NcNoteCard>
		</NcAppSidebarTab>
		<NcAppSidebarTab name="Bijlagen" id="share-tab" :order="3">
			<template #icon>
				<FileOutline :size="20" />
			</template>
			Welke bijlagen vereisen uw aandacht?
		</NcAppSidebarTab>
	</NcAppSidebar>
</template>
<script>

import { NcAppSidebar, NcAppSidebarTab, NcTextField, NcNoteCard } from '@nextcloud/vue'
import Magnify from 'vue-material-design-icons/Magnify.vue'
import ListBoxOutline from 'vue-material-design-icons/ListBoxOutline.vue'
import FileOutline from 'vue-material-design-icons/FileOutline.vue'

export default {
	name: 'DashboardSideBar',
	components: {
		NcAppSidebar,
		NcAppSidebarTab,
		NcTextField,
		NcNoteCard,
		// Icons
		Magnify,
		ListBoxOutline,
		FileOutline,
	},
	data() {
		return {
			starred: false,
		}
	},
}
</script>
