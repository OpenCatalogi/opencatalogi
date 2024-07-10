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
				<NcActions>
					<NcActionButton @click="fetchData">
						<template #icon>
							<Refresh :size="20" />
						</template>
						Ververs
					</NcActionButton>
					<NcActionButton @click="store.setModal('metaDataAdd')">
						<template #icon>
							<Plus :size="20" />
						</template>
						Metadata toevoegen
					</NcActionButton>
				</NcActions>
			</div>

			<div v-if="!loading">
				<NcListItem v-for="(metaData, i) in metaDataList.results"
					:key="`${metaData}${i}`"
					:name="metaData?.name"
					:active="store.metaDataItem === metaData?._id"
					:details="'1h'"
					:counter-number="44"
					@click="store.setMetadataItem(metaData._id)">
					<template #icon>
						<FileTreeOutline :class="store.metaDataItem === metaData._id && 'selectedZaakIcon'"
							disable-menu
							:size="44"
							user="janedoe"
							display-name="Jane Doe" />
					</template>
					<template #subname>
						{{ metaData?.summary }}
					</template>
					<template #actions>
						<NcActionButton>
							Bewerken
						</NcActionButton>
						<NcActionButton>
							Depubliceren
						</NcActionButton>
						<NcActionButton @click="deleteMetaData(metaData._id)">
							Verwijderen
						</NcActionButton>
					</template>
				</NcListItem>
			</div>

			<NcLoadingIcon v-if="loading"
				class="loadingIcon"
				:size="64"
				appearance="dark"
				name="Metadata aan het laden" />
		</ul>
	</NcAppContentList>
</template>
<script>
import { NcListItem, NcActionButton, NcAppContentList, NcTextField, NcLoadingIcon, NcActions } from '@nextcloud/vue'
// eslint-disable-next-line n/no-missing-import
import Magnify from 'vue-material-design-icons/Magnify'
// eslint-disable-next-line n/no-missing-import
import FileTreeOutline from 'vue-material-design-icons/FileTreeOutline'
import Plus from 'vue-material-design-icons/Plus.vue'
import Refresh from 'vue-material-design-icons/Refresh.vue'

export default {
	name: 'MetaDataList',
	components: {
		NcListItem,
		NcActions,
		NcActionButton,
		NcAppContentList,
		NcTextField,
		FileTreeOutline,
		Magnify,
		NcLoadingIcon,
		Refresh,
		Plus,
	},
	data() {
		return {
			search: '',
			loading: true,
			metaDataList: [],
		}
	},
	mounted() {
		this.fetchData()
	},
	methods: {
		fetchData(newPage) {
			this.loading = true
			fetch(
				'/index.php/apps/opencatalogi/api/metadata',
				{
					method: 'GET',
				},
			)
				.then((response) => {
					response.json().then((data) => {
						this.metaDataList = data
					})
					this.loading = false
				})
				.catch((err) => {
					console.error(err)
					this.loading = false
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
    margin-block-start: var(--OC-margin-20);
}
</style>
