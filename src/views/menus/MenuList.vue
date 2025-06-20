/**
 * MenuList.vue
 * Component for displaying a list of menus
 * @category Views
 * @package opencatalogi
 * @author Ruben Linde
 * @copyright 2024
 * @license AGPL-3.0-or-later
 * @version 1.0.0
 * @link https://github.com/opencatalogi/opencatalogi
 */

<script setup>
import { navigationStore, objectStore } from '../../store/store.js'
</script>

<template>
	<NcAppContentList>
		<ul>
			<div class="listHeader">
				<NcTextField class="searchField"
					:value="objectStore.getSearchTerm('menu')"
					label="Zoeken"
					trailing-button-icon="close"
					:show-trailing-button="objectStore.getSearchTerm('menu') !== ''"
					@update:value="(value) => objectStore.setSearchTerm('menu', value)"
					@trailing-button-click="objectStore.clearSearchTerm('menu')">
					<Magnify :size="20" />
				</NcTextField>
				<NcActions>
					<NcActionButton
						title="Bekijk de documentatie over menu's"
						@click="openLink('https://conduction.gitbook.io/opencatalogi-nextcloud/beheerders/menus', '_blank')">
						<template #icon>
							<HelpCircleOutline :size="20" />
						</template>
						Help
					</NcActionButton>
					<NcActionButton close-after-click
						:disabled="objectStore.isLoading('menu')"
						@click="objectStore.fetchCollection('menu')">
						<template #icon>
							<Refresh :size="20" />
						</template>
						Ververs
					</NcActionButton>
					<NcActionButton close-after-click @click="openAddMenuModal">
						<template #icon>
							<Plus :size="20" />
						</template>
						Menu toevoegen
					</NcActionButton>
				</NcActions>
			</div>

			<div v-if="!objectStore.isLoading('menu')">
				<NcListItem v-for="(menu, i) in objectStore.getCollection('menu').results"
					:key="`${menu}${i}`"
					:name="menu.title"
					:details="menu.description"
					:active="objectStore.getActiveObject('menu')?.id === menu?.id"
					:force-display-actions="true"
					@click="toggleActive(menu)">
					<template #icon>
						<MenuIcon :class="objectStore.getActiveObject('menu')?.id === menu.id && 'selectedZaakIcon'"
							disable-menu
							:size="44" />
					</template>
					<template #actions>
						<NcActionButton close-after-click @click="onActionButtonClick(menu, 'edit')">
							<template #icon>
								<Pencil :size="20" />
							</template>
							Bewerken
						</NcActionButton>
						<NcActionButton close-after-click @click="onActionButtonClick(menu, 'addMenuItem')">
							<template #icon>
								<Plus :size="20" />
							</template>
							Menu item toevoegen
						</NcActionButton>
						<NcActionButton close-after-click @click="onActionButtonClick(menu, 'copyObject')">
							<template #icon>
								<ContentCopy :size="20" />
							</template>
							Kopiëren
						</NcActionButton>
						<NcActionButton close-after-click @click="onActionButtonClick(menu, 'deleteObject')">
							<template #icon>
								<Delete :size="20" />
							</template>
							Verwijderen
						</NcActionButton>
					</template>
				</NcListItem>
			</div>

			<NcLoadingIcon v-if="objectStore.isLoading('menu')"
				:size="64"
				class="loadingIcon"
				appearance="dark"
				name="Menu's aan het laden" />

			<div v-if="!objectStore.getCollection('menu').results.length" class="emptyListHeader">
				Er zijn nog geen menu's gedefinieerd.
			</div>
		</ul>
	</NcAppContentList>
</template>

<script>
import { NcListItem, NcActionButton, NcAppContentList, NcTextField, NcLoadingIcon, NcActions } from '@nextcloud/vue'
import Magnify from 'vue-material-design-icons/Magnify.vue'
import Menu from 'vue-material-design-icons/Menu.vue'
import Plus from 'vue-material-design-icons/Plus.vue'
import Pencil from 'vue-material-design-icons/Pencil.vue'
import Delete from 'vue-material-design-icons/Delete.vue'
import Refresh from 'vue-material-design-icons/Refresh.vue'
import HelpCircleOutline from 'vue-material-design-icons/HelpCircleOutline.vue'
import ContentCopy from 'vue-material-design-icons/ContentCopy.vue'

export default {
	name: 'MenuList',
	components: {
		NcListItem,
		NcActionButton,
		NcAppContentList,
		NcTextField,
		NcLoadingIcon,
		NcActions,
		// Icons
		Magnify,
		// Menu is reserved in HTML, so we use MenuIcon instead
		MenuIcon: Menu,
		Plus,
		Pencil,
		Delete,
		Refresh,
		HelpCircleOutline,
		ContentCopy,
	},
	methods: {
		openLink(url, type = '') {
			window.open(url, type)
		},
		toggleActive(menu) {
			objectStore.getActiveObject('menu')?.id === menu?.id ? objectStore.clearActiveObject('menu') : objectStore.setActiveObject('menu', menu)
		},
		openAddMenuModal() {
			navigationStore.setModal('menu')
			objectStore.clearActiveObject('menu')
		},
		onActionButtonClick(menu, action) {
			objectStore.setActiveObject('menu', menu)
			switch (action) {
			case 'edit':
				navigationStore.setModal('menu')
				break
			case 'addMenuItem':
				navigationStore.setModal('menuItemForm')
				break
			case 'copyObject':
			case 'deleteObject':
				navigationStore.setDialog(action, { objectType: 'menu', dialogTitle: 'Menu' })
				break
			}
		},
	},
}
</script>

<style>
.listHeader {
    position: sticky;
    top: 0;
    z-index: 1000;
    background-color: var(--color-main-background);
    border-bottom: 1px solid var(--color-border);
}

.searchField {
    padding-inline-start: 65px;
    padding-inline-end: 20px;
    margin-block-end: 6px;
}

.selectedZaakIcon>svg {
    fill: white;
}

.loadingIcon {
    margin-block-start: var(--OC-margin-20);
}
</style>
