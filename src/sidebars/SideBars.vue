<script setup>
import { store } from '../store/store.js'
</script>

<template>
	<div>
		<!-- Placeholder div for all of the sidebars-->
		<SearchSideBar v-if="store.selected === 'search'" />
		<DashboardSideBar v-if="store.selected === 'dashboard'" />
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
}
</script>
