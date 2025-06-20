<script setup>
import { objectStore, navigationStore } from '../../store/store.js'
</script>

<template>
	<NcAppContent>
		<div class="viewContainer">
			<!-- Header -->
			<div class="viewHeader">
				<h1 class="viewHeaderTitleIndented">
					{{ t('opencatalogi', 'Catalogs') }}
				</h1>
				<p>{{ t('opencatalogi', 'Manage your data catalogs and their configurations') }}</p>
			</div>

			<!-- Actions Bar -->
			<div class="viewActionsBar">
				<div class="viewInfo">
					<span class="viewTotalCount">
						{{ t('opencatalogi', 'Showing {showing} of {total} catalogs', { showing: filteredCatalogs.length, total: currentPagination.total || filteredCatalogs.length }) }}
					</span>
					<span v-if="selectedCatalogs.length > 0" class="viewIndicator">
						({{ t('opencatalogi', '{count} selected', { count: selectedCatalogs.length }) }})
					</span>
				</div>
				<div class="viewActions">
					<div class="viewModeSwitchContainer">
						<NcCheckboxRadioSwitch
							v-tooltip="'See catalogs as cards'"
							:checked="viewMode === 'cards'"
							:button-variant="true"
							value="cards"
							name="catalogs_view_mode"
							type="radio"
							button-variant-grouped="horizontal"
							@update:checked="() => setViewMode('cards')">
							Cards
						</NcCheckboxRadioSwitch>
						<NcCheckboxRadioSwitch
							v-tooltip="'See catalogs as a table'"
							:checked="viewMode === 'table'"
							:button-variant="true"
							value="table"
							name="catalogs_view_mode"
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
							@click="objectStore.clearActiveObject('catalog'); navigationStore.setModal('catalog')">
							<template #icon>
								<Plus :size="20" />
							</template>
							Add Catalog
						</NcActionButton>
						<NcActionButton
							close-after-click
							:disabled="objectStore.isLoading('catalog')"
							@click="objectStore.fetchCollection('catalog')">
							<template #icon>
								<Refresh :size="20" />
							</template>
							Refresh
						</NcActionButton>
						<NcActionButton
							title="View documentation about catalogs"
							@click="openLink('https://conduction.gitbook.io/opencatalogi-nextcloud/beheerders/catalogi', '_blank')">
							<template #icon>
								<HelpCircleOutline :size="20" />
							</template>
							Help
						</NcActionButton>
					</NcActions>
				</div>
			</div>

			<!-- Loading, Error, and Empty States -->
			<NcEmptyContent v-if="objectStore.isLoading('catalog') || !filteredCatalogs.length"
				:name="emptyContentName"
				:description="emptyContentDescription">
				<template #icon>
					<NcLoadingIcon v-if="objectStore.isLoading('catalog')" :size="64" />
					<DatabaseOutline v-else :size="64" />
				</template>
				<template v-if="!objectStore.isLoading('catalog') && !objectStore.getCollection('catalog')?.results?.length" #action>
					<NcButton type="primary" @click="objectStore.clearActiveObject('catalog'); navigationStore.setModal('catalog')">
						{{ t('opencatalogi', 'Add catalog') }}
					</NcButton>
				</template>
			</NcEmptyContent>

			<!-- Content -->
			<div v-else>
				<template v-if="viewMode === 'cards'">
					<div class="cardGrid">
						<div v-for="catalog in paginatedCatalogs" :key="catalog.id" class="card">
							<div class="cardHeader">
								<h2 v-tooltip.bottom="catalog.summary">
									<DatabaseOutline :size="20" />
									{{ catalog.title }}
								</h2>
								<NcActions :primary="true" menu-name="Actions">
									<template #icon>
										<DotsHorizontal :size="20" />
									</template>
									<NcActionButton close-after-click @click="objectStore.setActiveObject('catalog', catalog); navigationStore.setModal('viewCatalogi')">
										<template #icon>
											<Eye :size="20" />
										</template>
										View
									</NcActionButton>
									<NcActionButton close-after-click @click="objectStore.setActiveObject('catalog', catalog); navigationStore.setModal('catalog')">
										<template #icon>
											<Pencil :size="20" />
										</template>
										Edit
									</NcActionButton>
									<NcActionButton close-after-click @click="navigationStore.setSelected('publication'); navigationStore.setSelectedCatalogus(catalog?.id)">
										<template #icon>
											<OpenInApp :size="20" />
										</template>
										View Catalog
									</NcActionButton>
									<NcActionButton close-after-click @click="objectStore.setActiveObject('catalog', catalog); navigationStore.setDialog('copyObject', { objectType: 'catalog', dialogTitle: 'Catalogus' })">
										<template #icon>
											<ContentCopy :size="20" />
										</template>
										Copy
									</NcActionButton>
									<NcActionButton close-after-click @click="objectStore.setActiveObject('catalog', catalog); navigationStore.setDialog('deleteObject', { objectType: 'catalog', dialogTitle: 'Catalogus' })">
										<template #icon>
											<TrashCanOutline :size="20" />
										</template>
										Delete
									</NcActionButton>
								</NcActions>
							</div>
							<!-- Catalog Statistics Table -->
							<table class="statisticsTable catalogStats">
								<thead>
									<tr>
										<th>{{ t('opencatalogi', 'Property') }}</th>
										<th>{{ t('opencatalogi', 'Value') }}</th>
										<th>{{ t('opencatalogi', 'Status') }}</th>
									</tr>
								</thead>
								<tbody>
									<tr>
										<td>{{ t('opencatalogi', 'Status') }}</td>
										<td>{{ catalog.listed ? 'Public' : 'Private' }}</td>
										<td>{{ catalog.listed ? 'Listed' : 'Not Listed' }}</td>
									</tr>
									<tr v-if="catalog.summary">
										<td>{{ t('opencatalogi', 'Summary') }}</td>
										<td class="truncatedText">
											{{ catalog.summary }}
										</td>
										<td>{{ 'Available' }}</td>
									</tr>
									<tr v-if="catalog.description">
										<td>{{ t('opencatalogi', 'Description') }}</td>
										<td class="truncatedText">
											{{ catalog.description }}
										</td>
										<td>{{ 'Available' }}</td>
									</tr>
									<tr>
										<td>{{ t('opencatalogi', 'Registers') }}</td>
										<td>{{ catalog.registers?.length || 0 }}</td>
										<td>{{ catalog.registers?.length > 0 ? 'Configured' : 'Empty' }}</td>
									</tr>
									<tr>
										<td>{{ t('opencatalogi', 'Schemas') }}</td>
										<td>{{ catalog.schemas?.length || 0 }}</td>
										<td>{{ catalog.schemas?.length > 0 ? 'Configured' : 'Empty' }}</td>
									</tr>
									<tr v-if="catalog.organization">
										<td>{{ t('opencatalogi', 'Organization') }}</td>
										<td>{{ getOrganizationName(catalog.organization) }}</td>
										<td>{{ 'Linked' }}</td>
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
									<th>{{ t('opencatalogi', 'Registers') }}</th>
									<th>{{ t('opencatalogi', 'Schemas') }}</th>
									<th>{{ t('opencatalogi', 'Organization') }}</th>
									<th class="tableColumnActions">
										{{ t('opencatalogi', 'Actions') }}
									</th>
								</tr>
							</thead>
							<tbody>
								<tr v-for="catalog in paginatedCatalogs"
									:key="catalog.id"
									class="viewTableRow"
									:class="{ viewTableRowSelected: selectedCatalogs.includes(catalog.id) }">
									<td class="tableColumnCheckbox">
										<NcCheckboxRadioSwitch
											:checked="selectedCatalogs.includes(catalog.id)"
											@update:checked="(checked) => toggleCatalogSelection(catalog.id, checked)" />
									</td>
									<td class="tableColumnTitle">
										<div class="titleContent">
											<strong>{{ catalog.title }}</strong>
											<span v-if="catalog.summary" class="textDescription textEllipsis">{{ catalog.summary }}</span>
										</div>
									</td>
									<td>{{ catalog.listed ? 'Public' : 'Private' }}</td>
									<td>{{ catalog.registers?.length || 0 }}</td>
									<td>{{ catalog.schemas?.length || 0 }}</td>
									<td class="tableColumnConstrained">
										<span v-if="catalog.organization">{{ getOrganizationName(catalog.organization) }}</span>
										<span v-else>-</span>
									</td>
									<td class="tableColumnActions">
										<NcActions :primary="false">
											<template #icon>
												<DotsHorizontal :size="20" />
											</template>
											<NcActionButton close-after-click @click="objectStore.setActiveObject('catalog', catalog); navigationStore.setModal('viewCatalogi')">
												<template #icon>
													<Eye :size="20" />
												</template>
												View
											</NcActionButton>
											<NcActionButton close-after-click @click="objectStore.setActiveObject('catalog', catalog); navigationStore.setModal('catalog')">
												<template #icon>
													<Pencil :size="20" />
												</template>
												Edit
											</NcActionButton>
											<NcActionButton close-after-click @click="navigationStore.setSelected('publication'); navigationStore.setSelectedCatalogus(catalog?.id)">
												<template #icon>
													<OpenInApp :size="20" />
												</template>
												View Catalog
											</NcActionButton>
											<NcActionButton close-after-click @click="objectStore.setActiveObject('catalog', catalog); navigationStore.setDialog('copyObject', { objectType: 'catalog', dialogTitle: 'Catalogus' })">
												<template #icon>
													<ContentCopy :size="20" />
												</template>
												Copy
											</NcActionButton>
											<NcActionButton close-after-click @click="objectStore.setActiveObject('catalog', catalog); navigationStore.setDialog('deleteObject', { objectType: 'catalog', dialogTitle: 'Catalogus' })">
												<template #icon>
													<TrashCanOutline :size="20" />
												</template>
												Delete
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
				:total-pages="currentPagination.pages || Math.ceil(filteredCatalogs.length / (currentPagination.limit || 20))"
				:total-items="currentPagination.total || filteredCatalogs.length"
				:current-page-size="currentPagination.limit || 20"
				:min-items-to-show="0"
				@page-changed="onPageChanged"
				@page-size-changed="onPageSizeChanged" />
		</div>
	</NcAppContent>
</template>

<script>
import { NcAppContent, NcEmptyContent, NcLoadingIcon, NcActions, NcActionButton, NcCheckboxRadioSwitch, NcButton } from '@nextcloud/vue'
import DatabaseOutline from 'vue-material-design-icons/DatabaseOutline.vue'
import DotsHorizontal from 'vue-material-design-icons/DotsHorizontal.vue'
import Pencil from 'vue-material-design-icons/Pencil.vue'
import TrashCanOutline from 'vue-material-design-icons/TrashCanOutline.vue'
import Refresh from 'vue-material-design-icons/Refresh.vue'
import Plus from 'vue-material-design-icons/Plus.vue'
import Eye from 'vue-material-design-icons/Eye.vue'
import OpenInApp from 'vue-material-design-icons/OpenInApp.vue'
import ContentCopy from 'vue-material-design-icons/ContentCopy.vue'
import HelpCircleOutline from 'vue-material-design-icons/HelpCircleOutline.vue'

import PaginationComponent from '../../components/PaginationComponent.vue'

export default {
	name: 'CatalogiIndex',
	components: {
		NcAppContent,
		NcEmptyContent,
		NcLoadingIcon,
		NcActions,
		NcActionButton,
		NcCheckboxRadioSwitch,
		NcButton,
		DatabaseOutline,
		DotsHorizontal,
		Pencil,
		TrashCanOutline,
		Refresh,
		Plus,
		Eye,
		OpenInApp,
		ContentCopy,
		HelpCircleOutline,
		PaginationComponent,
	},
	data() {
		return {
			selectedCatalogs: [],
			viewMode: 'cards',
		}
	},
	computed: {
		filteredCatalogs() {
			return objectStore.getCollection('catalog')?.results || []
		},
		currentPagination() {
			const pagination = objectStore.getPagination('catalog')
			console.info('Current pagination data:', pagination)
			return pagination
		},
		paginatedCatalogs() {
			return this.filteredCatalogs
		},
		allSelected() {
			return this.filteredCatalogs.length > 0 && this.filteredCatalogs.every(catalog => this.selectedCatalogs.includes(catalog.id))
		},
		someSelected() {
			return this.selectedCatalogs.length > 0 && !this.allSelected
		},
		emptyContentName() {
			if (objectStore.isLoading('catalog')) {
				return t('opencatalogi', 'Loading catalogs...')
			} else if (!objectStore.getCollection('catalog')?.results?.length) {
				return t('opencatalogi', 'No catalogs found')
			}
			return ''
		},
		emptyContentDescription() {
			if (objectStore.isLoading('catalog')) {
				return t('opencatalogi', 'Please wait while we fetch your catalogs.')
			} else if (!objectStore.getCollection('catalog')?.results?.length) {
				return t('opencatalogi', 'No catalogs are available.')
			}
			return ''
		},
	},
	mounted() {
		console.info('CatalogiIndex mounted, fetching catalogs...')
		objectStore.fetchCollection('catalog')
	},
	methods: {
		setViewMode(mode) {
			console.info('Setting view mode to:', mode)
			this.viewMode = mode
		},
		toggleSelectAll(checked) {
			if (checked) {
				this.selectedCatalogs = this.filteredCatalogs.map(catalog => catalog.id)
			} else {
				this.selectedCatalogs = []
			}
		},
		toggleCatalogSelection(catalogId, checked) {
			if (checked) {
				this.selectedCatalogs.push(catalogId)
			} else {
				this.selectedCatalogs = this.selectedCatalogs.filter(id => id !== catalogId)
			}
		},
		onPageChanged(page) {
			console.info('Page changed to:', page)
			objectStore.fetchCollection('catalog', { _page: page, _limit: this.currentPagination.limit || 20 })
		},
		onPageSizeChanged(pageSize) {
			console.info('Page size changed to:', pageSize)
			objectStore.fetchCollection('catalog', { _page: 1, _limit: pageSize })
		},
		getOrganizationName(organizationId) {
			const organization = objectStore.getObject('organization', organizationId)
			return organization?.name || 'Unknown Organization'
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
