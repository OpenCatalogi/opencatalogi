<script setup>
import { store } from '../../store.js'
</script>

<template>
	<NcContent app-name="opencatalog">
		<MainMenu selected="directory" />
		<NcAppContent>
			<template #list>
				<DirectoryList @directoryId="updateDirectoryId" />
			</template>
			<template #default>
				<NcEmptyContent v-if="!directoryId"
					class="detailContainer"
					name="Geen Directory"
					description="Nog geen directory geselecteerd">
					<template #icon>
						<LayersOutline />
					</template>
					<template #action />
				</NcEmptyContent>
				<DirectoryDetails v-if="directoryId" :directory-id="directoryId" />
			</template>
		</NcAppContent>
		<!-- <ZaakSidebar /> -->
	</NcContent>
</template>

<script>
import { NcAppContent, NcContent, NcEmptyContent } from '@nextcloud/vue'
import MainMenu from '../../navigation/MainMenu.vue'
import DirectoryList from './list.vue'
import DirectoryDetails from './details.vue'
import LayersOutline from 'vue-material-design-icons/LayersOutline'

export default {
	name: 'DirectoryIndex',
	components: {
		NcContent,
		NcAppContent,
		NcEmptyContent,
		MainMenu,
		DirectoryList,
		DirectoryDetails,
		LayersOutline,
	},
	data() {
		return {
			directoryId: undefined,
		}
	},
	methods: {
		updateDirectoryId(variable) {
			this.directoryId = variable
		},
	},
}
</script>
