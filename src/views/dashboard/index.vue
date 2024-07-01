<script setup>
import { store } from '../../store.js'
</script>

<template>
	<NcAppContent>		
		<div class="dashboardContainer">
			<h1>Titel Dashboard</h1>
			<p>Selected item {{ store.item}}</p>
		</div>
	</NcAppContent>
</template>

<script>

import { NcAppContent, NcButton } from '@nextcloud/vue'

export default {
	name: 'Dashboard',
	components: {
		NcAppContent,
		NcButton
	}
}
</script>
<style>
.dashboardContainer {
    margin-inline-start: 75px;
    margin-block-start: 20px
}
</style>
