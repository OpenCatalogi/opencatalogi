<script setup>
import { store } from '../../store.js'
</script>

<template>
	<div class="detailContainer">
		<div v-if="!loading" id="app-content">
			<!-- app-content-wrapper is optional, only use if app-content-list  -->
			<div>
				<h1 class="h1">
					{{ directory.title }}
				</h1>
				<div>
					<h4>Sammenvatting:</h4>
					<p>{{ directory.summary }}</p>
				</div>
				<div>
					<h4>Search:</h4>
					<span>{{ directory.search }}</span>
				</div>
				<div>
					<h4>MetaData:</h4>
					<span>{{ directory.metadata }}</span>
				</div>
				<div>
					<h4>Status:</h4>
					<span>{{ directory.status }}</span>
				</div>
				<div>
					<h4>Last synchronized:</h4>
					<span>{{ directory.lastSync }}</span>
				</div>
				<div>
					<h4>Default:</h4>
					<span>{{ directory.default }}</span>
				</div>
				<div>
					<h4>Available:</h4>
					<span>{{ directory.available }}</span>
				</div>
			</div>
		</div>
		<NcLoadingIcon v-if="loading"
			:size="100"
			appearance="dark"
			name="Directory details aan het laden" />
	</div>
</template>

<script>
import { NcLoadingIcon } from '@nextcloud/vue'

export default {
	name: 'DirectoryDetails',
	components: {
		NcLoadingIcon,
	},
	props: {
		directoryId: {
			type: String,
			required: true,
		},
	},
	data() {
		return {
			directory: [],
			loading: false,
		}
	},
	watch: {
		directoryId: {
			handler(directoryId) {
				this.fetchData(directoryId)
			},
			deep: true,
		},
	},
	mounted() {
		this.fetchData()
	},
	methods: {
		fetchData() {
			this.loading = true
			fetch(
				'/index.php/apps/opencatalogi/api/directory/' + store.directoryItem.id,
				{
					method: 'GET',
				},
			)
				.then((response) => {
					response.json().then((data) => {
						this.directory = data
					})
					this.loading = false
				})
				.catch((err) => {
					console.error(err)
					this.loading = false
				})
		},
	},
}
</script>

<style>
h4 {
  font-weight: bold
}

.h1 {
  display: block !important;
  font-size: 2em !important;
  margin-block-start: 0.67em !important;
  margin-block-end: 0.67em !important;
  margin-inline-start: 0px !important;
  margin-inline-end: 0px !important;
  font-weight: bold !important;
  unicode-bidi: isolate !important;
}

.grid {
  display: grid;
  grid-gap: 24px;
  grid-template-columns: 1fr 1fr;
  margin-block-start: var(--OC-margin-50);
  margin-block-end: var(--OC-margin-50);
}

.gridContent {
  display: flex;
  gap: 25px;
}

.tabContainer>* ul>li {
  display: flex;
  flex: 1;
}

.tabContainer>* ul>li:hover {
  background-color: var(--color-background-hover);
}

.tabContainer>* ul>li>a {
  flex: 1;
  text-align: center;
}

.tabContainer>* ul>li>.active {
  background: transparent !important;
  color: var(--color-main-text) !important;
  border-bottom: var(--default-grid-baseline) solid var(--color-primary-element) !important;
}

.tabContainer>* ul {
  display: flex;
  margin: 10px 8px 0 8px;
  justify-content: space-between;
  border-bottom: 1px solid var(--color-border);
}

.tabPanel {
  padding: 20px 10px;
  min-height: 100%;
  max-height: 100%;
  height: 100%;
  overflow: auto;
}
</style>
