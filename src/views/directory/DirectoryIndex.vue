<script setup>
import { store } from '../../store/store.js'
</script>

<template>
	<NcAppContent>
		<template #list>
			<DirectoryList :search="store.search" />
		</template>
		<template #default>
			<NcEmptyContent v-if="!store.listingItem || store.selected != 'directory' "
				class="detailContainer"
				name="Geen directory"
				description="Nog geen directory geselecteerd">
				<template #icon>
					<LayersOutline />
				</template>
				<template #action>
					<NcButton type="primary" @click="store.setModal('addListing')">
						Directory toevoegen
					</NcButton>
				</template>
			</NcEmptyContent>
			<ListingDetails v-if="store.listingItem && store.selected === 'directory'" :listing-item="store.listingItem" />
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
}
</script>
