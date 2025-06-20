<script setup>
import { navigationStore, objectStore } from '../../store/store.js'
</script>

<template>
	<ul>
		<div class="listHeader">
			<NcTextField class="searchField"
				:value="objectStore.getSearchTerm('listing')"
				label="Zoeken"
				trailing-button-icon="close"
				:show-trailing-button="objectStore.getSearchTerm('listing') !== ''"
				@update:value="(value) => objectStore.setSearchTerm('listing', value)"
				@trailing-button-click="objectStore.clearSearch('listing')">
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
				<NcActionButton close-after-click
					:disabled="objectStore.isLoading('listing')"
					@click="objectStore.fetchCollection('listing')">
					<template #icon>
						<Refresh :size="20" />
					</template>
					Ververs
				</NcActionButton>
				<NcActionButton close-after-click @click="navigationStore.setModal('addDirectory')">
					<template #icon>
						<Plus :size="20" />
					</template>
					Directory inlezen
				</NcActionButton>
			</NcActions>
		</div>

		<div v-if="!objectStore.isLoading('listing')">
			<NcListItem v-for="(listing, i) in objectStore.getCollection('listing').results"
				:key="`${listing}${i}`"
				:name="listing.name ?? listing.title"
				:active="objectStore.getActiveObject('listing')?.id === listing?.id"
				:details="listing.organization?.title || 'Geen organisatie'"
				:counter-number="listing.publicationTypes?.length || '0'"
				:force-display-actions="true"
				@click="objectStore.getActiveObject('listing')?.id === listing?.id ? objectStore.clearActiveObject('listing') : objectStore.setActiveObject('listing', listing)">
				<template #icon>
					<LayersOutline :class="objectStore.getActiveObject('listing')?.id === listing?.id && 'selectedIcon'"
						disable-menu
						:size="44" />
				</template>
				<template #subname>
					{{ listing?.summary }}
				</template>
			</NcListItem>
		</div>

		<NcLoadingIcon v-if="objectStore.isLoading('listing')"
			class="loadingIcon"
			:size="64"
			appearance="dark"
			name="Listings aan het laden" />

		<NcEmptyContent
			v-if="!objectStore.getCollection('listing').results.length && !objectStore.isLoading('listing')"
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
import { NcListItem, NcActionButton, NcTextField, NcLoadingIcon, NcActions, NcEmptyContent, NcButton } from '@nextcloud/vue'
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
	methods: {
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
