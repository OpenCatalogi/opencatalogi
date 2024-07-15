<template>
	<NcAppSidebar
		:starred="starred"
		name="Zoek opdracht"
		subname="Via deze pagina kunt u zoeken binnen het bij u bekende federatieve netwerk">
		<NcAppSidebarTab name="Zoeken" id="search-tab">
			<template #icon>
				<Magnify :size="20" />
			</template>
			Search tab content
		</NcAppSidebarTab>
		<NcAppSidebarTab name="Catalogi" id="settings-tab">
			<template #icon>
				<Cog :size="20" />
			</template>
			Settings tab content
		</NcAppSidebarTab>
		<NcAppSidebarTab name="Sharing" id="share-tab">
			<template #icon>
				<ShareVariant :size="20" />
			</template>
			Sharing tab content
		</NcAppSidebarTab>
	</NcAppSidebar>
</template>
<script>

import { NcAppSidebar, NcAppSidebarTab } from '@nextcloud/vue'
import Magnify from 'vue-material-design-icons/Magnify.vue'
import Cog from 'vue-material-design-icons/Cog.vue'
import ShareVariant from 'vue-material-design-icons/ShareVariant.vue'

export default {
	name: 'SearchSideBar',
	components: {
		NcAppSidebar,
		NcAppSidebarTab,
		// Icons
		Magnify,
		Cog,
		ShareVariant,
	},
	data() {
		return {
			starred: false,
		}
	},
}
</script>
