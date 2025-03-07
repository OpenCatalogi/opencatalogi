<script setup>
import { navigationStore, menuStore } from '../../store/store.js'
</script>

<template>
	<NcAppContentList>
		<ul>
			<div class="listHeader">
				<NcTextField class="searchField"
					:value.sync="search"
					label="Zoeken"
					trailing-button-icon="close"
					:show-trailing-button="search !== ''"
					@trailing-button-click="search = ''">
					<Magnify :size="20" />
				</NcTextField>
				<NcActions>
					<NcActionButton
						title="Bekijk de documentatie over menu's"
						@click="openLink('https://conduction.gitbook.io/opencatalogi-nextcloud/beheerders/menus')">
						<template #icon>
							<HelpCircleOutline :size="20" />
						</template>
						Help
					</NcActionButton>
					<NcActionButton :disabled="loading" @click="refresh">
						<template #icon>
							<Refresh :size="20" />
						</template>
						Ververs
					</NcActionButton>
					<NcActionButton @click="menuStore.setMenuItem(false); navigationStore.setModal('editMenu')">
						<template #icon>
							<Plus :size="20" />
						</template>
						Menu toevoegen
					</NcActionButton>
				</NcActions>
			</div>
			<div v-if="!loading">
				<NcListItem v-for="(menu, i) in filteredMenus"
					:key="`${menu}${i}`"
					:name="menu.name"
					:bold="false"
					:force-display-actions="true"
					:active="menuStore.menuItem?.id === menu.id"
					:details="menu?.status"
					@click="setActive(menu)">
					<template #icon>
						<MenuClose :size="44" />
					</template>
					<template #subname>
						{{ menu?.slug }}
					</template>
					<template #actions>
						<NcActionButton @click="menuStore.setMenuItem(menu); navigationStore.setModal('editMenu')">
							<template #icon>
								<Pencil :size="20" />
							</template>
							Bewerken
						</NcActionButton>
						<NcActionButton @click="menuStore.setMenuItem(menu); menuStore.menuItemItemsIndex = null; navigationStore.setModal('editMenuItem')">
							<template #icon>
								<Plus :size="20" />
							</template>
							Menu item toevoegen
						</NcActionButton>
						<NcActionButton @click="menuStore.setMenuItem(menu); navigationStore.setDialog('copyMenu')">
							<template #icon>
								<ContentCopy :size="20" />
							</template>
							Kopiëren
						</NcActionButton>
						<NcActionButton @click="menuStore.setMenuItem(menu); navigationStore.setModal('deleteMenu')">
							<template #icon>
								<Delete :size="20" />
							</template>
							Verwijderen
						</NcActionButton>
					</template>
				</NcListItem>
			</div>

			<NcLoadingIcon v-if="loading"
				:size="64"
				class="loadingIcon"
				appearance="dark"
				name="Menu's aan het laden" />

			<div v-if="!filteredMenus.length" class="emptyListHeader">
				Er zijn nog geen menu's gedefinieerd.
			</div>
		</ul>
	</NcAppContentList>
</template>
<script>
import { NcActionButton, NcActions, NcAppContentList, NcListItem, NcLoadingIcon, NcTextField } from '@nextcloud/vue'
import { debounce } from 'lodash'

// Icons
import Delete from 'vue-material-design-icons/Delete.vue'
import HelpCircleOutline from 'vue-material-design-icons/HelpCircleOutline.vue'
import Magnify from 'vue-material-design-icons/Magnify.vue'
import Pencil from 'vue-material-design-icons/Pencil.vue'
import Plus from 'vue-material-design-icons/Plus.vue'
import Refresh from 'vue-material-design-icons/Refresh.vue'
import MenuClose from 'vue-material-design-icons/MenuClose.vue'
import ContentCopy from 'vue-material-design-icons/ContentCopy.vue'

export default {
	name: 'MenuList',
	components: {
		NcListItem,
		NcActionButton,
		NcAppContentList,
		NcTextField,
		Magnify,
		NcLoadingIcon,
		NcActions,
		// Icons
		Refresh,
		Plus,
		MenuClose,
		Pencil,
		HelpCircleOutline,
		Delete,
	},
	beforeRouteLeave(to, from, next) {
		search = ''
		next()
	},
	props: {
		search: {
			type: String,
			required: true,
		},
	},
	data() {
		return {
			loading: false,
		}
	},
	computed: {
		filteredMenus() {
			if (!menuStore?.menuList) return []
			return menuStore.menuList.filter((menu) => {
				return menu
			})
		},
	},
	watch: {
		search: {
			handler(search) {
				this.debouncedFetchData(search)
			},
		},
	},
	mounted() {
		this.fetchData()
	},
	methods: {
		refresh(e) {
			e.preventDefault()
			this.fetchData()
		},
		fetchData(search = null) {
			this.loading = true
			menuStore.refreshMenuList(search)
				.then(() => {
					this.loading = false
				})
		},
		debouncedFetchData: debounce(function(search) {
			this.fetchData(search)
		}, 500),
		openLink(url, type = '') {
			window.open(url, type)
		},
		setActive(menu) {
			if (JSON.stringify(menuStore.menuItem) === JSON.stringify(menu)) {
				menuStore.setMenuItem(false)
			} else { menuStore.setMenuItem(menu) }
		},
	},
}
</script>
<style>
.listHeader{
	display: flex;
}

.refresh{
	margin-block-start: 11px !important;
    margin-block-end: 11px !important;
    margin-inline-end: 10px;
}

.active.menuDetails-actionsDelete {
    background-color: var(--color-error) !important;
}
.active.menuDetails-actionsDelete button {
    color: #EBEBEB !important;
}

.loadingIcon {
    margin-block-start: var(--OC-margin-20);
}
</style>
