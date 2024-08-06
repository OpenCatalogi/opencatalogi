<script setup>
import { searchStore } from '../../store/store.js'
</script>

<template>
	<NcAppContent>
		<h2 class="pageHeader">
			Dashboard
		</h2>
		<b>Aantal zoekopdrachten afgelopen maand</B>
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
	props: {
		search: {
			type: String,
			required: true,
		},
	},
	data() {
		return {
			options: {
				chart: {
					id: 'Aantal bekeken publicaties',
				},
				xaxis: {
					categories: ['7-11', '7-12', '7-13', '7-15', '7-16', '7-17', '7-18'],
				},
			},
			series: [{
				name: 'Weergaven',
				data: [30, 40, 45, 50, 49, 60, 55],
			}],
		}
	},
	watch: {
		search: {
			handler(search) {
				searchStore.getSearchResults()
			},
		},
	},
	mounted() {
		searchStore.getSearchResults()
	},
}
</script>
