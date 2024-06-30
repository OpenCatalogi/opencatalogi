<script setup>
import { store } from '../../store.js'
</script>

<template>
	<NcAppContent>
		<template #list>
			<DirectoryList @directoryId="updateDirectoryId" />
		</template>
		<template #default>
			<NcEmptyContent v-if="!store.item || store.selected != 'directory' "
				class="detailContainer"
				name="Geen Directory"
				description="Nog geen directory geselecteerd">
				<template #icon>
					<LayersOutline />
				</template>
				<template #action>
					<NcButton type="primary" @click="store.setModal('directoryAdd')">
						Directory toevoegen
					</NcButton>
				</template>
			</NcEmptyContent>
			<DirectoryDetails v-if="store.item && store.selected === 'directory'" :directory-id="directoryId" />
		</template>
	</NcAppContent>
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
