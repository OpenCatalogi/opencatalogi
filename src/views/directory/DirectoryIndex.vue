<script setup>
import { store } from '../../store.js'
</script>

<template>
	<NcAppContent>
		<template #list>
			<DirectoryList />
		</template>
		<template #default>
			<NcEmptyContent v-if="!store.directoryItem || store.selected != 'directory' "
				class="detailContainer"
				name="Geen Directory"
				description="Nog geen directory geselecteerd">
				<template #icon>
					<LayersOutline />
				</template>
				<template #action>
					<NcButton type="primary" @click="store.setModal('listingAdd')">
						Directory toevoegen
					</NcButton>
				</template>
			</NcEmptyContent>
			<DirectoryDetails v-if="store.directoryItem && store.selected === 'directory'" :directory-id="directoryId" />
		</template>
	</NcAppContent>
</template>

<script>
import { NcAppContent, NcEmptyContent, NcButton } from '@nextcloud/vue'
import DirectoryList from './DirectoryList.vue'
import DirectoryDetails from './DirectoryDetails.vue'
// eslint-disable-next-line n/no-missing-import
import LayersOutline from 'vue-material-design-icons/LayersOutline'

export default {
	name: 'DirectoryIndex',
	components: {
		NcAppContent,
		NcEmptyContent,
		NcButton,
		DirectoryList,
		DirectoryDetails,
		LayersOutline,
	},
	data() {
		return {
			directoryId: undefined,
		}
	},
}
</script>
