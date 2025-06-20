/**
 * GlossaryIndex.vue
 * Component for displaying glossary items with cards and table view
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
					{{ t('opencatalogi', 'Glossary') }}
				</h1>
				<p>{{ t('opencatalogi', 'Manage your glossary terms and definitions') }}</p>
			</div>

			<!-- Actions Bar -->
			<div class="viewActionsBar">
				<div class="viewInfo">
					<span class="viewTotalCount">
						{{ t('opencatalogi', 'Showing {showing} of {total} terms', { showing: filteredTerms.length, total: currentPagination.total || filteredTerms.length }) }}
					</span>
					<span v-if="selectedTerms.length > 0" class="viewIndicator">
						({{ t('opencatalogi', '{count} selected', { count: selectedTerms.length }) }})
					</span>
				</div>
				<div class="viewActions">
					<div class="viewModeSwitchContainer">
						<NcCheckboxRadioSwitch
							v-tooltip="'See terms as cards'"
							:checked="viewMode === 'cards'"
							:button-variant="true"
							value="cards"
							name="terms_view_mode"
							type="radio"
							button-variant-grouped="horizontal"
							@update:checked="() => setViewMode('cards')">
							Cards
						</NcCheckboxRadioSwitch>
						<NcCheckboxRadioSwitch
							v-tooltip="'See terms as a table'"
							:checked="viewMode === 'table'"
							:button-variant="true"
							value="table"
							name="terms_view_mode"
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
							@click="objectStore.clearActiveObject('glossary'); navigationStore.setModal('glossary')">
							<template #icon>
								<Plus :size="20" />
							</template>
							Add Term
						</NcActionButton>
						<NcActionButton
							close-after-click
							:disabled="objectStore.isLoading('glossary')"
							@click="objectStore.fetchCollection('glossary')">
							<template #icon>
								<Refresh :size="20" />
							</template>
							Refresh
						</NcActionButton>
						<NcActionButton
							title="View documentation about glossary"
							@click="openLink('https://conduction.gitbook.io/opencatalogi-nextcloud/beheerders/glossaries', '_blank')">
							<template #icon>
								<HelpCircleOutline :size="20" />
							</template>
							Help
						</NcActionButton>
					</NcActions>
				</div>
			</div>

			<!-- Loading, Error, and Empty States -->
			<NcEmptyContent v-if="objectStore.isLoading('glossary') || !filteredTerms.length"
				:name="emptyContentName"
				:description="emptyContentDescription">
				<template #icon>
					<NcLoadingIcon v-if="objectStore.isLoading('glossary')" :size="64" />
					<BookOpenOutline v-else :size="64" />
				</template>
				<template v-if="!objectStore.isLoading('glossary') && !objectStore.getCollection('glossary')?.results?.length" #action>
					<NcButton type="primary" @click="objectStore.clearActiveObject('glossary'); navigationStore.setModal('glossary')">
						{{ t('opencatalogi', 'Add term') }}
					</NcButton>
				</template>
			</NcEmptyContent>

			<!-- Content -->
			<div v-else>
				<template v-if="viewMode === 'cards'">
					<div class="cardGrid">
						<div v-for="term in paginatedTerms" :key="term.id" class="card">
							<div class="cardHeader">
								<h2 v-tooltip.bottom="term.summary">
									<BookOpenOutline :size="20" />
									{{ term.title }}
								</h2>
								<NcActions :primary="true" menu-name="Actions">
									<template #icon>
										<DotsHorizontal :size="20" />
									</template>
									<NcActionButton close-after-click @click="objectStore.setActiveObject('glossary', term); navigationStore.setModal('viewGlossary')">
										<template #icon>
											<Eye :size="20" />
										</template>
										View
									</NcActionButton>
									<NcActionButton close-after-click @click="objectStore.setActiveObject('glossary', term); navigationStore.setModal('glossary')">
										<template #icon>
											<Pencil :size="20" />
										</template>
										Edit
									</NcActionButton>
									<NcActionButton close-after-click @click="objectStore.setActiveObject('glossary', term); navigationStore.setDialog('copyObject', { objectType: 'glossary', dialogTitle: 'Term' })">
										<template #icon>
											<ContentCopy :size="20" />
										</template>
										Copy
									</NcActionButton>
									<NcActionButton close-after-click @click="objectStore.setActiveObject('glossary', term); navigationStore.setDialog('deleteObject', { objectType: 'glossary', dialogTitle: 'Term' })">
										<template #icon>
											<TrashCanOutline :size="20" />
										</template>
										Delete
									</NcActionButton>
								</NcActions>
							</div>
							<!-- Term Statistics Table -->
							<table class="statisticsTable termStats">
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
										<td>{{ term.published ? 'Public' : 'Private' }}</td>
										<td>{{ term.published ? 'Published' : 'Draft' }}</td>
									</tr>
									<tr v-if="term.summary">
										<td>{{ t('opencatalogi', 'Summary') }}</td>
										<td class="truncatedText">
											{{ term.summary }}
										</td>
										<td>{{ 'Available' }}</td>
									</tr>
									<tr v-if="term.description">
										<td>{{ t('opencatalogi', 'Description') }}</td>
										<td class="truncatedText">
											{{ term.description }}
										</td>
										<td>{{ 'Available' }}</td>
									</tr>
									<tr>
										<td>{{ t('opencatalogi', 'Related Terms') }}</td>
										<td>{{ term.relatedTerms?.length || 0 }}</td>
										<td>{{ term.relatedTerms?.length > 0 ? 'Linked' : 'None' }}</td>
									</tr>
									<tr v-if="term.keywords?.length">
										<td>{{ t('opencatalogi', 'Keywords') }}</td>
										<td>{{ term.keywords.join(', ') }}</td>
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
									<th>{{ t('opencatalogi', 'Related Terms') }}</th>
									<th>{{ t('opencatalogi', 'Keywords') }}</th>
									<th class="tableColumnActions">
										{{ t('opencatalogi', 'Actions') }}
									</th>
								</tr>
							</thead>
							<tbody>
								<tr v-for="term in paginatedTerms"
									:key="term.id"
									class="viewTableRow"
									:class="{ viewTableRowSelected: selectedTerms.includes(term.id) }">
									<td class="tableColumnCheckbox">
										<NcCheckboxRadioSwitch
											:checked="selectedTerms.includes(term.id)"
											@update:checked="(checked) => toggleTermSelection(term.id, checked)" />
									</td>
									<td class="tableColumnTitle">
										<div class="titleContent">
											<strong>{{ term.title }}</strong>
											<span v-if="term.summary" class="textDescription textEllipsis">{{ term.summary }}</span>
										</div>
									</td>
									<td>{{ term.published ? 'Public' : 'Private' }}</td>
									<td>{{ term.relatedTerms?.length || 0 }}</td>
									<td class="tableColumnConstrained">
										<span v-if="term.keywords?.length">{{ term.keywords.join(', ') }}</span>
										<span v-else>-</span>
									</td>
									<td class="tableColumnActions">
										<NcActions :primary="false">
											<template #icon>
												<DotsHorizontal :size="20" />
											</template>
											<NcActionButton close-after-click @click="objectStore.setActiveObject('glossary', term); navigationStore.setModal('viewGlossary')">
												<template #icon>
													<Eye :size="20" />
												</template>
												View
											</NcActionButton>
											<NcActionButton close-after-click @click="objectStore.setActiveObject('glossary', term); navigationStore.setModal('glossary')">
												<template #icon>
													<Pencil :size="20" />
												</template>
												Edit
											</NcActionButton>
											<NcActionButton close-after-click @click="objectStore.setActiveObject('glossary', term); navigationStore.setDialog('copyObject', { objectType: 'glossary', dialogTitle: 'Term' })">
												<template #icon>
													<ContentCopy :size="20" />
												</template>
												Copy
											</NcActionButton>
											<NcActionButton close-after-click @click="objectStore.setActiveObject('glossary', term); navigationStore.setDialog('deleteObject', { objectType: 'glossary', dialogTitle: 'Term' })">
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
				:total-pages="currentPagination.pages || Math.ceil(filteredTerms.length / (currentPagination.limit || 20))"
				:total-items="currentPagination.total || filteredTerms.length"
				:current-page-size="currentPagination.limit || 20"
				:min-items-to-show="0"
				@page-changed="onPageChanged"
				@page-size-changed="onPageSizeChanged" />
		</div>
	</NcAppContent>
</template>

<script>
import { NcAppContent, NcEmptyContent, NcLoadingIcon, NcActions, NcActionButton, NcCheckboxRadioSwitch, NcButton } from '@nextcloud/vue'
import BookOpenOutline from 'vue-material-design-icons/BookOpenOutline.vue'
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
	name: 'GlossaryIndex',
	components: {
		NcAppContent,
		NcEmptyContent,
		NcLoadingIcon,
		NcActions,
		NcActionButton,
		NcCheckboxRadioSwitch,
		NcButton,
		BookOpenOutline,
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
			selectedTerms: [],
			viewMode: 'cards',
		}
	},
	computed: {
		filteredTerms() {
			return objectStore.getCollection('glossary')?.results || []
		},
		currentPagination() {
			const pagination = objectStore.getPagination('glossary')
			console.info('Current pagination data:', pagination)
			return pagination
		},
		paginatedTerms() {
			return this.filteredTerms
		},
		allSelected() {
			return this.filteredTerms.length > 0 && this.filteredTerms.every(term => this.selectedTerms.includes(term.id))
		},
		someSelected() {
			return this.selectedTerms.length > 0 && !this.allSelected
		},
		emptyContentName() {
			if (objectStore.isLoading('glossary')) {
				return t('opencatalogi', 'Loading glossary terms...')
			} else if (!objectStore.getCollection('glossary')?.results?.length) {
				return t('opencatalogi', 'No glossary terms found')
			}
			return ''
		},
		emptyContentDescription() {
			if (objectStore.isLoading('glossary')) {
				return t('opencatalogi', 'Please wait while we fetch your glossary terms.')
			} else if (!objectStore.getCollection('glossary')?.results?.length) {
				return t('opencatalogi', 'No glossary terms are available.')
			}
			return ''
		},
	},
	mounted() {
		console.info('GlossaryIndex mounted, fetching terms...')
		objectStore.fetchCollection('glossary')
	},
	methods: {
		setViewMode(mode) {
			console.info('Setting view mode to:', mode)
			this.viewMode = mode
		},
		toggleSelectAll(checked) {
			if (checked) {
				this.selectedTerms = this.filteredTerms.map(term => term.id)
			} else {
				this.selectedTerms = []
			}
		},
		toggleTermSelection(termId, checked) {
			if (checked) {
				this.selectedTerms.push(termId)
			} else {
				this.selectedTerms = this.selectedTerms.filter(id => id !== termId)
			}
		},
		onPageChanged(page) {
			console.info('Page changed to:', page)
			objectStore.fetchCollection('glossary', { _page: page, _limit: this.currentPagination.limit || 20 })
		},
		onPageSizeChanged(pageSize) {
			console.info('Page size changed to:', pageSize)
			objectStore.fetchCollection('glossary', { _page: 1, _limit: pageSize })
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
