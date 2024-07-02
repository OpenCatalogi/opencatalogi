<script setup>
import { store } from '../../store.js'
</script>

<template>
	<NcAppContentList>
		<ul>
			<div class="listHeader">
				<NcTextField class="searchField"
					disabled
					:value.sync="search"
					label="Search"
					trailing-button-icon="close"
					:show-trailing-button="search !== ''"
					@trailing-button-click="clearText">
					<Magnify :size="20" />
				</NcTextField>
			</div>
			<div v-if="!loading">
				<NcListItem v-for="(publication, i) in publications.results"
					:key="`${publication}${i}`"
					:name="publication?.title"
					:bold="false"
					:force-display-actions="true"
					:active="store.publicationItem === publication._id"
					:details="'CC0 1.0'"
					:counter-number="1"
					@click="store.setPublicationItem(publication._id)">
					<template #icon>
						<ListBoxOutline :class="store.publicationItem === publication._id && 'selectedZaakIcon'"
							disable-menu
							:size="44"
							user="janedoe"
							display-name="Jane Doe" />
					</template>
					<template #subname>
						{{ publication?.description }}
					</template>
					<template #actions>
						<NcActionButton>
							Bewerken
						</NcActionButton>
						<NcActionButton>
							Depubliceren
						</NcActionButton>
						<NcActionButton @click="deletePublication(publication._id)">
							Verwijderen
						</NcActionButton>
					</template>
				</NcListItem>
			</div>

			<NcLoadingIcon v-if="loading"
				:size="64"
				class="loadingIcon"
				appearance="dark"
				name="Zaken aan het laden" />
		</ul>
	</NcAppContentList>
</template>
<script>
import { NcListItem, NcActionButton, NcAppContentList, NcTextField, NcLoadingIcon } from '@nextcloud/vue'
import Magnify from 'vue-material-design-icons/Magnify.vue'
import ListBoxOutline from 'vue-material-design-icons/ListBoxOutline.vue'

export default {
	name: 'PublicationList',
	components: {
		NcListItem,
		NcActionButton,
		NcAppContentList,
		NcTextField,
		ListBoxOutline,
		Magnify,
		NcLoadingIcon,
	},
	data() {
		return {
			search: '',
			loading: false,
			publications: [],
		}
	},
	mounted() {
		this.fetchData()
	},
	methods: {
		fetchData(newPage) {
			this.loading = true
			fetch(
				'/index.php/apps/opencatalog/publications/api',
				{
					method: 'GET',
				},
			)
				.then((response) => {
					response.json().then((data) => {
						this.publications = data
					})
					this.loading = false
				})
				.catch((err) => {
					console.error(err)
					this.loading = false
				})
		},
		deletePublication(id) {
			fetch(
				`/index.php/apps/opencatalog/publications/api/${id}`,
				{
					method: 'DELETE',
					headers: {
						'Content-Type': 'application/json',
					},
				},
			)
				.then((response) => {
					console.warn('publication removed')
					// this.succesMessage = true
					// setTimeout(() => (this.succesMessage = false), 2500)
				})
				.catch((err) => {
					console.error(err)
				})
		},
		clearText() {
			this.search = ''
		},
	},
}
</script>
<style>
.listHeader {
    position: sticky;
    top: 0;
    z-index: 1000;
    background-color: var(--color-main-background);
    border-bottom: 1px solid var(--color-border);
}

.searchField {
    padding-inline-start: 65px;
    padding-inline-end: 20px;
    margin-block-end: 6px;
}

.selectedZaakIcon>svg {
    fill: white;
}

.loadingIcon {
    margin-block-start: var(--zaa-margin-20);
}
</style>
