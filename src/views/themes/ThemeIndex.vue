/**
 * ThemeIndex.vue
 * Component for displaying themes with cards and table view
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
					{{ t('opencatalogi', 'Themes') }}
				</h1>
				<p>{{ t('opencatalogi', 'Manage your website themes and visual styling') }}</p>
			</div>

			<!-- Actions Bar -->
			<div class="viewActionsBar">
				<div class="viewInfo">
					<span class="viewTotalCount">
						{{ t('opencatalogi', 'Showing {showing} of {total} themes', { showing: filteredThemes.length, total: currentPagination.total || filteredThemes.length }) }}
					</span>
					<span v-if="selectedThemes.length > 0" class="viewIndicator">
						({{ t('opencatalogi', '{count} selected', { count: selectedThemes.length }) }})
					</span>
				</div>
				<div class="viewActions">
					<div class="viewModeSwitchContainer">
						<NcCheckboxRadioSwitch
							v-tooltip="'See themes as cards'"
							:checked="viewMode === 'cards'"
							:button-variant="true"
							value="cards"
							name="themes_view_mode"
							type="radio"
							button-variant-grouped="horizontal"
							@update:checked="() => setViewMode('cards')">
							Cards
						</NcCheckboxRadioSwitch>
						<NcCheckboxRadioSwitch
							v-tooltip="'See themes as a table'"
							:checked="viewMode === 'table'"
							:button-variant="true"
							value="table"
							name="themes_view_mode"
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
							@click="objectStore.clearActiveObject('theme'); navigationStore.setModal('theme')">
							<template #icon>
								<Plus :size="20" />
							</template>
							Add Theme
						</NcActionButton>
						<NcActionButton
							close-after-click
							:disabled="objectStore.isLoading('theme')"
							@click="objectStore.fetchCollection('theme')">
							<template #icon>
								<Refresh :size="20" />
							</template>
							Refresh
						</NcActionButton>
						<NcActionButton
							title="View documentation about themes"
							@click="openLink('https://conduction.gitbook.io/opencatalogi-nextcloud/beheerders/themas', '_blank')">
							<template #icon>
								<HelpCircleOutline :size="20" />
							</template>
							Help
						</NcActionButton>
					</NcActions>
				</div>
			</div>

			<!-- Loading, Error, and Empty States -->
			<NcEmptyContent v-if="objectStore.isLoading('theme') || !filteredThemes.length"
				:name="emptyContentName"
				:description="emptyContentDescription">
				<template #icon>
					<NcLoadingIcon v-if="objectStore.isLoading('theme')" :size="64" />
					<ShapeOutline v-else :size="64" />
				</template>
				<template v-if="!objectStore.isLoading('theme') && !objectStore.getCollection('theme')?.results?.length" #action>
					<NcButton type="primary" @click="objectStore.clearActiveObject('theme'); navigationStore.setModal('theme')">
						{{ t('opencatalogi', 'Add theme') }}
					</NcButton>
				</template>
			</NcEmptyContent>

			<!-- Content -->
			<div v-else>
				<template v-if="viewMode === 'cards'">
					<div class="cardGrid">
						<div v-for="theme in paginatedThemes" :key="theme.id" class="card">
							<div class="cardHeader">
								<h2 v-tooltip.bottom="theme.summary">
									<ShapeOutline :size="20" />
									{{ theme.title }}
								</h2>
								<NcActions :primary="true" menu-name="Actions">
									<template #icon>
										<DotsHorizontal :size="20" />
									</template>
									<NcActionButton close-after-click @click="objectStore.setActiveObject('theme', theme); navigationStore.setModal('viewTheme')">
										<template #icon>
											<Eye :size="20" />
										</template>
										View
									</NcActionButton>
									<NcActionButton close-after-click @click="objectStore.setActiveObject('theme', theme); navigationStore.setModal('theme')">
										<template #icon>
											<Pencil :size="20" />
										</template>
										Edit
									</NcActionButton>
									<NcActionButton close-after-click @click="objectStore.setActiveObject('theme', theme); navigationStore.setDialog('copyObject', { objectType: 'theme', dialogTitle: 'Theme' })">
										<template #icon>
											<ContentCopy :size="20" />
										</template>
										Copy
									</NcActionButton>
									<NcActionButton close-after-click @click="objectStore.setActiveObject('theme', theme); navigationStore.setDialog('deleteObject', { objectType: 'theme', dialogTitle: 'Theme' })">
										<template #icon>
											<TrashCanOutline :size="20" />
										</template>
										Delete
									</NcActionButton>
								</NcActions>
							</div>
							<!-- Theme Statistics Table -->
							<table class="statisticsTable themeStats">
								<thead>
									<tr>
										<th>{{ t('opencatalogi', 'Property') }}</th>
										<th>{{ t('opencatalogi', 'Value') }}</th>
										<th>{{ t('opencatalogi', 'Status') }}</th>
									</tr>
								</thead>
								<tbody>
									<tr v-if="theme.summary">
										<td>{{ t('opencatalogi', 'Summary') }}</td>
										<td class="truncatedText">
											{{ theme.summary }}
										</td>
										<td>{{ 'Available' }}</td>
									</tr>
									<tr v-if="theme.description">
										<td>{{ t('opencatalogi', 'Description') }}</td>
										<td class="truncatedText">
											{{ theme.description }}
										</td>
										<td>{{ 'Available' }}</td>
									</tr>
									<tr v-if="theme.image">
										<td>{{ t('opencatalogi', 'Image') }}</td>
										<td class="truncatedText">
											{{ theme.image }}
										</td>
										<td>{{ 'Available' }}</td>
									</tr>
									<tr v-if="theme.status">
										<td>{{ t('opencatalogi', 'Status') }}</td>
										<td>{{ theme.status }}</td>
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
									<th>{{ t('opencatalogi', 'Summary') }}</th>
									<th class="tableColumnActions">
										{{ t('opencatalogi', 'Actions') }}
									</th>
								</tr>
							</thead>
							<tbody>
								<tr v-for="theme in paginatedThemes"
									:key="theme.id"
									class="viewTableRow"
									:class="{ viewTableRowSelected: selectedThemes.includes(theme.id) }">
									<td class="tableColumnCheckbox">
										<NcCheckboxRadioSwitch
											:checked="selectedThemes.includes(theme.id)"
											@update:checked="(checked) => toggleThemeSelection(theme.id, checked)" />
									</td>
									<td class="tableColumnTitle">
										<div class="titleContent">
											<strong>{{ theme.title }}</strong>
											<span v-if="theme.description" class="textDescription textEllipsis">{{ theme.description }}</span>
										</div>
									</td>
									<td>{{ theme.status || '-' }}</td>
									<td class="tableColumnConstrained">
										<span v-if="theme.summary">{{ theme.summary }}</span>
										<span v-else>-</span>
									</td>
									<td class="tableColumnActions">
										<NcActions :primary="false">
											<template #icon>
												<DotsHorizontal :size="20" />
											</template>
											<NcActionButton close-after-click @click="objectStore.setActiveObject('theme', theme); navigationStore.setModal('viewTheme')">
												<template #icon>
													<Eye :size="20" />
												</template>
												View
											</NcActionButton>
											<NcActionButton close-after-click @click="objectStore.setActiveObject('theme', theme); navigationStore.setModal('theme')">
												<template #icon>
													<Pencil :size="20" />
												</template>
												Edit
											</NcActionButton>
											<NcActionButton close-after-click @click="objectStore.setActiveObject('theme', theme); navigationStore.setDialog('copyObject', { objectType: 'theme', dialogTitle: 'Theme' })">
												<template #icon>
													<ContentCopy :size="20" />
												</template>
												Copy
											</NcActionButton>
											<NcActionButton close-after-click @click="objectStore.setActiveObject('theme', theme); navigationStore.setDialog('deleteObject', { objectType: 'theme', dialogTitle: 'Theme' })">
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
				:total-pages="currentPagination.pages || Math.ceil(filteredThemes.length / (currentPagination.limit || 20))"
				:total-items="currentPagination.total || filteredThemes.length"
				:current-page-size="currentPagination.limit || 20"
				:min-items-to-show="0"
				@page-changed="onPageChanged"
				@page-size-changed="onPageSizeChanged" />
		</div>
	</NcAppContent>
</template>

<script>
import { NcAppContent, NcEmptyContent, NcLoadingIcon, NcActions, NcActionButton, NcCheckboxRadioSwitch, NcButton } from '@nextcloud/vue'
import ShapeOutline from 'vue-material-design-icons/ShapeOutline.vue'
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
	name: 'ThemeIndex',
	components: {
		NcAppContent,
		NcEmptyContent,
		NcLoadingIcon,
		NcActions,
		NcActionButton,
		NcCheckboxRadioSwitch,
		NcButton,
		ShapeOutline,
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
			selectedThemes: [],
			viewMode: 'cards',
		}
	},
	computed: {
		filteredThemes() {
			return objectStore.getCollection('theme')?.results || []
		},
		currentPagination() {
			const pagination = objectStore.getPagination('theme')
			console.info('Current pagination data:', pagination)
			return pagination
		},
		paginatedThemes() {
			return this.filteredThemes
		},
		allSelected() {
			return this.filteredThemes.length > 0 && this.filteredThemes.every(theme => this.selectedThemes.includes(theme.id))
		},
		someSelected() {
			return this.selectedThemes.length > 0 && !this.allSelected
		},
		emptyContentName() {
			if (objectStore.isLoading('theme')) {
				return t('opencatalogi', 'Loading themes...')
			} else if (!objectStore.getCollection('theme')?.results?.length) {
				return t('opencatalogi', 'No themes found')
			}
			return ''
		},
		emptyContentDescription() {
			if (objectStore.isLoading('theme')) {
				return t('opencatalogi', 'Please wait while we fetch your themes.')
			} else if (!objectStore.getCollection('theme')?.results?.length) {
				return t('opencatalogi', 'No themes are available.')
			}
			return ''
		},
	},
	mounted() {
		console.info('ThemeIndex mounted, fetching themes...')
		objectStore.fetchCollection('theme')
	},
	methods: {
		setViewMode(mode) {
			console.info('Setting view mode to:', mode)
			this.viewMode = mode
		},
		toggleSelectAll(checked) {
			if (checked) {
				this.selectedThemes = this.filteredThemes.map(theme => theme.id)
			} else {
				this.selectedThemes = []
			}
		},
		toggleThemeSelection(themeId, checked) {
			if (checked) {
				this.selectedThemes.push(themeId)
			} else {
				this.selectedThemes = this.selectedThemes.filter(id => id !== themeId)
			}
		},
		onPageChanged(page) {
			console.info('Page changed to:', page)
			objectStore.fetchCollection('theme', { _page: page, _limit: this.currentPagination.limit || 20 })
		},
		onPageSizeChanged(pageSize) {
			console.info('Page size changed to:', pageSize)
			objectStore.fetchCollection('theme', { _page: 1, _limit: pageSize })
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
