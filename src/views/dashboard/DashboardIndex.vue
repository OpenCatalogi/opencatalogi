<template>
	<NcAppContent>
		<div class="dashboardContainer">
			<h2>Dashboard</h2>
		</div>
		<b>Aantal zoekopdrachten afglopen maand</B>
		<apexchart
			width="500"
			type="line"
			:options="options"
			:series="series" />
	</NcAppContent>
</template>

<script>

import { NcAppContent } from '@nextcloud/vue'
import VueApexCharts from 'vue-apexcharts'

export default {
	name: 'DashboardIndex',
	components: {
		NcAppContent,
		apexchart: VueApexCharts,
	},
	data() {
		return {
			options: {
				chart: {
					id: 'vuechart-example',
				},
				xaxis: {
					categories: [1991, 1992, 1993, 1994, 1995, 1996, 1997, 1998],
				},
			},
			series: [{
				name: 'series-1',
				data: [30, 40, 45, 50, 49, 60, 70, 91],
			}],
		}
	},
}
</script>

<style>
.dashboardContainer {
    margin-inline-start: 65px;
    margin-block-start: 20px
}
</style>
