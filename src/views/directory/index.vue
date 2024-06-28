<template>
	<NcContent app-name="opencatalog">
		<MainMenu selected="directory" />
		<NcAppContent>
			<template #list>
				<DirectoryList @activeDirectory="updateActiveDirectory" @activePDirectoryId="updateActiveDirectoryId" />
			</template>
			<template #default>
				<NcEmptyContent v-if="activeDirectory !== true" name="Geen Directory" description="Nog geen directory geselecteerd">
					<template #icon>
						<LayersOutline />
					</template>
					<template #action />
				</NcEmptyContent>
				<DirectoryDetails v-if="activeDirectory === true" />
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
			activeDirectory: false,
			activeDirectoryId: '',
		}
	},
	methods: {
		updateDirectory(variable) {
			this.activeDirectory = variable
		},
		updateDirectoryId(variable) {
			this.activeDirectoryId = variable
		},
	},
}
</script>
