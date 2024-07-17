<script setup>
import { store } from '../../store.js'
</script>

<template>
	<NcAppContentList>
		<ul v-if="!loading">
			<div class="listHeader">
				<NcTextField class="searchField"
					:value.sync="store.search"
					label="Search"
					trailing-button-icon="close"
					:show-trailing-button="search !== ''"
					@trailing-button-click="store.setSearch('')">
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

			<NcListItem v-for="(listing, i) in store.listingList.results"
				:key="`${listing}${i}`"
				:name="listing.name ?? listing.title"
				:active="store.listingItem?.id === listing?.id"
				:details="'1h'"
				:counter-number="45"
				@click="store.setListingItem(listing)">
				<template #icon>
					<LayersOutline :class="store.listingItem?.id === listing?.id && 'selectedIcon'"
						disable-menu
						:size="44" />
				</template>
				<template #subname>
					{{ listing?.title }}
				</template>
				<template #actions>
					<NcActionButton @click="store.setListingItem(listing); store.setModal('editListing')">
						<template #icon>
							<Pencil :size="20" />
						</template>
						Bewerken
					</NcActionButton>
					<NcActionButton @click="store.setListingItem(listing); store.setDialog('deleteListing')">
						<template #icon>
							<Delete :size="20" />
						</template>
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
import Pencil from 'vue-material-design-icons/Pencil.vue'
import Delete from 'vue-material-design-icons/Delete.vue'
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
		Pencil,
		Delete,
	},
	props: {
		search: {
			type: String,
			required: true,
		},
	},
	data() {
		return {
			loading: false,
		}
	},
	watch: {
		search: {
			handler(search) {
				this.fetchData()
			},
		},
	},
	mounted() {
		this.fetchData()
	},
	methods: {
		fetchData(newPage) {
			this.loading = true
			store.refreshListingList()
			this.loading = false
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

.selectedIcon>svg {
    fill: white;
}

.loadingIcon {
    margin-block-start: var(--OC-margin-20);
}
</style>
