<script setup>
import { UIStore } from '../store/store.js'
</script>

<template>
	<div>
		<!-- Placeholder div for all of the sidebars-->
		<SearchSideBar v-if="UIStore.selected === 'search'" />
		<DashboardSideBar v-if="UIStore.selected === 'dashboard'" />
	</div>
</template>

<script>
import SearchSideBar from './search/SearchSideBar.vue'
import DashboardSideBar from './dashboard/DashboardSideBar.vue'

export default {
	name: 'SideBars',
	components: {
		SearchSideBar,
		DashboardSideBar,
	},
	data() {
		return {

		}
	},
}
</script>
