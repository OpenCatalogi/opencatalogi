<script setup>
import { store } from '../../store.js'
</script>

<template>
	<div class="detailContainer">
		<div v-if="!loading" id="app-content">
			<NcListItem v-for="(publication, i) in publications.results"
				:key="`${publication}${i}`"
				:name="publication?.name"
				:bold="false"
				:force-display-actions="true"
				:active="activePublicationId === publication.id"
				:details="'CC0 1.0'"
				:counter-number="1"
				@click="setActive(publication.id)">
				<template #icon>
					<ListBoxOutline :class="activePublicationId === publication.id && 'selectedZaakIcon'"
						disable-menu
						:size="44"
						user="janedoe"
						display-name="Jane Doe" />
				</template>
				<template #subname>
					{{ publication?.summary }}
				</template>
				<template #actions>
					<NcActionButton>
						Bewerken
					</NcActionButton>
					<NcActionButton>
						Depubliceren
					</NcActionButton>
				</template>
			</NcListItem>
		</div>
		<NcLoadingIcon v-if="loading"
			:size="100"
			appearance="dark"
			name="Publicatie details aan het laden" />
	</div>
</template>

<script>
import { NcLoadingIcon, NcListItem, NcActionButton } from '@nextcloud/vue'
// eslint-disable-next-line n/no-missing-import
import ListBoxOutline from 'vue-material-design-icons/ListBoxOutline'

export default {
	name: 'CatalogDetail',
	components: {
		NcLoadingIcon,
		NcListItem,
		NcActionButton,
		ListBoxOutline,
	},
	data() {
		return {
			publications: [],
			loading: false,
		}
	},
	watch: {
		catalogId: {
			handler(catalogId) {
				this.fetchData(catalogId)
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
				'/index.php/apps/opencatalogi/api/publications/' + store.item,
				{
					method: 'GET',
				},
			)
				.then((response) => {
					response.json().then((data) => {
						this.publications = data
						// this.oldZaakId = id
					})
					this.loading = false
				})
				.catch((err) => {
					console.error(err)
					// this.oldZaakId = id
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
  margin-block-start: var(--zaa-margin-50);
  margin-block-end: var(--zaa-margin-50);
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
