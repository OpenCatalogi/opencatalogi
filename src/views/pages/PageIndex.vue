/**
 * PageIndex.vue
 * Component for displaying pages with cards and table view
 * @category Views
 * @package opencatalogi
 * @author Ruben Linde
 * @copyright 2024
 * @license AGPL-3.0-or-later
 * @version 1.0.0
 * @link https://github.com/opencatalogi/opencatalogi
 */

<script setup>
import { objectStore, navigationStore } from '../../store/store.js'
</script>

<template>
	<NcAppContent>
		<div class="viewContainer">
			<!-- Header -->
			<div class="viewHeader">
				<h1 class="viewHeaderTitleIndented">
					{{ t('opencatalogi', 'Pages') }}
				</h1>
				<p>{{ t('opencatalogi', 'Manage your content pages and their components') }}</p>
			</div>

			<!-- Actions Bar -->
			<div class="viewActionsBar">
				<div class="viewInfo">
					<span class="viewTotalCount">
						{{ t('opencatalogi', 'Showing {showing} of {total} pages', { showing: filteredPages.length, total: currentPagination.total || filteredPages.length }) }}
					</span>
					<span v-if="selectedPages.length > 0" class="viewIndicator">
						({{ t('opencatalogi', '{count} selected', { count: selectedPages.length }) }})
					</span>
				</div>
				<div class="viewActions">
					<div class="viewModeSwitchContainer">
						<NcCheckboxRadioSwitch
							v-tooltip="'See pages as cards'"
							:checked="viewMode === 'cards'"
							:button-variant="true"
							value="cards"
							name="pages_view_mode"
							type="radio"
							button-variant-grouped="horizontal"
							@update:checked="() => setViewMode('cards')">
							Cards
						</NcCheckboxRadioSwitch>
						<NcCheckboxRadioSwitch
							v-tooltip="'See pages as a table'"
							:checked="viewMode === 'table'"
							:button-variant="true"
							value="table"
							name="pages_view_mode"
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
							@click="objectStore.clearActiveObject('page'); navigationStore.setModal('page')">
							<template #icon>
								<Plus :size="20" />
							</template>
							Add Page
						</NcActionButton>
						<NcActionButton
							close-after-click
							:disabled="objectStore.isLoading('page')"
							@click="objectStore.fetchCollection('page')">
							<template #icon>
								<Refresh :size="20" />
							</template>
							Refresh
						</NcActionButton>
						<NcActionButton
							title="View documentation about pages"
							@click="openLink('https://conduction.gitbook.io/opencatalogi-nextcloud/beheerders/pages', '_blank')">
							<template #icon>
								<HelpCircleOutline :size="20" />
							</template>
							Help
						</NcActionButton>
					</NcActions>
				</div>
			</div>

			<!-- Loading, Error, and Empty States -->
			<NcEmptyContent v-if="objectStore.isLoading('page') || !filteredPages.length"
				:name="emptyContentName"
				:description="emptyContentDescription">
				<template #icon>
					<NcLoadingIcon v-if="objectStore.isLoading('page')" :size="64" />
					<Web v-else :size="64" />
				</template>
				<template v-if="!objectStore.isLoading('page') && !objectStore.getCollection('page')?.results?.length" #action>
					<NcButton type="primary" @click="objectStore.clearActiveObject('page'); navigationStore.setModal('page')">
						{{ t('opencatalogi', 'Add page') }}
					</NcButton>
				</template>
			</NcEmptyContent>

			<!-- Content -->
			<div v-else>
				<template v-if="viewMode === 'cards'">
					<div class="cardGrid">
						<div v-for="page in paginatedPages" :key="page.id" class="card">
							<div class="cardHeader">
								<h2 v-tooltip.bottom="page.slug">
									<Web :size="20" />
									{{ page.title }}
								</h2>
								<NcActions :primary="true" menu-name="Actions">
									<template #icon>
										<DotsHorizontal :size="20" />
									</template>
									<NcActionButton close-after-click @click="objectStore.setActiveObject('page', page); navigationStore.setModal('viewPage')">
										<template #icon>
											<Eye :size="20" />
										</template>
										View
									</NcActionButton>
									<NcActionButton close-after-click @click="objectStore.setActiveObject('page', page); navigationStore.setModal('page')">
										<template #icon>
											<Pencil :size="20" />
										</template>
										Edit
									</NcActionButton>
									<NcActionButton close-after-click @click="objectStore.setActiveObject('page', page); navigationStore.setModal('pageContentForm')">
										<template #icon>
											<Plus :size="20" />
										</template>
										Add Content
									</NcActionButton>
									<NcActionButton close-after-click @click="objectStore.setActiveObject('page', page); navigationStore.setDialog('copyObject', { objectType: 'page', dialogTitle: 'Pagina' })">
										<template #icon>
											<ContentCopy :size="20" />
										</template>
										Copy
									</NcActionButton>
									<NcActionButton close-after-click @click="objectStore.setActiveObject('page', page); navigationStore.setDialog('deleteObject', { objectType: 'page', dialogTitle: 'Pagina' })">
										<template #icon>
											<TrashCanOutline :size="20" />
										</template>
										Delete
									</NcActionButton>
								</NcActions>
							</div>
							<!-- Page Statistics Table -->
							<table class="statisticsTable pageStats">
								<thead>
									<tr>
										<th>{{ t('opencatalogi', 'Property') }}</th>
										<th>{{ t('opencatalogi', 'Value') }}</th>
										<th>{{ t('opencatalogi', 'Status') }}</th>
									</tr>
								</thead>
								<tbody>
									<tr v-if="page.slug">
										<td>{{ t('opencatalogi', 'Slug') }}</td>
										<td>{{ page.slug }}</td>
										<td>{{ 'Available' }}</td>
									</tr>
									<tr v-if="page.description">
										<td>{{ t('opencatalogi', 'Description') }}</td>
										<td class="truncatedText">
											{{ page.description }}
										</td>
										<td>{{ 'Available' }}</td>
									</tr>
									<tr>
										<td>{{ t('opencatalogi', 'Content Items') }}</td>
										<td>{{ page.contents?.length || 0 }}</td>
										<td>{{ page.contents?.length > 0 ? 'Configured' : 'Empty' }}</td>
									</tr>
									<tr v-if="page.updatedAt">
										<td>{{ t('opencatalogi', 'Last Updated') }}</td>
										<td>{{ new Date(page.updatedAt).toLocaleDateString() }}</td>
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
									<th>{{ t('opencatalogi', 'Slug') }}</th>
									<th>{{ t('opencatalogi', 'Content Items') }}</th>
									<th>{{ t('opencatalogi', 'Last Updated') }}</th>
									<th class="tableColumnActions">
										{{ t('opencatalogi', 'Actions') }}
									</th>
								</tr>
							</thead>
							<tbody>
								<tr v-for="page in paginatedPages"
									:key="page.id"
									class="viewTableRow"
									:class="{ viewTableRowSelected: selectedPages.includes(page.id) }">
									<td class="tableColumnCheckbox">
										<NcCheckboxRadioSwitch
											:checked="selectedPages.includes(page.id)"
											@update:checked="(checked) => togglePageSelection(page.id, checked)" />
									</td>
									<td class="tableColumnTitle">
										<div class="titleContent">
											<strong>{{ page.title }}</strong>
											<span v-if="page.description" class="textDescription textEllipsis">{{ page.description }}</span>
										</div>
									</td>
									<td>{{ page.slug || '-' }}</td>
									<td>{{ page.contents?.length || 0 }}</td>
									<td class="tableColumnConstrained">
										<span v-if="page.updatedAt">{{ new Date(page.updatedAt).toLocaleDateString() }}</span>
										<span v-else>-</span>
									</td>
									<td class="tableColumnActions">
										<NcActions :primary="false">
											<template #icon>
												<DotsHorizontal :size="20" />
											</template>
											<NcActionButton close-after-click @click="objectStore.setActiveObject('page', page); navigationStore.setModal('viewPage')">
												<template #icon>
													<Eye :size="20" />
												</template>
												View
											</NcActionButton>
											<NcActionButton close-after-click @click="objectStore.setActiveObject('page', page); navigationStore.setModal('page')">
												<template #icon>
													<Pencil :size="20" />
												</template>
												Edit
											</NcActionButton>
											<NcActionButton close-after-click @click="objectStore.setActiveObject('page', page); navigationStore.setModal('pageContentForm')">
												<template #icon>
													<Plus :size="20" />
												</template>
												Add Content
											</NcActionButton>
											<NcActionButton close-after-click @click="objectStore.setActiveObject('page', page); navigationStore.setDialog('copyObject', { objectType: 'page', dialogTitle: 'Pagina' })">
												<template #icon>
													<ContentCopy :size="20" />
												</template>
												Copy
											</NcActionButton>
											<NcActionButton close-after-click @click="objectStore.setActiveObject('page', page); navigationStore.setDialog('deleteObject', { objectType: 'page', dialogTitle: 'Pagina' })">
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
				:total-pages="currentPagination.pages || Math.ceil(filteredPages.length / (currentPagination.limit || 20))"
				:total-items="currentPagination.total || filteredPages.length"
				:current-page-size="currentPagination.limit || 20"
				:min-items-to-show="0"
				@page-changed="onPageChanged"
				@page-size-changed="onPageSizeChanged" />
		</div>
	</NcAppContent>
</template>

<script>
import { NcAppContent, NcEmptyContent, NcLoadingIcon, NcActions, NcActionButton, NcCheckboxRadioSwitch, NcButton } from '@nextcloud/vue'
import Web from 'vue-material-design-icons/Web.vue'
import DotsHorizontal from 'vue-material-design-icons/DotsHorizontal.vue'
import Pencil from 'vue-material-design-icons/Pencil.vue'
import TrashCanOutline from 'vue-material-design-icons/TrashCanOutline.vue'
import Refresh from 'vue-material-design-icons/Refresh.vue'
import Plus from 'vue-material-design-icons/Plus.vue'
import Eye from 'vue-material-design-icons/Eye.vue'
import ContentCopy from 'vue-material-design-icons/ContentCopy.vue'
import HelpCircleOutline from 'vue-material-design-icons/HelpCircleOutline.vue'

import PaginationComponent from '../../components/PaginationComponent.vue'

export default {
	name: 'PageIndex',
	components: {
		NcAppContent,
		NcEmptyContent,
		NcLoadingIcon,
		NcActions,
		NcActionButton,
		NcCheckboxRadioSwitch,
		NcButton,
		Web,
		DotsHorizontal,
		Pencil,
		TrashCanOutline,
		Refresh,
		Plus,
		Eye,
		ContentCopy,
		HelpCircleOutline,
		PaginationComponent,
	},
	data() {
		return {
			selectedPages: [],
			viewMode: 'cards',
		}
	},
	computed: {
		filteredPages() {
			return objectStore.getCollection('page')?.results || []
		},
		currentPagination() {
			const pagination = objectStore.getPagination('page')
			console.info('Current pagination data:', pagination)
			return pagination
		},
		paginatedPages() {
			return this.filteredPages
		},
		allSelected() {
			return this.filteredPages.length > 0 && this.filteredPages.every(page => this.selectedPages.includes(page.id))
		},
		someSelected() {
			return this.selectedPages.length > 0 && !this.allSelected
		},
		emptyContentName() {
			if (objectStore.isLoading('page')) {
				return t('opencatalogi', 'Loading pages...')
			} else if (!objectStore.getCollection('page')?.results?.length) {
				return t('opencatalogi', 'No pages found')
			}
			return ''
		},
		emptyContentDescription() {
			if (objectStore.isLoading('page')) {
				return t('opencatalogi', 'Please wait while we fetch your pages.')
			} else if (!objectStore.getCollection('page')?.results?.length) {
				return t('opencatalogi', 'No pages are available.')
			}
			return ''
		},
	},
	mounted() {
		console.info('PageIndex mounted, fetching pages...')
		objectStore.fetchCollection('page')
	},
	methods: {
		setViewMode(mode) {
			console.info('Setting view mode to:', mode)
			this.viewMode = mode
		},
		toggleSelectAll(checked) {
			if (checked) {
				this.selectedPages = this.filteredPages.map(page => page.id)
			} else {
				this.selectedPages = []
			}
		},
		togglePageSelection(pageId, checked) {
			if (checked) {
				this.selectedPages.push(pageId)
			} else {
				this.selectedPages = this.selectedPages.filter(id => id !== pageId)
			}
		},
		onPageChanged(page) {
			console.info('Page changed to:', page)
			objectStore.fetchCollection('page', { _page: page, _limit: this.currentPagination.limit || 20 })
		},
		onPageSizeChanged(pageSize) {
			console.info('Page size changed to:', pageSize)
			objectStore.fetchCollection('page', { _page: 1, _limit: pageSize })
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
