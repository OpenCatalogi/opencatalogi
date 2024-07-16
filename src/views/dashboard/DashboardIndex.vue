<script setup>
import { store } from '../../store.js'
</script>

<template>
	<NcAppContent>
		<div class="dashboardContainer">
			<h1>Titel Dashboard</h1>
			<p>Selected menu item {{ store.selected }}</p>
		</div>
	</NcAppContent>
</template>

<script>

import { NcAppContent } from '@nextcloud/vue'

export default {
	name: 'DashboardIndex',
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
