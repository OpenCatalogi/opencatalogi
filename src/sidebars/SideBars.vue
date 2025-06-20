/**
 * SideBars.vue
 * Component for displaying sidebars
 * @category Components
 * @package opencatalogi
 * @author Ruben Linde
 * @copyright 2024
 * @license AGPL-3.0-or-later
 * @version 1.0.0
 * @link https://github.com/opencatalogi/opencatalogi
 */

<script setup>
import { computed, ref } from 'vue'
import { objectStore, navigationStore } from '../store/store.js'
import SearchSideBar from './search/SearchSideBar.vue'

// Reactive state for sidebar visibility
const isSidebarOpen = ref(true)
</script>

<template>
	<div class="sidebars">
		<!-- Search Sidebar -->
		<NcAppSidebar 
			v-if="isSearchPage" 
			:name="t('opencatalogi', 'Search Publications')"
			:open="isSidebarOpen"
			@update:open="(e) => isSidebarOpen = e">
			<SearchSideBar />
		</NcAppSidebar>

		<!-- Directory Sidebar -->
		<NcAppSidebar v-if="directory" :title="directory.title">
			<template #description>
				{{ directory.description }}
			</template>
			<template #actions>
				<NcButton type="primary" @click="navigationStore.setModal('editDirectory')">
					<template #icon>
						<Pencil :size="20" />
					</template>
					Bewerken
				</NcButton>
			</template>
		</NcAppSidebar>

		<!-- Listing Sidebar -->
		<NcAppSidebar v-if="listing" :title="listing.title">
			<template #description>
				{{ listing.description }}
			</template>
			<template #actions>
				<NcButton type="primary" @click="navigationStore.setModal('editListing')">
					<template #icon>
						<Pencil :size="20" />
					</template>
					Bewerken
				</NcButton>
			</template>
		</NcAppSidebar>
	</div>
</template>

<script>
import { NcAppSidebar, NcButton } from '@nextcloud/vue'
import Pencil from 'vue-material-design-icons/Pencil.vue'

/**
 * Get the active directory from the store
 * @return {object | null}
 */
const directory = computed(() => objectStore.getActiveObject('directory'))

/**
 * Get the active listing from the store
 * @return {object | null}
 */
const listing = computed(() => objectStore.getActiveObject('listing'))

/**
 * Check if we're on the search page
 * @return {boolean}
 */
const isSearchPage = computed(() => navigationStore.selected === 'search')

export default {
	name: 'SideBars',
	components: {
		NcAppSidebar,
		NcButton,
		SearchSideBar,
		Pencil,
	},
}
</script>

<style scoped>
.sidebars {
	display: flex;
	flex-direction: column;
	gap: 20px;
}
</style>
