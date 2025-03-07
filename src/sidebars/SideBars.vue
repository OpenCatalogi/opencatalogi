<script setup>
import { navigationStore, searchStore, directoryStore } from '../store/store.js'
</script>

<template>
	<div>
		<!-- Placeholder div for all of the sidebars-->
		<SearchSideBar v-if="navigationStore.selected === 'search'"
			:search="searchStore.search"
			:publication-type="searchStore.publicationType"
			:catalogi="searchStore.catalogi" />
		<DashboardSideBar v-if="navigationStore.selected === 'dashboard'" />
		<DirectorySideBar v-if="navigationStore.selected === 'directory'" :listing-item="directoryStore.listingItem" />
	</div>
</template>

<script>
import SearchSideBar from './search/SearchSideBar.vue'
import DashboardSideBar from './dashboard/DashboardSideBar.vue'
import DirectorySideBar from './directory/DirectorySideBar.vue'

export default {
	name: 'SideBars',
	components: {
		SearchSideBar,
		DashboardSideBar,
		DirectorySideBar,
	},
	data() {
		return {

		}
	},
}
</script>
