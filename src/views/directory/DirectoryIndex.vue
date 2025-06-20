<script setup>
import { objectStore, navigationStore } from '../../store/store.js'
</script>

<template>
	<NcAppContent>
		<div class="viewContainer">
			<!-- Header -->
			<div class="viewHeader">
				<h1 class="viewHeaderTitleIndented">
					{{ t('opencatalogi', 'Directory') }}
				</h1>
				<p>{{ t('opencatalogi', 'Browse and manage directory listings from various catalogs') }}</p>
			</div>

			<!-- Actions Bar -->
			<div class="viewActionsBar">
				<div class="viewInfo">
					<span class="viewTotalCount">
						{{ t('opencatalogi', 'Showing {showing} of {total} listings', { showing: filteredListings.length, total: currentPagination.total || filteredListings.length }) }}
					</span>
					<span v-if="selectedListings.length > 0" class="viewIndicator">
						({{ t('opencatalogi', '{count} selected', { count: selectedListings.length }) }})
					</span>
				</div>
				<div class="viewActions">
					<div class="viewModeSwitchContainer">
						<NcCheckboxRadioSwitch
							v-tooltip="'See listings as cards'"
							:checked="viewMode === 'cards'"
							:button-variant="true"
							value="cards"
							name="listings_view_mode"
							type="radio"
							button-variant-grouped="horizontal"
							@update:checked="() => setViewMode('cards')">
							Cards
						</NcCheckboxRadioSwitch>
						<NcCheckboxRadioSwitch
							v-tooltip="'See listings as a table'"
							:checked="viewMode === 'table'"
							:button-variant="true"
							value="table"
							name="listings_view_mode"
							type="radio"
							button-variant-grouped="horizontal"
							@update:checked="() => setViewMode('table')">
							Table
						</NcCheckboxRadioSwitch>
					</div>

					<NcActions
						:force-name="true"
						:inline="3"
						menu-name="Actions">
						<NcActionButton
							:primary="true"
							close-after-click
							@click="navigationStore.setModal('addDirectory')">
							<template #icon>
								<Plus :size="20" />
							</template>
							Add Directory
						</NcActionButton>
						<NcActionButton
							close-after-click
							:disabled="objectStore.isLoading('listing')"
							@click="objectStore.fetchCollection('listing')">
							<template #icon>
								<Refresh :size="20" />
							</template>
							Refresh
						</NcActionButton>
						<NcActionButton
							title="View documentation about directory"
							@click="openLink('https://conduction.gitbook.io/opencatalogi-nextcloud/beheerders/directory', '_blank')">
							<template #icon>
								<HelpCircleOutline :size="20" />
							</template>
							Help
						</NcActionButton>
					</NcActions>
				</div>
			</div>

			<!-- Loading, Error, and Empty States -->
			<NcEmptyContent v-if="objectStore.isLoading('listing') || !filteredListings.length"
				:name="emptyContentName"
				:description="emptyContentDescription">
				<template #icon>
					<NcLoadingIcon v-if="objectStore.isLoading('listing')" :size="64" />
					<LayersOutline v-else :size="64" />
				</template>
				<template v-if="!objectStore.isLoading('listing') && !objectStore.getCollection('listing')?.results?.length" #action>
					<NcButton type="primary" @click="navigationStore.setModal('addDirectory')">
						{{ t('opencatalogi', 'Add directory') }}
					</NcButton>
				</template>
			</NcEmptyContent>

			<!-- Content -->
			<div v-else>
				<template v-if="viewMode === 'cards'">
					<div class="cardGrid">
						<div v-for="listing in paginatedListings" :key="listing.id" class="card">
							<div class="cardHeader">
								<h2 v-tooltip.bottom="listing.summary">
									<LayersOutline :size="20" />
									{{ listing.name || listing.title }}
								</h2>
								<NcActions :primary="true" menu-name="Actions">
									<template #icon>
										<DotsHorizontal :size="20" />
									</template>
									<NcActionButton close-after-click @click="objectStore.setActiveObject('listing', listing); navigationStore.setModal('viewDirectory')">
										<template #icon>
											<Eye :size="20" />
										</template>
										View
									</NcActionButton>
								</NcActions>
							</div>
							<!-- Listing Statistics Table -->
							<table class="statisticsTable listingStats">
								<thead>
									<tr>
										<th>{{ t('opencatalogi', 'Property') }}</th>
										<th>{{ t('opencatalogi', 'Value') }}</th>
										<th>{{ t('opencatalogi', 'Status') }}</th>
									</tr>
								</thead>
								<tbody>
									<tr v-if="listing.organization">
										<td>{{ t('opencatalogi', 'Organization') }}</td>
										<td>{{ listing.organization.title || listing.organization }}</td>
										<td>{{ 'Available' }}</td>
									</tr>
									<tr v-if="listing.summary">
										<td>{{ t('opencatalogi', 'Summary') }}</td>
										<td class="truncatedText">
											{{ listing.summary }}
										</td>
										<td>{{ 'Available' }}</td>
									</tr>
									<tr>
										<td>{{ t('opencatalogi', 'Publication Types') }}</td>
										<td>{{ listing.publicationTypes?.length || 0 }}</td>
										<td>{{ listing.publicationTypes?.length > 0 ? 'Configured' : 'Empty' }}</td>
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
									<th>{{ t('opencatalogi', 'Name') }}</th>
									<th>{{ t('opencatalogi', 'Organization') }}</th>
									<th>{{ t('opencatalogi', 'Publication Types') }}</th>
									<th class="tableColumnActions">
										{{ t('opencatalogi', 'Actions') }}
									</th>
								</tr>
							</thead>
							<tbody>
								<tr v-for="listing in paginatedListings"
									:key="listing.id"
									class="viewTableRow"
									:class="{ viewTableRowSelected: selectedListings.includes(listing.id) }">
									<td class="tableColumnCheckbox">
										<NcCheckboxRadioSwitch
											:checked="selectedListings.includes(listing.id)"
											@update:checked="(checked) => toggleListingSelection(listing.id, checked)" />
									</td>
									<td class="tableColumnTitle">
										<div class="titleContent">
											<strong>{{ listing.name || listing.title }}</strong>
											<span v-if="listing.summary" class="textDescription textEllipsis">{{ listing.summary }}</span>
										</div>
									</td>
									<td class="tableColumnConstrained">
										<span v-if="listing.organization">{{ listing.organization.title || listing.organization }}</span>
										<span v-else>-</span>
									</td>
									<td>{{ listing.publicationTypes?.length || 0 }}</td>
									<td class="tableColumnActions">
										<NcActions :primary="false">
											<template #icon>
												<DotsHorizontal :size="20" />
											</template>
											<NcActionButton close-after-click @click="objectStore.setActiveObject('listing', listing); navigationStore.setModal('viewDirectory')">
												<template #icon>
													<Eye :size="20" />
												</template>
												View
											</NcActionButton>
										</NcActions>
									</td>
								</tr>
							</tbody>
						</table>
					</div>
				</template>
			</div>

			<!-- Pagination -->
			<PaginationComponent
				:current-page="currentPagination.page || 1"
				:total-pages="currentPagination.pages || Math.ceil(filteredListings.length / (currentPagination.limit || 20))"
				:total-items="currentPagination.total || filteredListings.length"
				:current-page-size="currentPagination.limit || 20"
				:min-items-to-show="0"
				@page-changed="onPageChanged"
				@page-size-changed="onPageSizeChanged" />
		</div>
	</NcAppContent>
</template>

<script>
import { NcAppContent, NcEmptyContent, NcLoadingIcon, NcActions, NcActionButton, NcCheckboxRadioSwitch, NcButton } from '@nextcloud/vue'
import LayersOutline from 'vue-material-design-icons/LayersOutline.vue'
import DotsHorizontal from 'vue-material-design-icons/DotsHorizontal.vue'
import Refresh from 'vue-material-design-icons/Refresh.vue'
import Plus from 'vue-material-design-icons/Plus.vue'
import Eye from 'vue-material-design-icons/Eye.vue'
import HelpCircleOutline from 'vue-material-design-icons/HelpCircleOutline.vue'

import PaginationComponent from '../../components/PaginationComponent.vue'

export default {
	name: 'DirectoryIndex',
	components: {
		NcAppContent,
		NcEmptyContent,
		NcLoadingIcon,
		NcActions,
		NcActionButton,
		NcCheckboxRadioSwitch,
		NcButton,
		LayersOutline,
		DotsHorizontal,
		Refresh,
		Plus,
		Eye,
		HelpCircleOutline,
		PaginationComponent,
	},
	data() {
		return {
			selectedListings: [],
			viewMode: 'cards',
		}
	},
	computed: {
		filteredListings() {
			return objectStore.getCollection('listing')?.results || []
		},
		currentPagination() {
			const pagination = objectStore.getPagination('listing')
			console.info('Current pagination data:', pagination)
			return pagination
		},
		paginatedListings() {
			return this.filteredListings
		},
		allSelected() {
			return this.filteredListings.length > 0 && this.filteredListings.every(listing => this.selectedListings.includes(listing.id))
		},
		someSelected() {
			return this.selectedListings.length > 0 && !this.allSelected
		},
		emptyContentName() {
			if (objectStore.isLoading('listing')) {
				return t('opencatalogi', 'Loading directory listings...')
			} else if (!objectStore.getCollection('listing')?.results?.length) {
				return t('opencatalogi', 'No directory listings found')
			}
			return ''
		},
		emptyContentDescription() {
			if (objectStore.isLoading('listing')) {
				return t('opencatalogi', 'Please wait while we fetch directory listings.')
			} else if (!objectStore.getCollection('listing')?.results?.length) {
				return t('opencatalogi', 'No directory listings are available.')
			}
			return ''
		},
	},
	mounted() {
		console.info('DirectoryIndex mounted, fetching listings...')
		objectStore.fetchCollection('listing')
	},
	methods: {
		setViewMode(mode) {
			console.info('Setting view mode to:', mode)
			this.viewMode = mode
		},
		toggleSelectAll(checked) {
			if (checked) {
				this.selectedListings = this.filteredListings.map(listing => listing.id)
			} else {
				this.selectedListings = []
			}
		},
		toggleListingSelection(listingId, checked) {
			if (checked) {
				this.selectedListings.push(listingId)
			} else {
				this.selectedListings = this.selectedListings.filter(id => id !== listingId)
			}
		},
		onPageChanged(page) {
			console.info('Page changed to:', page)
			objectStore.fetchCollection('listing', { _page: page, _limit: this.currentPagination.limit || 20 })
		},
		onPageSizeChanged(pageSize) {
			console.info('Page size changed to:', pageSize)
			objectStore.fetchCollection('listing', { _page: 1, _limit: pageSize })
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
