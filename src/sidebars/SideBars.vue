<script setup>
import { store } from '../store.js'
</script>

<template>
	<!-- Placeholder div for all of the vieuws-->
	<SearchSideBar v-if="store.selected === 'search'" />
</template>

<script>
import SearchSideBar from './search/SearchSideBar.vue'

export default {
	name: 'SideBars',
	components: {
		SearchSideBar,
	},
}
</script>
