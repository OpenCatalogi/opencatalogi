<script setup>
import { navigationStore, catalogiStore, publicationStore } from '../store/store.js'
</script>

<template>
	<NcAppNavigation>
		<NcAppNavigationList>
			<NcAppNavigationNew text="Publicatie Aanmaken" @click="navigationStore.setModal('publicationAdd'); navigationStore.setTransferData('ignore selectedCatalogus')">
				<template #icon>
					<Plus :size="20" />
				</template>
			</NcAppNavigationNew>
			<NcAppNavigationItem :active="navigationStore.selected === 'dashboard'" name="Dashboard" @click="navigationStore.setSelected('dashboard')">
				<template #icon>
					<Finance :size="20" />
				</template>
			</NcAppNavigationItem>
			<NcAppNavigationItem v-for="(catalogus, i) in catalogiStore.catalogiList"
				:key="`${catalogus}${i}`"
				:name="catalogus?.title"
				:active="catalogus?.id?.toString() === navigationStore.selectedCatalogus?.toString() && navigationStore.selected === 'publication'"
				@click="switchCatalogus(catalogus)">
				<template #icon>
					<DatabaseEyeOutline :size="20" />
				</template>
			</NcAppNavigationItem>
			<NcAppNavigationItem :active="navigationStore.selected === 'search'" name="Zoeken" @click="navigationStore.setSelected('search')">
				<template #icon>
					<LayersSearchOutline :size="20" />
				</template>
			</NcAppNavigationItem>
			<NcAppNavigationItem name="Documentatie" @click="openLink('https://conduction.gitbook.io/opencatalogi-nextcloud/gebruikers', '_blank')">
				<template #icon>
					<BookOpenVariantOutline :size="20" />
				</template>
			</NcAppNavigationItem>
		</NcAppNavigationList>

		<NcAppNavigationSettings>
			<NcAppNavigationItem :active="navigationStore.selected === 'organizations'" name="Organisaties" @click="navigationStore.setSelected('organizations')">
				<template #icon>
					<OfficeBuildingOutline :size="20" />
				</template>
			</NcAppNavigationItem>
			<NcAppNavigationItem :active="navigationStore.selected === 'catalogi'" name="Catalogi" @click="navigationStore.setSelected('catalogi')">
				<template #icon>
					<DatabaseCogOutline :size="20" />
				</template>
			</NcAppNavigationItem>
			<NcAppNavigationItem :active="navigationStore.selected === 'publicationType'" name="Publicatietypes" @click="navigationStore.setSelected('publicationType')">
				<template #icon>
					<FileTreeOutline :size="20" />
				</template>
			</NcAppNavigationItem>
			<NcAppNavigationItem :active="navigationStore.selected === 'themes'" name="Thema's" @click="navigationStore.setSelected('themes')">
				<template #icon>
					<ShapeOutline :size="20" />
				</template>
			</NcAppNavigationItem>
			<NcAppNavigationItem :active="navigationStore.selected === 'pages'" name="Page's" @click="navigationStore.setSelected('pages')">
				<template #icon>
					<Web :size="20" />
				</template>
			</NcAppNavigationItem>
			<NcAppNavigationItem :active="navigationStore.selected === 'menus'" name="Menu's" @click="navigationStore.setSelected('menus')">
				<template #icon>
					<MenuClose :size="20" />
				</template>
			</NcAppNavigationItem>
			<NcAppNavigationItem :active="navigationStore.selected === 'directory'" name="Directory" @click="navigationStore.setSelected('directory')">
				<template #icon>
					<LayersOutline :size="20" />
				</template>
			</NcAppNavigationItem>

			<Configuration />
		</NcAppNavigationSettings>
	</NcAppNavigation>
</template>
<script>

import {
	NcAppNavigation,
	NcAppNavigationList,
	NcAppNavigationItem,
	NcAppNavigationNew,
	NcAppNavigationSettings,
} from '@nextcloud/vue'

// Configuration
import Configuration from './Configuration.vue'

// Icons

import Plus from 'vue-material-design-icons/Plus.vue'
import DatabaseEyeOutline from 'vue-material-design-icons/DatabaseEyeOutline.vue'
import DatabaseCogOutline from 'vue-material-design-icons/DatabaseCogOutline.vue'
import LayersSearchOutline from 'vue-material-design-icons/LayersSearchOutline.vue'
import LayersOutline from 'vue-material-design-icons/LayersOutline.vue'
import FileTreeOutline from 'vue-material-design-icons/FileTreeOutline.vue'
import Finance from 'vue-material-design-icons/Finance.vue'
import BookOpenVariantOutline from 'vue-material-design-icons/BookOpenVariantOutline.vue'
import OfficeBuildingOutline from 'vue-material-design-icons/OfficeBuildingOutline.vue'
import ShapeOutline from 'vue-material-design-icons/ShapeOutline.vue'
import Web from 'vue-material-design-icons/Web.vue'
import MenuClose from 'vue-material-design-icons/MenuClose.vue'

export default {
	name: 'MainMenu',
	components: {
		// components
		NcAppNavigation,
		NcAppNavigationList,
		NcAppNavigationItem,
		NcAppNavigationNew,
		NcAppNavigationSettings,
		Configuration,
		// icons
		Plus,
		DatabaseEyeOutline,
		DatabaseCogOutline,
		LayersSearchOutline,
		LayersOutline,
		FileTreeOutline,
		Finance,
		BookOpenVariantOutline,
		OfficeBuildingOutline,
		ShapeOutline,
		Web,
		MenuClose,
	},
	data() {
		return {

			// all of this is settings and should be moved
			settingsOpen: false,
			orc_location: '',
			orc_key: '',
			drc_location: '',
			drc_key: '',
			elastic_location: '',
			elastic_key: '',
			loading: true,
			organization_name: '',
			organization_oin: '',
			organization_pki: '',
			configuration: {
				external: false,
				drcLocation: '',
				drcKey: '',
				orcLocation: '',
				orcKey: '',
				elasticLocation: '',
				elasticKey: '',
				elasticIndex: '',
				mongodbLocation: '',
				mongodbKey: '',
				mongodbCluster: '',
				organizationName: '',
				organizationOin: '',
				organizationPki: '',
			},
			configurationSuccess: -1,
			feedbackPosition: '',
			debounceTimeout: false,
		}
	},
	mounted() {
		catalogiStore.refreshCatalogiList()
	},
	methods: {
		switchCatalogus(catalogus) {
			if (catalogus.id !== navigationStore.selectedCatalogus) publicationStore.setPublicationItem(false) // for when you switch catalogus
			navigationStore.setSelected('publication')
			navigationStore.setSelectedCatalogus(catalogus.id)
			catalogiStore.setCatalogiItem(catalogus)
		},
		openLink(url, type = '') {
			window.open(url, type)
		},
	},
}
</script>
<style>
table {
	table-layout: fixed;
}

td.row-name {
	padding-inline-start: 16px;
}

td.row-size {
	text-align: right;
	padding-inline-end: 16px;
}

.table-header {
	font-weight: normal;
	color: var(--color-text-maxcontrast);
}

.sort-icon {
	color: var(--color-text-maxcontrast);
	position: relative;
	inset-inline: -10px;
}

.row-size .sort-icon {
	inset-inline: 10px;
}
</style>
