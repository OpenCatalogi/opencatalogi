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
		<NcAppSidebarTab v-if="directoryStore.listingItem.id && navigationStore.selected === 'directory'"
			id="settings-tab"
			name="Configuratie"
			:order="1">
			<template #icon>
				<CogOutline :size="20" />
			</template>
			<NcCheckboxRadioSwitch type="switch">
				Beschickbaar maken voor mijn zoek opdrachten
			</NcCheckboxRadioSwitch>
			<NcCheckboxRadioSwitch type="switch">
				Standaard mee nemen in de beantwoording van mijn zoekopdrachten
			</NcCheckboxRadioSwitch>
		</NcAppSidebarTab>
		<NcAppSidebarTab v-if="directoryStore.listingItem.id && navigationStore.selected === 'directory'"
			id="metdata-tab"
			name="Metadata"
			:order="2">
			<template #icon>
				<FileTreeOutline :size="20" />
			</template>
			Welke meta data typen zou u uit deze catalogus willen overnemen?
			<NcCheckboxRadioSwitch type="switch">
				Metedata type 1
			</NcCheckboxRadioSwitch>
		</NcAppSidebarTab>
	</NcAppSidebar>
</template>
<script>

import { NcAppSidebar, NcEmptyContent, NcButton, NcAppSidebarTab, NcCheckboxRadioSwitch } from '@nextcloud/vue'
import LayersOutline from 'vue-material-design-icons/LayersOutline.vue'
import Plus from 'vue-material-design-icons/Plus.vue'
import HelpCircleOutline from 'vue-material-design-icons/HelpCircleOutline.vue'
import CogOutline from 'vue-material-design-icons/CogOutline.vue'
import FileTreeOutline from 'vue-material-design-icons/FileTreeOutline.vue'

export default {
	name: 'DirectorySideBar',
	components: {
		NcAppSidebar,
		NcAppSidebarTab,
		NcEmptyContent,
		NcButton,
		NcCheckboxRadioSwitch,
		// Icons
		LayersOutline,
		Plus,
		HelpCircleOutline,
		CogOutline,
		FileTreeOutline,
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
