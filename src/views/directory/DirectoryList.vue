<script setup>
import { navigationStore, directoryStore } from '../../store/store.js'
</script>

<template>
	<NcAppContentList>
		<ul>
			<div class="listHeader">
				<NcTextField class="searchField"
					:value.sync="search"
					label="Zoeken"
					trailing-button-icon="close"
					:show-trailing-button="search !== ''"
					@trailing-button-click="search = ''">
					<Magnify :size="20" />
				</NcTextField>
				<NcActions>
					<NcActionButton :disabled="loading" @click="fetchData">
						<template #icon>
							<Refresh :size="20" />
						</template>
						Ververs
					</NcActionButton>
					<NcActionButton @click="navigationStore.setModal('addListing')">
						<template #icon>
							<Plus :size="20" />
						</template>
						Listing toevoegen aan directory
					</NcActionButton>
				</NcActions>
			</div>

			<div v-if="!loading">
				<NcListItem v-for="(listing, i) in directoryStore.listingList"
					:key="`${listing}${i}`"
					:name="listing.name ?? listing.title"
					:active="directoryStore.listingItem?.id === listing?.id"
					:details="'1h'"
					:counter-number="45"
					@click="directoryStore.setListingItem(listing)">
					<template #icon>
						<LayersOutline :class="directoryStore.listingItem?.id === listing?.id && 'selectedIcon'"
							disable-menu
							:size="44" />
					</template>
					<template #subname>
						{{ listing?.title }}
					</template>
					<template #actions>
						<NcActionButton @click="directoryStore.setListingItem(listing); navigationStore.setModal('editListing')">
							<template #icon>
								<Pencil :size="20" />
							</template>
							Bewerken
						</NcActionButton>
						<NcActionButton @click="directoryStore.setListingItem(listing); navigationStore.setDialog('deleteListing')">
							<template #icon>
								<Delete :size="20" />
							</template>
							Verwijderen
						</NcActionButton>
					</template>
				</NcListItem>
			</div>

			<NcLoadingIcon v-if="loading"
				class="loadingIcon"
				:size="64"
				appearance="dark"
				name="Listings aan het laden" />
		</ul>
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
import { debounce } from 'lodash'

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
	beforeRouteLeave(to, from, next) {
		search = ''
		next()
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
			search: '',
		}
	},
	watch: {
		search: {
			handler(search) {
				this.debouncedFetchData(search)
			},
		},
	},
	mounted() {
		this.fetchData()
	},
	methods: {
		fetchData(search = null) {
			this.loading = true
			directoryStore.refreshListingList(search)
				.then(() => {
					this.loading = false
				})
		},
		debouncedFetchData: debounce(function(search) {
			this.fetchData(search)
		}, 500),
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
