<script setup>
import { navigationStore, menuStore } from '../../store/store.js'
import { getTheme } from '../../services/getTheme.js'
import { EventBus } from '../../eventBus.js'
</script>

<template>
	<div class="detailContainer">
		<div class="head">
			<h1 class="h1">
				{{ menuStore.menuItem.name }}
			</h1>

			<NcActions
				:disabled="loading"
				:primary="true"
				:menu-name="loading ? 'Laden...' : 'Acties'"
				:inline="1"
				title="Acties die je kan uitvoeren op deze pagina">
				<template #icon>
					<span>
						<NcLoadingIcon v-if="loading"
							:size="20"
							appearance="dark" />
						<DotsHorizontal v-if="!loading" :size="20" />
					</span>
				</template>
				<NcActionButton
					title="Bekijk de documentatie over paginas"
					@click="openLink('https://conduction.gitbook.io/opencatalogi-nextcloud/beheerders/paginas')">
					<template #icon>
						<HelpCircleOutline :size="20" />
					</template>
					Help
				</NcActionButton>
				<NcActionButton @click="navigationStore.setModal('editMenu')">
					<template #icon>
						<Pencil :size="20" />
					</template>
					Bewerken
				</NcActionButton>
				<NcActionButton @click="menuStore.menuItemItemsIndex = null; navigationStore.setModal('editMenuItem')">
					<template #icon>
						<Plus :size="20" />
					</template>
					Menu item toevoegen
				</NcActionButton>
				<NcActionButton @click="navigationStore.setDialog('copyMenu')">
					<template #icon>
						<ContentCopy :size="20" />
					</template>
					Kopiëren
				</NcActionButton>
				<NcActionButton @click="navigationStore.setModal('deleteMenu')">
					<template #icon>
						<Delete :size="20" />
					</template>
					Verwijderen
				</NcActionButton>
			</NcActions>
		</div>
		<div class="container">
			<div class="detailGrid">
				<div>
					<b>Naam:</b>
					<span>{{ menuStore.menuItem.name }}</span>
				</div>
				<div>
					<b>Positie:</b>
					<span v-if="menuStore.menuItem.position === 1">{{ menuStore.menuItem.position }} - rechts boven</span>
					<span v-else-if="menuStore.menuItem.position === 2">{{ menuStore.menuItem.position }} - navigatiebalk</span>
					<span v-else-if="menuStore.menuItem.position >= 3">{{ menuStore.menuItem.position }} - footer</span>
					<span v-else>{{ menuStore.menuItem.position }} - niet gedefinieerd</span>
				</div>
				<div>
					<b>Laatst bijgewerkt:</b>
					<span>{{ menuStore.menuItem.updatedAt }}</span>
				</div>
			</div>
		</div>

		<div class="tabContainer">
			<BTabs content-class="mt-3" justified>
				<BTab active>
					<template #title>
						<div class="tabTitleLoadingContainer">
							<p>Data</p>
							<NcLoadingIcon v-if="safeItemsLoading" class="tabTitleIcon" :size="24" />
							<CheckCircleOutline v-if="safeItemsLoadingSuccess" class="tabTitleIcon" :size="24" />
						</div>
					</template>

					<!-- if menu has items -->
					<div v-if="menuItems.length > 0">
						<!-- show draggable list -->
						<VueDraggable v-model="menuItems" easing="ease-in-out">
							<!-- show a div which is draggable for each item -->
							<div v-for="(menuItem, i) in menuItems" :key="i" :class="`draggable-list-item ${getTheme()}`">
								<!-- show a drag handle and NcListItem -->
								<Drag class="drag-handle" :size="40" />
								<NcListItem :name="menuItem.name"
									:bold="false"
									:force-display-actions="true">
									<template #subname>
										{{ menuItem.description }}
									</template>
									<template #actions>
										<NcActionButton :disabled="safeItemsLoading"
											@click="() => {
												menuStore.menuItemsItemId = menuItem.id
												navigationStore.setModal('editMenuItem')
											}">
											<template #icon>
												<Pencil :size="20" />
											</template>
											Bewerk menu item
										</NcActionButton>
										<NcActionButton :disabled="safeItemsLoading"
											@click="() => {
												menuStore.menuItemsItemId = menuItem.id
												navigationStore.setModal('deleteMenuItem')
											}">
											<template #icon>
												<Delete :size="20" />
											</template>
											Verwijder menu item
										</NcActionButton>
									</template>
								</NcListItem>
							</div>
						</VueDraggable>

						<NcButton :disabled="(JSON.stringify(menuStore.menuItem.items) === JSON.stringify(menuItems)) || safeItemsLoading"
							@click="saveMenuItems">
							Opslaan
						</NcButton>
					</div>
					<div v-else>
						Geen menu items gevonden
					</div>
				</BTab>
			</BTabs>
		</div>
	</div>
</template>

<script>
// Components
import { NcActionButton, NcActions, NcLoadingIcon, NcListItem, NcButton } from '@nextcloud/vue'
import { VueDraggable } from 'vue-draggable-plus'
import { BTabs, BTab } from 'bootstrap-vue'

// Icons
import ContentCopy from 'vue-material-design-icons/ContentCopy.vue'
import Delete from 'vue-material-design-icons/Delete.vue'
import DotsHorizontal from 'vue-material-design-icons/DotsHorizontal.vue'
import HelpCircleOutline from 'vue-material-design-icons/HelpCircleOutline.vue'
import Pencil from 'vue-material-design-icons/Pencil.vue'
import Plus from 'vue-material-design-icons/Plus.vue'
import Drag from 'vue-material-design-icons/Drag.vue'
import CheckCircleOutline from 'vue-material-design-icons/CheckCircleOutline.vue'
import _ from 'lodash'
import { Menu } from '../../entities/index.js'

/**
 * Component for displaying and managing page details
 */
export default {
	name: 'MenuDetail',
	components: {
		// Components
		NcLoadingIcon,
		NcActionButton,
		NcActions,
		NcListItem,
		NcButton,
		// Bootstrap
		BTabs,
		BTab,
		// Icons
		DotsHorizontal,
		Pencil,
		Delete,
		ContentCopy,
		HelpCircleOutline,
	},
	data() {
		return {
			menuItems: [],
			safeItemsLoading: false,
			safeItemsLoadingSuccess: false,
			loading: false,
			upToDate: false,
		}
	},
	computed: {
		menuId() {
			return menuStore.menuItem?.id
		},
	},
	watch: {
		menuId: {
			handler(id, oldId) {
				// fetch up-to-date data on id change
				this.fetchItems()
			},
			immediate: true,
		},
	},
	created() {
		// Listen for the event that gets emitted when the menuItem item is saved or deleted
		EventBus.$on(['edit-menu-item-item-success', 'delete-menu-item-item-success'], () => {
			this.fetchItems()
		})
	},
	beforeDestroy() {
		// Clean up the event listener
		EventBus.$off(['edit-menu-item-item-success', 'delete-menu-item-item-success'])
	},
	mounted() {
		this.menuItems = menuStore.menuItem.items

		// fetch up-to-date data on mount
		this.fetchItems()
	},
	methods: {
		fetchItems() {
			menuStore.getOneMenu(menuStore.menuItem.id)
				.then(({ data }) => {
					this.menuItems = data.items.map((item, index) => ({
						...item,
						id: index,
					}))
				})
		},
		resetItemsIndexes() {
			// When the item order is saved, a new Menu entity is created.
			// upon creation, the Menu entity gives each item an ID based on the index of the order of items. (0, 1, 2...)
			// However the items list in this component still holds the original indexes before saving, which depending on how you changed it can be 0, 2, 1..
			// This function resets the ID of each item to the index again, so both the new Menu entity and the items list in this component are in sync.
			// Which is important for the save button to recognize the changes properly, and the edit button to find the correct item to edit.
			this.menuItems = this.menuItems.map((item, index) => ({
				...item,
				id: index,
			}))
		},
		openLink(url, type = '') {
			window.open(url, type)
		},
		saveMenuItems() {
			this.safeItemsLoading = true
			this.safeItemsLoadingSuccess = false

			const menuItemClone = _.cloneDeep(menuStore.menuItem)
			menuItemClone.items = this.menuItems

			const newMenuItem = new Menu(menuItemClone)

			menuStore.saveMenu(newMenuItem)
				.then(() => {
					this.resetItemsIndexes()
					this.safeItemsLoadingSuccess = true
					setTimeout(() => {
						this.safeItemsLoadingSuccess = false
					}, 1500)
				})
				.finally(() => {
					this.safeItemsLoading = false
				})
		},
	},
}
</script>

<style>
h4 {
  font-weight: bold;
}

.head{
	display: flex;
	justify-content: space-between;
}

.button{
	max-height: 10px;
}

.h1 {
  display: block !important;
  font-size: 2em !important;
  margin-block-start: 0.67em !important;
  margin-block-end: 0.67em !important;
  margin-inline-start: 0px !important;
  margin-inline-end: 0px !important;
  font-weight: bold !important;
  unicode-bidi: isolate !important;
}

.dataContent {
  display: flex;
  flex-direction: column;
}

.active.pageDetails-actionsDelete {
    background-color: var(--color-error) !important;
}
.active.pageDetails-actionsDelete button {
    color: #EBEBEB !important;
}

.PageDetail-clickable {
    cursor: pointer !important;
}

.buttonLinkContainer{
	display: flex;
    align-items: center;
}

.float-right {
    float: right;
}
</style>

<style scoped>
.tabTitleLoadingContainer {
    display: flex;
    justify-content: center;
    align-items: center;
    position: relative;
}
.tabTitleLoadingContainer .tabTitleIcon {
    position: absolute;
    right: 0;
}

/* draggable list item */
.draggable-list-item {
    display: flex;
    align-items: center;
    gap: 3px;

    background-color: rgba(255, 255, 255, 0.05);
    padding: 4px;
    border-radius: 8px;

    margin-block: 8px;
}
.draggable-list-item.light {
    background-color: rgba(0, 0, 0, 0.05);
}
</style>
