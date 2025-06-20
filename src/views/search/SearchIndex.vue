/**
 * SearchIndex.vue
 * Component for displaying the search index
 * @category Components
 * @package opencatalogi
 * @author Ruben Linde
 * @copyright 2024
 * @license AGPL-3.0-or-later
 * @version 1.0.0
 * @link https://github.com/opencatalogi/opencatalogi
 */

<script setup>
import { useSearchStore } from '../../store/modules/search.ts'
</script>

<template>
	<NcAppContent>
		<div class="viewContainer">
			<!-- Header -->
			<div class="viewHeader">
				<h1 class="viewHeaderTitleIndented">
					{{ t('opencatalogi', 'Search Publications') }}
				</h1>
				<p>{{ t('opencatalogi', 'Search and explore publications across all catalogs') }}</p>
			</div>

			<!-- Actions Bar -->
			<div class="viewActionsBar">
				<div class="viewInfo">
					<span class="viewTotalCount">
						{{ t('opencatalogi', 'Showing {showing} of {total} publications', { 
							showing: searchStore.getSearchResults.length, 
							total: searchStore.getPagination.total || searchStore.getSearchResults.length 
						}) }}
					</span>
					<span v-if="searchStore.getSelectedPublications.length > 0" class="viewIndicator">
						({{ t('opencatalogi', '{count} selected', { count: searchStore.getSelectedPublications.length }) }})
					</span>
				</div>
				<div class="viewActions">
					<div class="viewModeSwitchContainer">
						<NcCheckboxRadioSwitch
							v-tooltip="'See publications as cards'"
							:checked="searchStore.getViewMode === 'cards'"
							:button-variant="true"
							value="cards"
							name="publications_view_mode"
							type="radio"
							button-variant-grouped="horizontal"
							@update:checked="() => searchStore.setViewMode('cards')">
							Cards
						</NcCheckboxRadioSwitch>
						<NcCheckboxRadioSwitch
							v-tooltip="'See publications as a table'"
							:checked="searchStore.getViewMode === 'table'"
							:button-variant="true"
							value="table"
							name="publications_view_mode"
							type="radio"
							button-variant-grouped="horizontal"
							@update:checked="() => searchStore.setViewMode('table')">
							Table
						</NcCheckboxRadioSwitch>
					</div>

					<NcActions
						:force-name="true"
						:inline="3"
						menu-name="Actions">
						<NcActionButton
							close-after-click
							:disabled="searchStore.isLoading"
							@click="performSearch">
							<template #icon>
								<Refresh :size="20" />
							</template>
							Refresh
						</NcActionButton>
						<NcActionButton
							title="View documentation about search"
							@click="openLink('https://conduction.gitbook.io/opencatalogi-nextcloud/gebruikers/zoeken', '_blank')">
							<template #icon>
								<HelpCircleOutline :size="20" />
							</template>
							Help
						</NcActionButton>
					</NcActions>
				</div>
			</div>

			<!-- Error State -->
			<NcEmptyContent v-if="searchStore.getError"
				:name="t('opencatalogi', 'Search Error')"
				:description="searchStore.getError">
				<template #icon>
					<AlertCircleOutline :size="64" />
				</template>
				<template #action>
					<NcButton type="primary" @click="performSearch">
						{{ t('opencatalogi', 'Try Again') }}
					</NcButton>
				</template>
			</NcEmptyContent>

			<!-- Loading State -->
			<NcEmptyContent v-else-if="searchStore.isLoading"
				:name="t('opencatalogi', 'Searching publications...')"
				:description="t('opencatalogi', 'Please wait while we search for publications.')">
				<template #icon>
					<NcLoadingIcon :size="64" />
				</template>
			</NcEmptyContent>

			<!-- No Results State -->
			<NcEmptyContent v-else-if="!searchStore.getSearchResults.length && !searchStore.isLoading"
				:name="searchStore.getSearchTerm ? t('opencatalogi', 'No publications found') : t('opencatalogi', 'No publications available')"
				:description="searchStore.getSearchTerm ? t('opencatalogi', 'Try adjusting your search terms or filters in the sidebar') : t('opencatalogi', 'There are no publications available in the system')">
				<template #icon>
					<FileDocumentOutline :size="64" />
				</template>
			</NcEmptyContent>

			<!-- Search Results -->
			<div v-else-if="searchStore.getSearchResults.length" class="searchResults">
				<template v-if="searchStore.getViewMode === 'cards'">
					<div class="cardGrid">
						<div v-for="publication in searchStore.getSearchResults" :key="publication.id" class="card">
							<div class="cardHeader">
								<h2 v-tooltip.bottom="publication.summary || publication.description">
									<FileDocumentOutline :size="20" />
									{{ publication.title || publication.name }}
								</h2>
								<NcActions :primary="true" menu-name="Actions">
									<template #icon>
										<DotsHorizontal :size="20" />
									</template>
									<NcActionButton close-after-click @click="viewPublication(publication)">
										<template #icon>
											<Eye :size="20" />
										</template>
										View
									</NcActionButton>
									<NcActionButton close-after-click @click="viewPublicationUses(publication)">
										<template #icon>
											<LinkVariant :size="20" />
										</template>
										View Uses
									</NcActionButton>
									<NcActionButton close-after-click @click="viewPublicationUsed(publication)">
										<template #icon>
											<LinkVariantOff :size="20" />
										</template>
										View Used By
									</NcActionButton>
									<NcActionButton close-after-click @click="downloadPublication(publication)">
										<template #icon>
											<Download :size="20" />
										</template>
										Download
									</NcActionButton>
								</NcActions>
							</div>
							<!-- Publication Information Table -->
							<table class="statisticsTable publicationStats">
								<thead>
									<tr>
										<th>{{ t('opencatalogi', 'Property') }}</th>
										<th>{{ t('opencatalogi', 'Value') }}</th>
										<th>{{ t('opencatalogi', 'Status') }}</th>
									</tr>
								</thead>
								<tbody>
									<tr v-if="publication.status">
										<td>{{ t('opencatalogi', 'Status') }}</td>
										<td>{{ publication.status }}</td>
										<td>{{ publication.published ? 'Published' : 'Draft' }}</td>
									</tr>
									<tr v-if="publication.summary || publication.description">
										<td>{{ t('opencatalogi', 'Description') }}</td>
										<td class="truncatedText">
											{{ publication.summary || publication.description }}
										</td>
										<td>{{ 'Available' }}</td>
									</tr>
									<tr v-if="publication.license">
										<td>{{ t('opencatalogi', 'License') }}</td>
										<td>{{ publication.license }}</td>
										<td>{{ 'Available' }}</td>
									</tr>
									<tr v-if="publication.version">
										<td>{{ t('opencatalogi', 'Version') }}</td>
										<td>{{ publication.version }}</td>
										<td>{{ 'Available' }}</td>
									</tr>
									<tr v-if="publication.modified">
										<td>{{ t('opencatalogi', 'Last Modified') }}</td>
										<td>{{ formatDate(publication.modified) }}</td>
										<td>{{ 'Available' }}</td>
									</tr>
								</tbody>
							</table>
						</div>
					</div>
				</template>
				<template v-else>
					<div class="viewTableContainer">
						<table class="viewTable">
							<thead>
								<tr>
									<th class="tableColumnCheckbox">
										<NcCheckboxRadioSwitch
											:checked="allSelected"
											:indeterminate="someSelected"
											@update:checked="toggleSelectAll" />
									</th>
									<th>{{ t('opencatalogi', 'Title') }}</th>
									<th>{{ t('opencatalogi', 'Status') }}</th>
									<th>{{ t('opencatalogi', 'License') }}</th>
									<th>{{ t('opencatalogi', 'Version') }}</th>
									<th>{{ t('opencatalogi', 'Modified') }}</th>
									<th class="tableColumnActions">
										{{ t('opencatalogi', 'Actions') }}
									</th>
								</tr>
							</thead>
							<tbody>
								<tr v-for="publication in searchStore.getSearchResults"
									:key="publication.id"
									class="viewTableRow"
									:class="{ viewTableRowSelected: searchStore.getSelectedPublications.includes(publication.id) }">
									<td class="tableColumnCheckbox">
										<NcCheckboxRadioSwitch
											:checked="searchStore.getSelectedPublications.includes(publication.id)"
											@update:checked="(checked) => searchStore.togglePublicationSelection(publication.id, checked)" />
									</td>
									<td class="tableColumnTitle">
										<div class="titleContent">
											<strong>{{ publication.title || publication.name }}</strong>
											<span v-if="publication.summary || publication.description" class="textDescription textEllipsis">
												{{ publication.summary || publication.description }}
											</span>
										</div>
									</td>
									<td>{{ publication.status || 'Unknown' }}</td>
									<td>{{ publication.license || '-' }}</td>
									<td>{{ publication.version || '-' }}</td>
									<td class="tableColumnConstrained">
										<span v-if="publication.modified">{{ formatDate(publication.modified) }}</span>
										<span v-else>-</span>
									</td>
									<td class="tableColumnActions">
										<NcActions :primary="false">
											<template #icon>
												<DotsHorizontal :size="20" />
											</template>
											<NcActionButton close-after-click @click="viewPublication(publication)">
												<template #icon>
													<Eye :size="20" />
												</template>
												View
											</NcActionButton>
											<NcActionButton close-after-click @click="viewPublicationUses(publication)">
												<template #icon>
													<LinkVariant :size="20" />
												</template>
												View Uses
											</NcActionButton>
											<NcActionButton close-after-click @click="viewPublicationUsed(publication)">
												<template #icon>
													<LinkVariantOff :size="20" />
												</template>
												View Used By
											</NcActionButton>
											<NcActionButton close-after-click @click="downloadPublication(publication)">
												<template #icon>
													<Download :size="20" />
												</template>
												Download
											</NcActionButton>
										</NcActions>
									</td>
								</tr>
							</tbody>
						</table>
					</div>
				</template>

				<!-- Pagination -->
				<PaginationComponent
					v-if="searchStore.getSearchResults.length"
					:current-page="searchStore.getPagination.page || 1"
					:total-pages="searchStore.getPagination.pages || 1"
					:total-items="searchStore.getPagination.total || searchStore.getSearchResults.length"
					:current-page-size="searchStore.getPagination.limit || 20"
					:min-items-to-show="0"
					@page-changed="onPageChanged"
					@page-size-changed="onPageSizeChanged" />
			</div>
		</div>
	</NcAppContent>
</template>

<script>
import { 
	NcAppContent, 
	NcEmptyContent, 
	NcLoadingIcon, 
	NcActions, 
	NcActionButton, 
	NcCheckboxRadioSwitch, 
	NcButton
} from '@nextcloud/vue'

// Icons
import FileDocumentOutline from 'vue-material-design-icons/FileDocumentOutline.vue'
import DotsHorizontal from 'vue-material-design-icons/DotsHorizontal.vue'
import Eye from 'vue-material-design-icons/Eye.vue'
import Download from 'vue-material-design-icons/Download.vue'
import LinkVariant from 'vue-material-design-icons/LinkVariant.vue'
import LinkVariantOff from 'vue-material-design-icons/LinkVariantOff.vue'
import Refresh from 'vue-material-design-icons/Refresh.vue'
import Close from 'vue-material-design-icons/Close.vue'
import Magnify from 'vue-material-design-icons/Magnify.vue'
import HelpCircleOutline from 'vue-material-design-icons/HelpCircleOutline.vue'
import AlertCircleOutline from 'vue-material-design-icons/AlertCircleOutline.vue'

import PaginationComponent from '../../components/PaginationComponent.vue'

export default {
	name: 'SearchIndex',
	components: {
		NcAppContent,
		NcEmptyContent,
		NcLoadingIcon,
		NcActions,
		NcActionButton,
		NcCheckboxRadioSwitch,
		NcButton,
		FileDocumentOutline,
		DotsHorizontal,
		Eye,
		Download,
		LinkVariant,
		LinkVariantOff,
		Refresh,
		Close,
		Magnify,
		HelpCircleOutline,
		AlertCircleOutline,
		PaginationComponent,
	},
	data() {
		return {
			searchStore: useSearchStore(),
		}
	},
	computed: {
		allSelected() {
			return this.searchStore.getSearchResults.length > 0 && 
				   this.searchStore.getSearchResults.every(pub => this.searchStore.getSelectedPublications.includes(pub.id))
		},
		someSelected() {
			return this.searchStore.getSelectedPublications.length > 0 && !this.allSelected
		},
	},
	mounted() {
		console.info('SearchIndex mounted')
		// Load initial results or perform search if there's already a search term
		if (this.searchStore.getSearchTerm) {
			this.performSearch()
		} else {
			// Load some initial results to show when page opens
			this.searchStore.loadInitialResults()
		}
	},
	methods: {
		async performSearch() {
			console.info('Performing search with term:', this.searchStore.getSearchTerm)
			await this.searchStore.searchPublications()
		},
		toggleSelectAll(checked) {
			if (checked) {
				this.searchStore.selectAllPublications()
			} else {
				this.searchStore.clearAllSelections()
			}
		},
		async onPageChanged(page) {
			console.info('Page changed to:', page)
			await this.searchStore.searchPublications({ _page: page })
		},
		async onPageSizeChanged(pageSize) {
			console.info('Page size changed to:', pageSize)
			await this.searchStore.searchPublications({ _page: 1, _limit: pageSize })
		},
		viewPublication(publication) {
			console.info('Viewing publication:', publication)
			// TODO: Implement publication detail view
			// This could open a modal or navigate to a detail page
		},
		async viewPublicationUses(publication) {
			console.info('Viewing publication uses:', publication)
			try {
				const uses = await this.searchStore.getPublicationUses(publication.id)
				// TODO: Display uses in a modal or separate view
				console.info('Publication uses:', uses)
			} catch (error) {
				console.error('Failed to fetch publication uses:', error)
			}
		},
		async viewPublicationUsed(publication) {
			console.info('Viewing publication used by:', publication)
			try {
				const used = await this.searchStore.getPublicationUsed(publication.id)
				// TODO: Display used by in a modal or separate view
				console.info('Publication used by:', used)
			} catch (error) {
				console.error('Failed to fetch publication used by:', error)
			}
		},
		downloadPublication(publication) {
			console.info('Downloading publication:', publication)
			// Open download URL in new tab
			window.open(`/index.php/apps/opencatalogi/api/search/${publication.id}/download`, '_blank')
		},
		formatDate(dateString) {
			if (!dateString) return '-'
			try {
				return new Date(dateString).toLocaleDateString()
			} catch (error) {
				return dateString
			}
		},
		openLink(url, type = '') {
			window.open(url, type)
		},
	},
}
</script>

<style scoped>
.truncatedText {
	max-width: 200px;
	overflow: hidden;
	text-overflow: ellipsis;
	white-space: nowrap;
	display: inline-block;
}
</style>
