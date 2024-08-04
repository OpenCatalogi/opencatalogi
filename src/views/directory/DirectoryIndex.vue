<script setup>
import { navigationStore, searchStore, directoryStore } from '../../store/store.js'
</script>

<template>
	<NcAppContent>
		<template #list>
			<DirectoryList :search="searchStore.search" />
		</template>
		<template #default>
			<NcEmptyContent v-if="!directoryStore.listingItem || navigationStore.selected != 'directory' "
				class="detailContainer"
				name="Geen directory"
				description="Nog geen directory geselecteerd">
				<template #icon>
					<LayersOutline />
				</template>
				<template #action>
					<NcButton type="primary" @click="navigationStore.setModal('addListing')">
						Directory toevoegen
					</NcButton>
				</template>
			</NcEmptyContent>
			<ListingDetails v-if="directoryStore.listingItem && navigationStore.selected === 'directory'" :listing-item="directoryStore.listingItem" />
		</template>
	</NcAppContent>
</template>

<script>
import { NcAppContent, NcEmptyContent, NcButton } from '@nextcloud/vue'
import DirectoryList from './DirectoryList.vue'
import ListingDetails from './ListingDetails.vue'
// eslint-disable-next-line n/no-missing-import
import LayersOutline from 'vue-material-design-icons/LayersOutline'

export default {
	name: 'DirectoryIndex',
	components: {
		NcAppContent,
		NcEmptyContent,
		NcButton,
		DirectoryList,
		ListingDetails,
		LayersOutline,
	},
	data() {
		return {

		}
	},
}
</script>
