<script setup>
import { navigationStore, directoryStore } from '../../store/store.js'
</script>

<template>
	<NcAppSidebar
		:name="directoryStore.listingItem?.title || 'Geen listing' "
		:subname="directoryStore.listingItem?.organisation?.title">
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
			id="detail-tab"
			name="Details"
			:order="1">
			<template #icon>
				<InformationSlabSymbol :size="20" />
			</template>
			<div class="container">
				<div>
					<b>Samenvatting:</b>
					<span>{{ directoryStore.listingItem?.summery }}</span>
				</div>
				<div>
					<b>Status:</b>
					<span>{{ directoryStore.listingItem?.status }}</span>
				</div>
				<div>
					<b>Last synchronysation:</b>
					<span>{{ directoryStore.listingItem?.lastSync }}</span>
				</div>
				<div>
					<b>Directory:</b>
					<span>{{ directoryStore.listingItem?.directory }}</span>
				</div>
				<div>
					<b>Zoeken:</b>
					<span>{{ directoryStore.listingItem?.search }}</span>
				</div>
				<div>
					<b>Beschrijving:</b>
					<span>{{ directoryStore.listingItem?.description }}</span>
				</div>
			</div>
		</NcAppSidebarTab>
		<NcAppSidebarTab v-if="directoryStore.listingItem.id && navigationStore.selected === 'directory'"
			id="settings-tab"
			name="Configuratie"
			:order="2">
			<template #icon>
				<CogOutline :size="20" />
			</template>
			<NcCheckboxRadioSwitch :checked.sync="directoryStore.listingItem.available" type="switch">
				Beschickbaar maken voor mijn zoek opdrachten
			</NcCheckboxRadioSwitch>
			<NcCheckboxRadioSwitch :checked.sync="directoryStore.listingItem.default" type="switch">
				Standaard mee nemen in de beantwoording van mijn zoekopdrachten
			</NcCheckboxRadioSwitch>
		</NcAppSidebarTab>
		<NcAppSidebarTab v-if="directoryStore.listingItem.id && navigationStore.selected === 'directory'"
			id="metdata-tab"
			name="Metadata"
			:order="3">
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
import InformationSlabSymbol from 'vue-material-design-icons/InformationSlabSymbol.vue'

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
		InformationSlabSymbol,
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
