<template>
	<div class="dashboardContainer">
		<h1>Titel Dashboard</h1>
		<p>{{store.selected}}</p>
	</div>
</template>
<script>
import { store } from './store.js'

export default {
	name: 'Dashboard',
	components: {
		store
	}

}
</script>
<style>
.dashboardContainer {
    margin-inline-start: 75px;
    margin-block-start: 20px
}
</style>
