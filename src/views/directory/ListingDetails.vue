<script setup>
import { navigationStore, directoryStore } from '../../store/store.js'
</script>

<template>
	<div class="detailContainer">
		<div class="head">
			<h1 class="h1">
				{{ listing.title }}
			</h1>
			<div class="flex-hor">
				<a target="_blank" href="https://conduction.gitbook.io/opencatalogi-nextcloud/beheerders/directory">
					<NcButton type="tertiary-no-background">
						<template #icon>
							<HelpCircleOutline :size="20" />
						</template>
					</NcButton>
				</a>
				<NcActions :disabled="loading" :primary="true" :menu-name="loading ? 'Laden...' : 'Acties'">
					<template #icon>
						<span>
							<NcLoadingIcon v-if="loading"
								:size="20"
								appearance="dark" />
							<DotsHorizontal v-if="!loading" :size="20" />
						</span>
					</template>
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
				</NcActions>
			</div>
		</div>
		<div>
			<div>
				<h4>Samenvatting:</h4>
				<p>{{ listing.summary }}</p>
			</div>
			<div>
				<h4>Search:</h4>
				<span>{{ listing.search }}</span>
			</div>
			<div>
				<h4>MetaData:</h4>
				<span>{{ listing.metadata }}</span>
			</div>
			<div>
				<h4>Status:</h4>
				<span>{{ listing.status }}</span>
			</div>
			<div>
				<h4>Last synchronized:</h4>
				<span>{{ listing.lastSync }}</span>
			</div>
			<div>
				<h4>Default:</h4>
				<span>{{ listing.default }}</span>
			</div>
			<div>
				<h4>Available:</h4>
				<span>{{ listing.available }}</span>
			</div>
		</div>
	</div>
</template>

<script>
import {
	NcActions,
	NcActionButton,
	NcButton,
	NcLoadingIcon,
} from '@nextcloud/vue'

import DotsHorizontal from 'vue-material-design-icons/DotsHorizontal.vue'
import Pencil from 'vue-material-design-icons/Pencil.vue'
import Delete from 'vue-material-design-icons/Delete.vue'
import HelpCircleOutline from 'vue-material-design-icons/HelpCircleOutline.vue'

export default {
	name: 'ListingDetails',
	components: {
		NcLoadingIcon,
	},
	props: {
		listingItem: {
			type: Object,
			required: true,
		},
	},
	data() {
		return {

			listing: [],
			loading: false,
			upToDate: false,
		}
	},
	watch: {
		listingItem: {
			handler(newListingItem, oldListingItem) {
				if (!this.upToDate || JSON.stringify(newListingItem) !== JSON.stringify(oldListingItem)) {
					this.listing = newListingItem
					newListingItem && this.fetchData(newListingItem?.id)
					this.upToDate = true
				}
			},
			deep: true,
		},
	},
	mounted() {
		directoryStore.listingItem && this.fetchData(directoryStore.listingItem?.id)
	},
	methods: {
		fetchData(listingId) {
			this.loading = true
			fetch(
				'/index.php/apps/opencatalogi/api/directory/' + listingId,
				{
					method: 'GET',
				},
			)
				.then((response) => {
					response.json().then((data) => {
						this.listing = data
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

.flex-hor {
    display: flex;
    gap: 4px;
}
</style>
