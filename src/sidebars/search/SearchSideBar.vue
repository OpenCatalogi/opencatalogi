<script setup>
import { useSearchStore } from '../../store/modules/search.ts'
</script>

<template>
	<div class="searchSideBar">
		<div class="searchSideBar-header">
			<h2>{{ t('opencatalogi', 'Search Filters') }}</h2>
		</div>
		<div class="searchSideBar-content">
			<!-- Search Input -->
			<div class="searchSideBar-content-item">
				<h3>{{ t('opencatalogi', 'Search Term') }}</h3>
				<NcTextField
					:value="searchStore.getSearchTerm"
					:label="t('opencatalogi', 'Search publications...')"
					trailing-button-icon="close"
					:show-trailing-button="searchStore.getSearchTerm !== ''"
					@update:value="onSearchTermChange"
					@trailing-button-click="clearSearch"
					@keydown.enter="performSearch">
					<template #leading-icon>
						<Magnify :size="20" />
					</template>
				</NcTextField>
			</div>

			<!-- Search Actions -->
			<div class="searchSideBar-content-item">
				<div class="searchActions">
					<NcButton
						type="primary"
						:disabled="searchStore.isLoading"
						@click="performSearch">
						<template #icon>
							<Magnify :size="20" />
						</template>
						{{ t('opencatalogi', 'Search') }}
					</NcButton>
					<NcButton
						type="secondary"
						@click="clearSearch">
						<template #icon>
							<Close :size="20" />
						</template>
						{{ t('opencatalogi', 'Clear') }}
					</NcButton>
				</div>
			</div>

			<!-- Ordering Section -->
			<div class="searchSideBar-content-item">
				<h3>{{ t('opencatalogi', 'Sort Results') }}</h3>
				
				<!-- Current ordering display -->
				<div v-if="Object.keys(searchStore.getOrdering).length > 0" class="currentOrdering">
					<h4>{{ t('opencatalogi', 'Current Sorting') }}</h4>
					<div v-for="(direction, field) in searchStore.getOrdering" :key="field" class="orderingItem">
						<span class="orderingField">{{ formatOrderingLabel(field) }}</span>
						<span class="orderingDirection">{{ direction }}</span>
						<NcButton
							type="tertiary-no-background"
							:aria-label="t('opencatalogi', 'Remove sorting')"
							@click="removeOrdering(field)">
							<template #icon>
								<Close :size="16" />
							</template>
						</NcButton>
					</div>
					<NcButton
						type="secondary"
						size="small"
						@click="clearAllOrdering">
						{{ t('opencatalogi', 'Clear all sorting') }}
					</NcButton>
				</div>

				<!-- Add new ordering -->
				<div class="addOrdering">
					<h4>{{ t('opencatalogi', 'Add Sorting') }}</h4>
					<div class="orderingControls">
						<NcSelect
							v-model="selectedOrderField"
							:options="orderingOptions"
							:placeholder="t('opencatalogi', 'Select field to sort by')"
							label="title"
							track-by="value"
							:allow-empty="false"
							:searchable="true">
						</NcSelect>
						<div class="orderingDirectionButtons">
							<NcButton
								:type="'secondary'"
								size="small"
								:disabled="!selectedOrderField"
								@click="addOrdering('ASC')">
								<template #icon>
									<ArrowUp :size="16" />
								</template>
								{{ t('opencatalogi', 'ASC') }}
							</NcButton>
							<NcButton
								:type="'secondary'"
								size="small"
								:disabled="!selectedOrderField"
								@click="addOrdering('DESC')">
								<template #icon>
									<ArrowDown :size="16" />
								</template>
								{{ t('opencatalogi', 'DESC') }}
							</NcButton>
						</div>
					</div>
				</div>
			</div>

			<!-- Dynamic Filters from Facetable Data -->
			<div v-if="availableFilters.length > 0" class="searchSideBar-content-item">
				<h3>{{ t('opencatalogi', 'Filters') }}</h3>
				<div class="searchSideBar-content-item-filters">
					<!-- System Metadata Filters -->
					<div v-for="filter in systemFilters" :key="filter.key" class="searchSideBar-content-item-filters-item">
						<h4>{{ filter.label }}</h4>
						<div v-for="option in filter.options" :key="option.value" class="filterOption">
							<NcCheckboxRadioSwitch
								:checked="searchStore.getFilters[filter.key] === option.value"
								@update:checked="(checked) => toggleFilter(filter.key, option.value, checked)">
								{{ option.label }} ({{ option.count }})
							</NcCheckboxRadioSwitch>
						</div>
					</div>

					<!-- Object Field Filters -->
					<div v-for="filter in objectFieldFilters" :key="filter.key" class="searchSideBar-content-item-filters-item">
						<h4>{{ filter.label }}</h4>
						<div v-if="filter.type === 'categorical'" class="filterOptions">
							<div v-for="option in filter.options" :key="option.value" class="filterOption">
								<NcCheckboxRadioSwitch
									:checked="searchStore.getFilters[filter.key] === option.value"
									@update:checked="(checked) => toggleFilter(filter.key, option.value, checked)">
									{{ option.value }} ({{ option.count || 'N/A' }})
								</NcCheckboxRadioSwitch>
							</div>
						</div>
						<div v-else-if="filter.type === 'boolean'" class="filterOptions">
							<NcCheckboxRadioSwitch
								:checked="searchStore.getFilters[filter.key] === 'true'"
								@update:checked="(checked) => toggleFilter(filter.key, 'true', checked)">
								{{ t('opencatalogi', 'Yes') }}
							</NcCheckboxRadioSwitch>
							<NcCheckboxRadioSwitch
								:checked="searchStore.getFilters[filter.key] === 'false'"
								@update:checked="(checked) => toggleFilter(filter.key, 'false', checked)">
								{{ t('opencatalogi', 'No') }}
							</NcCheckboxRadioSwitch>
						</div>
					</div>
				</div>
			</div>

			<!-- Legacy Facets (fallback) -->
			<div v-else-if="Object.keys(searchStore.getFacets).length > 0" class="searchSideBar-content-item">
				<h3>{{ t('opencatalogi', 'Filters') }}</h3>
				<div class="searchSideBar-content-item-filters">
					<div v-for="(facet, key) in searchStore.getFacets" :key="key" class="searchSideBar-content-item-filters-item">
						<h4>{{ key }}</h4>
						<div v-for="(count, value) in facet" :key="value" class="filterOption">
							<NcCheckboxRadioSwitch
								:checked="searchStore.getFilters[key] === value"
								@update:checked="(checked) => toggleFilter(key, value, checked)">
								{{ value }} ({{ count }})
							</NcCheckboxRadioSwitch>
						</div>
					</div>
				</div>
			</div>

			<!-- Search Statistics -->
			<div v-if="searchStore.getSearchTerm || searchStore.getPagination.total > 0" class="searchSideBar-content-item">
				<h3>{{ t('opencatalogi', 'Search Results') }}</h3>
				<div class="searchStats">
					<p v-if="searchStore.getSearchTerm">
						<strong>{{ t('opencatalogi', 'Search term:') }}</strong> "{{ searchStore.getSearchTerm }}"
					</p>
					<p v-if="searchStore.getPagination.total > 0">
						<strong>{{ t('opencatalogi', 'Results:') }}</strong> {{ searchStore.getPagination.total }} {{ t('opencatalogi', 'publications found') }}
					</p>
					<p v-if="Object.keys(searchStore.getOrdering).length > 0">
						<strong>{{ t('opencatalogi', 'Sorted by:') }}</strong> {{ Object.keys(searchStore.getOrdering).length }} {{ t('opencatalogi', 'field(s)') }}
					</p>
					<p v-if="searchStore.isLoading">
						<em>{{ t('opencatalogi', 'Searching...') }}</em>
					</p>
				</div>
			</div>
		</div>
	</div>
</template>

<script>
import { NcTextField, NcButton, NcCheckboxRadioSwitch, NcSelect } from '@nextcloud/vue'
import Magnify from 'vue-material-design-icons/Magnify.vue'
import Close from 'vue-material-design-icons/Close.vue'
import ArrowUp from 'vue-material-design-icons/ArrowUp.vue'
import ArrowDown from 'vue-material-design-icons/ArrowDown.vue'

export default {
	name: 'SearchSideBar',
	components: {
		NcTextField,
		NcButton,
		NcCheckboxRadioSwitch,
		NcSelect,
		Magnify,
		Close,
		ArrowUp,
		ArrowDown,
	},
	data() {
		return {
			searchStore: useSearchStore(),
			searchTimeout: null,
			selectedOrderField: null,
		}
	},
	computed: {
		/**
		 * Get available filters from facetable data
		 */
		availableFilters() {
			const facetable = this.searchStore.getFacetable
			if (!facetable || (!facetable['@self'] && !facetable.object_fields)) {
				return []
			}
			
			const filters = []
			
			// Add system metadata filters
			if (facetable['@self']) {
				Object.entries(facetable['@self']).forEach(([key, config]) => {
					if (config.sample_values && config.sample_values.length > 0) {
						filters.push({
							key: key, // Use the key directly without @self. prefix to avoid URL encoding issues
							label: this.formatFilterLabel(key),
							type: config.type,
							options: config.sample_values,
							category: 'system'
						})
					}
				})
			}
			
			// Add object field filters
			if (facetable.object_fields) {
				Object.entries(facetable.object_fields).forEach(([key, config]) => {
					if (config.sample_values && config.sample_values.length > 0 && this.shouldShowFilter(key, config)) {
						filters.push({
							key,
							label: this.formatFilterLabel(key),
							type: config.type,
							options: config.sample_values.map(value => ({ value, count: 'N/A' })),
							category: 'object'
						})
					}
				})
			}
			
			return filters
		},
		
		/**
		 * Get system metadata filters
		 */
		systemFilters() {
			return this.availableFilters.filter(f => f.category === 'system')
		},
		
		/**
		 * Get object field filters
		 */
		objectFieldFilters() {
			return this.availableFilters.filter(f => f.category === 'object')
		},
		
		/**
		 * Get available fields for ordering
		 */
		orderingOptions() {
			const options = []
			const facetable = this.searchStore.getFacetable
			
			// Add system metadata fields (with @self. prefix for ordering)
			if (facetable['@self']) {
				Object.entries(facetable['@self']).forEach(([key, config]) => {
					options.push({
						value: `@self.${key}`,
						title: `${this.formatFilterLabel(key)} (System)`,
						category: 'system'
					})
				})
			}
			
			// Add object fields that are suitable for ordering
			if (facetable.object_fields) {
				Object.entries(facetable.object_fields).forEach(([key, config]) => {
					if (this.shouldShowForOrdering(key, config)) {
						options.push({
							value: key,
							title: `${this.formatFilterLabel(key)} (Object)`,
							category: 'object'
						})
					}
				})
			}
			
			// Sort options by category and title
			return options.sort((a, b) => {
				if (a.category !== b.category) {
					return a.category === 'system' ? -1 : 1
				}
				return a.title.localeCompare(b.title)
			})
		},
	},
	mounted() {
		console.info('SearchSideBar mounted')
		// Only perform search if there's already a search term
		// Initial results loading is handled by SearchIndex
		if (this.searchStore.getSearchTerm) {
			this.performSearch()
		}
	},
	methods: {
		onSearchTermChange(value) {
			this.searchStore.setSearchTerm(value)
			
			// Clear existing timeout
			if (this.searchTimeout) {
				clearTimeout(this.searchTimeout)
			}
			
			// Set new timeout for real-time search
			this.searchTimeout = setTimeout(() => {
				this.performSearch()
			}, 500) // 500ms delay for real-time search
		},
		async performSearch() {
			console.info('Performing search from sidebar with term:', this.searchStore.getSearchTerm)
			await this.searchStore.searchPublications()
		},
		clearSearch() {
			console.info('Clearing search from sidebar')
			if (this.searchTimeout) {
				clearTimeout(this.searchTimeout)
			}
			this.searchStore.clearSearch()
		},
		toggleFilter(key, value, checked) {
			if (checked) {
				this.searchStore.setFilters({ [key]: value })
			} else {
				this.searchStore.clearFilter(key)
			}
			// Automatically search when filters change
			this.performSearch()
		},
		
		/**
		 * Add ordering for the selected field
		 */
		addOrdering(direction) {
			if (this.selectedOrderField) {
				this.searchStore.setOrdering(this.selectedOrderField.value, direction)
				this.selectedOrderField = null // Reset selection
				this.performSearch()
			}
		},
		
		/**
		 * Remove ordering for a field
		 */
		removeOrdering(field) {
			this.searchStore.removeOrdering(field)
			this.performSearch()
		},
		
		/**
		 * Clear all ordering
		 */
		clearAllOrdering() {
			this.searchStore.clearOrdering()
			this.performSearch()
		},
		
		/**
		 * Format filter label for display
		 */
		formatFilterLabel(key) {
			// Convert camelCase and snake_case to readable labels
			return key
				.replace(/([A-Z])/g, ' $1')
				.replace(/_/g, ' ')
				.replace(/\b\w/g, l => l.toUpperCase())
				.trim()
		},
		
		/**
		 * Format ordering label for display
		 */
		formatOrderingLabel(field) {
			// Remove @self. prefix for display
			const cleanField = field.replace('@self.', '')
			return this.formatFilterLabel(cleanField)
		},
		
		/**
		 * Determine if a filter should be shown
		 */
		shouldShowFilter(key, config) {
			// Skip filters with empty values or very high cardinality
			if (!config.sample_values || config.sample_values.length === 0) {
				return false
			}
			
			// Skip fields that are likely not useful for filtering
			const skipFields = ['id', 'extend', 'attachments.endpoint', 'attachments.filename']
			if (skipFields.includes(key)) {
				return false
			}
			
			// Skip fields with only empty values
			if (config.sample_values.every(val => val === '' || val === null)) {
				return false
			}
			
			// Show categorical and boolean fields
			if (config.type === 'boolean' || config.type === 'categorical') {
				return true
			}
			
			// Show string fields with low cardinality
			if (config.type === 'string' && config.cardinality === 'low') {
				return true
			}
			
			return false
		},
		
		/**
		 * Determine if a field should be available for ordering
		 */
		shouldShowForOrdering(key, config) {
			// Skip fields that are not useful for ordering
			const skipFields = ['id', 'extend', 'attachments.endpoint', 'attachments.filename']
			if (skipFields.includes(key)) {
				return false
			}
			
			// Show date, numeric, and string fields
			if (['date', 'numeric', 'numeric_string', 'string', 'integer'].includes(config.type)) {
				return true
			}
			
			return false
		},
	},
}
</script>

<style scoped>
.searchSideBar {
	padding: 1rem;
	height: 100%;
	overflow-y: auto;
}

.searchSideBar-header {
	margin-bottom: 1rem;
}

.searchSideBar-content-item {
	margin-bottom: 1.5rem;
}

.searchSideBar-content-item-filters-item {
	margin-bottom: 1rem;
}

.searchActions {
	display: flex;
	flex-direction: column;
	gap: 0.5rem;
}

.filterOption {
	margin-bottom: 0.5rem;
}

.filterOptions {
	max-height: 200px;
	overflow-y: auto;
}

/* Ordering Styles */
.currentOrdering {
	margin-bottom: 1rem;
	padding: 0.75rem;
	background-color: var(--color-background-hover);
	border-radius: var(--border-radius);
}

.orderingItem {
	display: flex;
	align-items: center;
	gap: 0.5rem;
	margin-bottom: 0.5rem;
	padding: 0.25rem;
	background-color: var(--color-main-background);
	border-radius: var(--border-radius);
}

.orderingField {
	flex: 1;
	font-weight: 500;
}

.orderingDirection {
	padding: 0.125rem 0.5rem;
	background-color: var(--color-primary-element);
	color: var(--color-primary-element-text);
	border-radius: var(--border-radius);
	font-size: 0.8em;
	font-weight: bold;
}

.addOrdering {
	margin-top: 1rem;
}

.orderingControls {
	display: flex;
	flex-direction: column;
	gap: 0.5rem;
}

.orderingDirectionButtons {
	display: flex;
	gap: 0.5rem;
}

.searchStats {
	background-color: var(--color-background-hover);
	padding: 0.75rem;
	border-radius: var(--border-radius);
	font-size: 0.9em;
}

.searchStats p {
	margin: 0.25rem 0;
}

h2 {
	margin: 0;
	font-size: 1.2em;
}

h3 {
	margin: 0 0 0.5rem 0;
	font-size: 1.1em;
	font-weight: 600;
}

h4 {
	margin: 0 0 0.5rem 0;
	font-size: 1em;
	font-weight: 500;
	color: var(--color-text-maxcontrast);
}
</style>
