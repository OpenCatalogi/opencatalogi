<script setup>
import { store } from '../../store.js'
</script>

<template>
	<NcAppContent>
		<div class="dashboardContainer">
			<h1>Searc</h1>
			<p>Search item {{ store.search }}</p>
		</div>
	</NcAppContent>
</template>

<script>

import { NcAppContent } from '@nextcloud/vue'

export default {
	name: 'SearchIndex',
	components: {
		NcAppContent,
	},
}
</script>
<style>
.dashboardContainer {
    margin-inline-start: 75px;
    margin-block-start: 20px
}
</style>
