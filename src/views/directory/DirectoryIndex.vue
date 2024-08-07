<script setup>
import { navigationStore, searchStore, directoryStore } from '../../store/store.js'
</script>

<template>
	<NcAppContent>
		<h2 class="pageHeader">
			Directory
		</h2>
		<DirectoryList :search="searchStore.search" />
	</NcAppContent>
</template>

<script>
import { NcAppContent, NcEmptyContent, NcButton } from '@nextcloud/vue'
import DirectoryList from './DirectoryList.vue'
import ListingDetails from './ListingDetails.vue'
// eslint-disable-next-line n/no-missing-import
import LayersOutline from 'vue-material-design-icons/LayersOutline.vue'
import Plus from 'vue-material-design-icons/Plus.vue'
import HelpCircleOutline from 'vue-material-design-icons/HelpCircleOutline.vue'

export default {
	name: 'DirectoryIndex',
	components: {
		NcAppContent,
		NcEmptyContent,
		NcButton,
		DirectoryList,
		ListingDetails,
		// Icons
		LayersOutline,
		Plus,
		HelpCircleOutline,
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
