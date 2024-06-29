<script setup>
import { store } from '../../store.js'
</script>

<template>
	<NcAppContent>		
		<div class="dashboardContainer">
			<h1>Searc</h1>
			<p>Search item {{ store.item}}</p>
		</div>
	</NcAppContent>
</template>

<script>

import { NcAppContent, NcEmptyContent } from '@nextcloud/vue'

export default {
	name: 'Dashboard',
	components: {
		NcAppContent
	}
}
</script>
<style>
.dashboardContainer {
    margin-inline-start: 75px;
    margin-block-start: 20px
}
</style>
