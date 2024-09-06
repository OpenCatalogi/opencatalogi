<script setup>
import { navigationStore, directoryStore } from '../../store/store.js'
</script>

<template>
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
				<NcActionButton
					title="Bekijk de documentatie over catalogi"
					@click="openLink('https://conduction.gitbook.io/opencatalogi-nextcloud/beheerders/directory', '_blank')">
					<template #icon>
						<HelpCircleOutline :size="20" />
					</template>
					Help
				</NcActionButton>
				<NcActionButton :disabled="loading" @click="refresh">
					<template #icon>
						<Refresh :size="20" />
					</template>
					Ververs
				</NcActionButton>
				<NcActionButton @click="navigationStore.setModal('addDirectory')">
					<template #icon>
						<Plus :size="20" />
					</template>
					Directory inlezen
				</NcActionButton>
			</NcActions>
		</div>

		<div v-if="!loading">
			<NcListItem v-for="(listing, i) in directoryStore.listingList"
				:key="`${listing}${i}`"
				:name="listing.name ?? listing.title"
				:active="directoryStore.listingItem?.id === listing?.id"
				:details="listing?.organisation?.title || 'Geen organisatie'"
				:counter-number="listing?.metadata?.length || 0"
				@click="directoryStore.setListingItem(listing)">
				<template #icon>
					<LayersOutline :class="directoryStore.listingItem?.id === listing?.id && 'selectedIcon'"
						disable-menu
						:size="44" />
				</template>
				<template #subname>
					{{ listing?.summary }}
				</template>
			</NcListItem>
		</div>

		<NcLoadingIcon v-if="loading"
			class="loadingIcon"
			:size="64"
			appearance="dark"
			name="Listings aan het laden" />

		<NcEmptyContent
			v-if="!directoryStore.listingList?.length > 0 && !loading"
			class="detailContainer"
			name="Geen Listings"
			description="Je directory of zoek opdracht bevat nog geen listings, wil je een externe directory toevoegen?">
			<template #icon>
				<LayersOutline />
			</template>
			<template #action>
				<NcButton type="primary" @click="navigationStore.setModal('addDirectory')">
					<template #icon>
						<Plus :size="20" />
					</template>
					Directory inlezen
				</NcButton>
				<NcButton @click="openLink('https://conduction.gitbook.io/opencatalogi-nextcloud/beheerders/directory', '_blank')">
					<template #icon>
						<HelpCircleOutline :size="20" />
					</template>
					Meer informatie over de directory
				</NcButton>
			</template>
		</NcEmptyContent>
	</ul>
</template>
<script>
import { debounce } from 'lodash'
import { NcListItem, NcActionButton, NcTextField, NcLoadingIcon, NcActions, NcEmptyContent, NcButton } from '@nextcloud/vue'

// Icons
import Magnify from 'vue-material-design-icons/Magnify.vue'
import LayersOutline from 'vue-material-design-icons/LayersOutline.vue'
import Plus from 'vue-material-design-icons/Plus.vue'
import Refresh from 'vue-material-design-icons/Refresh.vue'
import HelpCircleOutline from 'vue-material-design-icons/HelpCircleOutline.vue'

export default {
	name: 'DirectoryList',
	components: {
		NcListItem,
		NcActions,
		NcActionButton,
		NcTextField,
		NcLoadingIcon,
		NcEmptyContent,
		NcButton,
		// Icons
		LayersOutline,
		Magnify,
		HelpCircleOutline,
		Refresh,
		Plus,
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
		refresh(e) {
			e.preventDefault()
			this.fetchData()
		},
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
		openLink(url, type = '') {
			window.open(url, type)
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
