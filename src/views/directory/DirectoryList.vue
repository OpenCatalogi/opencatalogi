<script setup>
import { store } from '../../store.js'
</script>

<template>
	<NcAppContentList>
		<ul v-if="!loading">
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
				<NcActions>
					<NcActionButton @click="fetchData">
						<template #icon>
							<Refresh :size="20" />
						</template>
						Ververs
					</NcActionButton>
					<NcActionButton @click="store.setModal('addListing')">
						<template #icon>
							<Plus :size="20" />
						</template>
						Listing toevoegen aan directory
					</NcActionButton>
				</NcActions>
			</div>

			<NcListItem v-for="(directory, i) in directoryList.results"
				:key="`${directory}${i}`"
				:name="directory?.title"
				:active="store.directoryId === directory?.id"
				:details="'1h'"
				:counter-number="44"
				@click="storeDirectory(directory)">
				<template #icon>
					<LayersOutline :class="store.directoryId === directory.id && 'selectedZaakIcon'"
						disable-menu
						:size="44"
						user="janedoe"
						display-name="Jane Doe" />
				</template>
				<template #subname>
					{{ directory?.title }}
				</template>
				<template #actions>
					<NcActionButton @click="editDirectory(directory)">
						Bewerken
					</NcActionButton>
					<NcActionButton @click="deleteDirectory(directory.id)">
						Verwijderen
					</NcActionButton>
				</template>
			</NcListItem>
		</ul>
		<NcLoadingIcon v-if="loading"
			class="loadingIcon"
			:size="64"
			appearance="dark"
			name="Directories aan het laden" />
	</NcAppContentList>
</template>
<script>
import { NcListItem, NcActionButton, NcAppContentList, NcTextField, NcLoadingIcon, NcActions } from '@nextcloud/vue'
// eslint-disable-next-line n/no-missing-import
import Magnify from 'vue-material-design-icons/Magnify'
// eslint-disable-next-line n/no-missing-import
import LayersOutline from 'vue-material-design-icons/LayersOutline'
import Plus from 'vue-material-design-icons/Plus.vue'
import Refresh from 'vue-material-design-icons/Refresh.vue'

export default {
	name: 'DirectoryList',
	components: {
		NcListItem,
		NcActions,
		NcActionButton,
		NcAppContentList,
		NcTextField,
		LayersOutline,
		Magnify,
		NcLoadingIcon,
		Refresh,
		Plus,
	},
	data() {
		return {
			search: '',
			loading: false,
			directoryList: [],
		}
	},
	mounted() {
		this.fetchData()
	},
	methods: {
		storeDirectory(directory) {
			store.setDirectoryId(directory.id)
			store.setDirectoryItem(directory)
		},
		editDirectory(directory) {
			store.setDirectoryItem(directory)
			store.setDirectoryId(directory.id)
			store.setModal('editDirectory')
		},
		fetchData(newPage) {
			this.loading = true
			fetch(
				'/index.php/apps/opencatalogi/api/directory',
				{
					method: 'GET',
				},
			)
				.then((response) => {
					response.json().then((data) => {
						this.directoryList = data
					})
					this.loading = false
				})
				.catch((err) => {
					console.error(err)
					this.loading = false
				})
		},
		setActive(id) {
			store.setDirectoryItem(id)
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
    margin-block-start: var(--OC-margin-20);
}
</style>
