/**
 * SearchResults.vue
 * Reusable component for displaying search results
 * @category Components
 * @package opencatalogi
 * @author Ruben Linde
 * @copyright 2024
 * @license AGPL-3.0-or-later
 * @version 1.0.0
 * @link https://github.com/opencatalogi/opencatalogi
 */

<script setup>
import { ref, computed, onMounted } from 'vue'
import { objectStore } from '../store/store.js'
import { NcEmptyContent, NcLoadingIcon, NcTextField, NcListItem, NcActionButton } from '@nextcloud/vue'
import FolderIcon from 'vue-material-design-icons/Folder.vue'
import FileIcon from 'vue-material-design-icons/File.vue'
import MagnifyIcon from 'vue-material-design-icons/Magnify.vue'
import RefreshIcon from 'vue-material-design-icons/Refresh.vue'

/**
 * Props for the SearchResults component
 * @typedef {object} Props
 * @property {string} [containerClass] - Additional CSS class for the container
 * @property {string} [searchClass] - Additional CSS class for the search field
 * @property {string} [resultsClass] - Additional CSS class for the results container
 */

defineProps({
	containerClass: {
		type: String,
		default: '',
	},
	searchClass: {
		type: String,
		default: '',
	},
	resultsClass: {
		type: String,
		default: '',
	},
})

/**
 * Loading state for the component
 * @type {import('vue').Ref<boolean>}
 */
const loading = ref(false)

/**
 * Fetch search data from the store
 * @return {Promise<void>}
 */
const fetchData = async () => {
	loading.value = true
	try {
		await objectStore.fetchCollection('search')
	} finally {
		loading.value = false
	}
}

/**
 * Get all search results from the store
 * @return {Array<object>}
 */
const results = computed(() => objectStore.getCollection('search').results)

/**
 * Check if there are any results
 * @return {boolean}
 */
const hasResults = computed(() => results.value.length === 0)

// Fetch data when component is mounted
onMounted(() => {
	fetchData()
})
</script>

<template>
	<div :class="['search-results', containerClass]">
		<div class="search-results__header">
			<NcTextField :class="['search-results__search', searchClass]"
				:value="objectStore.getSearchTerm('search')"
				label="Zoeken"
				trailing-button-icon="close"
				:show-trailing-button="objectStore.getSearchTerm('search') !== ''"
				@update:value="(value) => objectStore.setSearchTerm('search', value)"
				@trailing-button-click="objectStore.clearSearch('search')">
				<template #icon>
					<MagnifyIcon />
				</template>
			</NcTextField>
			<div class="search-results__actions">
				<NcActionButton close-after-click @click="fetchData">
					<template #icon>
						<RefreshIcon />
					</template>
					{{ t('opencatalogi', 'Vernieuwen') }}
				</NcActionButton>
			</div>
		</div>
		<NcEmptyContent v-if="!hasResults" :title="t('opencatalogi', 'Geen resultaten gevonden')">
			<template #icon>
				<FolderIcon />
			</template>
		</NcEmptyContent>
		<NcLoadingIcon v-else-if="loading" :size="20" />
		<div v-else :class="['search-results__list', resultsClass]">
			<NcListItem v-for="result in results"
				:key="result.id"
				:title="result.title"
				:subtitle="result.summary"
				:to="'/' + result.type + 's/' + result.id">
				<template #icon>
					<FileIcon />
				</template>
			</NcListItem>
		</div>
	</div>
</template>

<style scoped>
.search-results {
	padding: 20px;
}

.search-results__header {
	display: flex;
	justify-content: space-between;
	align-items: center;
	margin-bottom: 20px;
}

.search-results__search {
	max-width: 300px;
}

.search-results__actions {
	display: flex;
	gap: 10px;
}

.search-results__list {
	display: flex;
	flex-direction: column;
	gap: 10px;
}
</style>
