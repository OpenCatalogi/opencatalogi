<script setup>
import { searchStore } from '../../store/store.js'
</script>

<template>
	<NcAppContent>
		<h2 class="pageHeader">
			Dashboard
		</h2>

		<div class="dashboard-content">
			<div class="most-searched-terms">
				<div>
					<h5>Term</h5>
					<div class="content">
						23
					</div>
				</div>
				<div>
					<h5>Queries</h5>
					<div class="content">
						543
					</div>
				</div>
				<div>
					<h5>Clicks</h5>
					<div class="content">
						65433
					</div>
				</div>
			</div>

			<div class="graphs">
				<div>
					<h5>Queries per dag</h5>
					<div class="content">
						<apexchart
							width="500"
							type="line"
							:options="queriesMonth.graph.options"
							:series="queriesMonth.graph.series" />
					</div>
				</div>
				<div>
					<h5>Queries per uur</h5>
					<div class="content">
						<apexchart
							width="500"
							type="donut"
							:options="queriesMonth.pie.options"
							:series="queriesMonth.pie.series" />
					</div>
				</div>
			</div>
		</div>
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
			// mock data
			queriesMonth: {
				graph: {
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
				},
				pie: {
					options: {
						labels: ['Apple', 'Mango', 'Orange', 'Watermelon'],
						dataLabels: {
							enabled: true,
							formatter(val) {
								return val + ' queries'
							},
						},
					},
					series: [44, 55, 13, 33],
				},
			},
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

<style>
.pageHeader {
    margin-block-end: 4rem;
}

.dashboard-content {
    margin-inline: auto;
    max-width: 1000px;
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
}
.dashboard-content > *:not(:last-child) {
    margin-block-end: 4rem;
}

/* most searched terms */
.dashboard-content > .most-searched-terms {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 2rem;
    max-width: 750px;
}
.dashboard-content > .most-searched-terms > div {
    padding: 1rem;
    background-color: rgba(255, 255, 255, 0.1);
    height: 200px;
    border-radius: 8px;
}
.dashboard-content > .most-searched-terms > div > h5 {
    margin: 0;
    font-weight: normal;
}
.dashboard-content > .most-searched-terms > div > .content {
    display: flex;
    justify-content: center;
    align-items: center;
    height: calc(100% - 40px);

    font-size: 3rem;
}

/* graphs */
.dashboard-content > .graphs {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 2rem;
}
.dashboard-content > .graphs .full-width {
    grid-column-start: 1;
    grid-column-end: 3;
}
.dashboard-content > .graphs .content {
    display: flex;
    gap: 4px;
}
</style>
