/**
 * MenuIndex.vue
 * Component for displaying menus with cards and table view
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
					{{ t('opencatalogi', 'Menus') }}
				</h1>
				<p>{{ t('opencatalogi', 'Manage your navigation menus and menu items') }}</p>
			</div>

			<!-- Actions Bar -->
			<div class="viewActionsBar">
				<div class="viewInfo">
					<span class="viewTotalCount">
						{{ t('opencatalogi', 'Showing {showing} of {total} menus', { showing: filteredMenus.length, total: currentPagination.total || filteredMenus.length }) }}
					</span>
					<span v-if="selectedMenus.length > 0" class="viewIndicator">
						({{ t('opencatalogi', '{count} selected', { count: selectedMenus.length }) }})
					</span>
				</div>
				<div class="viewActions">
					<div class="viewModeSwitchContainer">
						<NcCheckboxRadioSwitch
							v-tooltip="'See menus as cards'"
							:checked="viewMode === 'cards'"
							:button-variant="true"
							value="cards"
							name="menus_view_mode"
							type="radio"
							button-variant-grouped="horizontal"
							@update:checked="() => setViewMode('cards')">
							Cards
						</NcCheckboxRadioSwitch>
						<NcCheckboxRadioSwitch
							v-tooltip="'See menus as a table'"
							:checked="viewMode === 'table'"
							:button-variant="true"
							value="table"
							name="menus_view_mode"
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
							@click="objectStore.clearActiveObject('menu'); navigationStore.setModal('menu')">
							<template #icon>
								<Plus :size="20" />
							</template>
							Add Menu
						</NcActionButton>
						<NcActionButton
							close-after-click
							:disabled="objectStore.isLoading('menu')"
							@click="objectStore.fetchCollection('menu')">
							<template #icon>
								<Refresh :size="20" />
							</template>
							Refresh
						</NcActionButton>
						<NcActionButton
							title="View documentation about menus"
							@click="openLink('https://conduction.gitbook.io/opencatalogi-nextcloud/beheerders/menus', '_blank')">
							<template #icon>
								<HelpCircleOutline :size="20" />
							</template>
							Help
						</NcActionButton>
					</NcActions>
				</div>
			</div>

			<!-- Loading, Error, and Empty States -->
			<NcEmptyContent v-if="objectStore.isLoading('menu') || !filteredMenus.length"
				:name="emptyContentName"
				:description="emptyContentDescription">
				<template #icon>
					<NcLoadingIcon v-if="objectStore.isLoading('menu')" :size="64" />
					<MenuIcon v-else :size="64" />
				</template>
				<template v-if="!objectStore.isLoading('menu') && !objectStore.getCollection('menu')?.results?.length" #action>
					<NcButton type="primary" @click="objectStore.clearActiveObject('menu'); navigationStore.setModal('menu')">
						{{ t('opencatalogi', 'Add menu') }}
					</NcButton>
				</template>
			</NcEmptyContent>

			<!-- Content -->
			<div v-else>
				<template v-if="viewMode === 'cards'">
					<div class="cardGrid">
						<div v-for="menu in paginatedMenus" :key="menu.id" class="card">
							<div class="cardHeader">
								<h2 v-tooltip.bottom="menu.description">
									<MenuIcon :size="20" />
									{{ menu.title }}
								</h2>
								<NcActions :primary="true" menu-name="Actions">
									<template #icon>
										<DotsHorizontal :size="20" />
									</template>
									<NcActionButton close-after-click @click="objectStore.setActiveObject('menu', menu); navigationStore.setModal('viewMenu')">
										<template #icon>
											<Eye :size="20" />
										</template>
										View
									</NcActionButton>
									<NcActionButton close-after-click @click="objectStore.setActiveObject('menu', menu); navigationStore.setModal('menu')">
										<template #icon>
											<Pencil :size="20" />
										</template>
										Edit
									</NcActionButton>
									<NcActionButton close-after-click @click="objectStore.setActiveObject('menu', menu); navigationStore.setModal('menuItemForm')">
										<template #icon>
											<Plus :size="20" />
										</template>
										Add Item
									</NcActionButton>
									<NcActionButton close-after-click @click="objectStore.setActiveObject('menu', menu); navigationStore.setDialog('copyObject', { objectType: 'menu', dialogTitle: 'Menu' })">
										<template #icon>
											<ContentCopy :size="20" />
										</template>
										Copy
									</NcActionButton>
									<NcActionButton close-after-click @click="objectStore.setActiveObject('menu', menu); navigationStore.setDialog('deleteObject', { objectType: 'menu', dialogTitle: 'Menu' })">
										<template #icon>
											<TrashCanOutline :size="20" />
										</template>
										Delete
									</NcActionButton>
								</NcActions>
							</div>
							<!-- Menu Statistics Table -->
							<table class="statisticsTable menuStats">
								<thead>
									<tr>
										<th>{{ t('opencatalogi', 'Property') }}</th>
										<th>{{ t('opencatalogi', 'Value') }}</th>
										<th>{{ t('opencatalogi', 'Status') }}</th>
									</tr>
								</thead>
								<tbody>
									<tr v-if="menu.slug">
										<td>{{ t('opencatalogi', 'Slug') }}</td>
										<td>{{ menu.slug }}</td>
										<td>{{ 'Available' }}</td>
									</tr>
									<tr v-if="menu.description">
										<td>{{ t('opencatalogi', 'Description') }}</td>
										<td class="truncatedText">
											{{ menu.description }}
										</td>
										<td>{{ 'Available' }}</td>
									</tr>
									<tr>
										<td>{{ t('opencatalogi', 'Position') }}</td>
										<td>
											<span v-if="menu.position === 0">Header</span>
											<span v-else-if="menu.position === 1">Navigation</span>
											<span v-else-if="menu.position === 2">Footer</span>
											<span v-else>{{ menu.position }}</span>
										</td>
										<td>{{ 'Configured' }}</td>
									</tr>
									<tr>
										<td>{{ t('opencatalogi', 'Menu Items') }}</td>
										<td>{{ menu.items?.length || 0 }}</td>
										<td>{{ menu.items?.length > 0 ? 'Configured' : 'Empty' }}</td>
									</tr>
									<tr v-if="menu.updatedAt">
										<td>{{ t('opencatalogi', 'Last Updated') }}</td>
										<td>{{ new Date(menu.updatedAt).toLocaleDateString() }}</td>
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
									<th>{{ t('opencatalogi', 'Position') }}</th>
									<th>{{ t('opencatalogi', 'Menu Items') }}</th>
									<th>{{ t('opencatalogi', 'Last Updated') }}</th>
									<th class="tableColumnActions">
										{{ t('opencatalogi', 'Actions') }}
									</th>
								</tr>
							</thead>
							<tbody>
								<tr v-for="menu in paginatedMenus"
									:key="menu.id"
									class="viewTableRow"
									:class="{ viewTableRowSelected: selectedMenus.includes(menu.id) }">
									<td class="tableColumnCheckbox">
										<NcCheckboxRadioSwitch
											:checked="selectedMenus.includes(menu.id)"
											@update:checked="(checked) => toggleMenuSelection(menu.id, checked)" />
									</td>
									<td class="tableColumnTitle">
										<div class="titleContent">
											<strong>{{ menu.title }}</strong>
											<span v-if="menu.description" class="textDescription textEllipsis">{{ menu.description }}</span>
										</div>
									</td>
									<td>
										<span v-if="menu.position === 0">Header</span>
										<span v-else-if="menu.position === 1">Navigation</span>
										<span v-else-if="menu.position === 2">Footer</span>
										<span v-else>{{ menu.position }}</span>
									</td>
									<td>{{ menu.items?.length || 0 }}</td>
									<td class="tableColumnConstrained">
										<span v-if="menu.updatedAt">{{ new Date(menu.updatedAt).toLocaleDateString() }}</span>
										<span v-else>-</span>
									</td>
									<td class="tableColumnActions">
										<NcActions :primary="false">
											<template #icon>
												<DotsHorizontal :size="20" />
											</template>
											<NcActionButton close-after-click @click="objectStore.setActiveObject('menu', menu); navigationStore.setModal('viewMenu')">
												<template #icon>
													<Eye :size="20" />
												</template>
												View
											</NcActionButton>
											<NcActionButton close-after-click @click="objectStore.setActiveObject('menu', menu); navigationStore.setModal('menu')">
												<template #icon>
													<Pencil :size="20" />
												</template>
												Edit
											</NcActionButton>
											<NcActionButton close-after-click @click="objectStore.setActiveObject('menu', menu); navigationStore.setModal('menuItemForm')">
												<template #icon>
													<Plus :size="20" />
												</template>
												Add Item
											</NcActionButton>
											<NcActionButton close-after-click @click="objectStore.setActiveObject('menu', menu); navigationStore.setDialog('copyObject', { objectType: 'menu', dialogTitle: 'Menu' })">
												<template #icon>
													<ContentCopy :size="20" />
												</template>
												Copy
											</NcActionButton>
											<NcActionButton close-after-click @click="objectStore.setActiveObject('menu', menu); navigationStore.setDialog('deleteObject', { objectType: 'menu', dialogTitle: 'Menu' })">
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
				:total-pages="currentPagination.pages || Math.ceil(filteredMenus.length / (currentPagination.limit || 20))"
				:total-items="currentPagination.total || filteredMenus.length"
				:current-page-size="currentPagination.limit || 20"
				:min-items-to-show="0"
				@page-changed="onPageChanged"
				@page-size-changed="onPageSizeChanged" />
		</div>
	</NcAppContent>
</template>

<script>
import { NcAppContent, NcEmptyContent, NcLoadingIcon, NcActions, NcActionButton, NcCheckboxRadioSwitch, NcButton } from '@nextcloud/vue'
import Menu from 'vue-material-design-icons/Menu.vue'
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
	name: 'MenuIndex',
	components: {
		NcAppContent,
		NcEmptyContent,
		NcLoadingIcon,
		NcActions,
		NcActionButton,
		NcCheckboxRadioSwitch,
		NcButton,
		// Menu is reserved in HTML, so we use MenuIcon instead
		MenuIcon: Menu,
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
			selectedMenus: [],
			viewMode: 'cards',
		}
	},
	computed: {
		filteredMenus() {
			return objectStore.getCollection('menu')?.results || []
		},
		currentPagination() {
			const pagination = objectStore.getPagination('menu')
			console.info('Current pagination data:', pagination)
			return pagination
		},
		paginatedMenus() {
			return this.filteredMenus
		},
		allSelected() {
			return this.filteredMenus.length > 0 && this.filteredMenus.every(menu => this.selectedMenus.includes(menu.id))
		},
		someSelected() {
			return this.selectedMenus.length > 0 && !this.allSelected
		},
		emptyContentName() {
			if (objectStore.isLoading('menu')) {
				return t('opencatalogi', 'Loading menus...')
			} else if (!objectStore.getCollection('menu')?.results?.length) {
				return t('opencatalogi', 'No menus found')
			}
			return ''
		},
		emptyContentDescription() {
			if (objectStore.isLoading('menu')) {
				return t('opencatalogi', 'Please wait while we fetch your menus.')
			} else if (!objectStore.getCollection('menu')?.results?.length) {
				return t('opencatalogi', 'No menus are available.')
			}
			return ''
		},
	},
	mounted() {
		console.info('MenuIndex mounted, fetching menus...')
		objectStore.fetchCollection('menu')
	},
	methods: {
		setViewMode(mode) {
			console.info('Setting view mode to:', mode)
			this.viewMode = mode
		},
		toggleSelectAll(checked) {
			if (checked) {
				this.selectedMenus = this.filteredMenus.map(menu => menu.id)
			} else {
				this.selectedMenus = []
			}
		},
		toggleMenuSelection(menuId, checked) {
			if (checked) {
				this.selectedMenus.push(menuId)
			} else {
				this.selectedMenus = this.selectedMenus.filter(id => id !== menuId)
			}
		},
		onPageChanged(page) {
			console.info('Page changed to:', page)
			objectStore.fetchCollection('menu', { _page: page, _limit: this.currentPagination.limit || 20 })
		},
		onPageSizeChanged(pageSize) {
			console.info('Page size changed to:', pageSize)
			objectStore.fetchCollection('menu', { _page: 1, _limit: pageSize })
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
