<script setup>
import { store } from '../../store.js'
</script>

<template>
	<NcAppContent>
		<div class="dashboardContainer">
			<h2>Dashboard</h2>
		</div>
		<p>Hier dus wat statestieken</p>
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
    margin-inline-start: 65px;
    margin-block-start: 20px
}
</style>
