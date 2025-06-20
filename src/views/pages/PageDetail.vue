/**
 * PageDetail.vue
 * Component for displaying page details
 * @category Components
 * @package opencatalogi
 * @author Ruben Linde
 * @copyright 2024
 * @license AGPL-3.0-or-later
 * @version 1.0.0
 * @link https://github.com/opencatalogi/opencatalogi
 */

<script setup>
import { navigationStore, objectStore } from '../../store/store.js'
import { getTheme } from '../../services/getTheme.js'
</script>

<template>
	<div class="detailContainer">
		<div class="head">
			<h1 class="h1">
				{{ page?.title }}
			</h1>
			<NcActions
				:disabled="objectStore.isLoading('page')"
				:primary="true"
				:menu-name="objectStore.isLoading('page') ? 'Laden...' : 'Acties'"
				:inline="1"
				title="Acties die je kan uitvoeren op deze pagina">
				<template #icon>
					<span>
						<NcLoadingIcon v-if="objectStore.isLoading('page')" :size="20" appearance="dark" />
						<DotsHorizontal v-if="!objectStore.isLoading('page')" :size="20" />
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
				<NcActionButton close-after-click @click="onActionButtonClick(page, 'edit')">
					<template #icon>
						<Pencil :size="20" />
					</template>
					Bewerken
				</NcActionButton>
				<NcActionButton close-after-click @click="onActionButtonClick(page, 'addContent')">
					<template #icon>
						<Plus :size="20" />
					</template>
					Content toevoegen
				</NcActionButton>
				<NcActionButton close-after-click @click="onActionButtonClick(page, 'copyObject')">
					<template #icon>
						<ContentCopy :size="20" />
					</template>
					Kopiëren
				</NcActionButton>
				<NcActionButton close-after-click @click="onActionButtonClick(page, 'deleteObject')">
					<template #icon>
						<Delete :size="20" />
					</template>
					Verwijderen
				</NcActionButton>
			</NcActions>
		</div>
		<div class="pageDetailContent">
			<div class="pageDetailGrid">
				<div>
					<b>Titel:</b>
					<span>{{ page?.title }}</span>
				</div>
				<div>
					<b>Slug:</b>
					<span>{{ page?.slug }}</span>
				</div>
				<div>
					<b>Laatst bijgewerkt:</b>
					<span>{{ page?.updatedAt ? new Date(page.updatedAt).toLocaleDateString() : '-' }}</span>
				</div>
			</div>
		</div>
		<div class="tabContainer">
			<BTabs content-class="mt-3" justified>
				<BTab active>
					<template #title>
						<div class="pageDetailTabTitle">
							<p>Data</p>
							<NcLoadingIcon v-if="saveContentsLoading" class="pageDetailTabIcon" :size="24" />
							<CheckCircleOutline v-if="saveContentsSuccess" class="pageDetailTabIcon" :size="24" />
						</div>
					</template>
					<div v-if="pageContents.length > 0">
						<VueDraggable v-model="pageContents" easing="ease-in-out">
							<div v-for="(pageContent, i) in pageContents"
								:key="i"
								:class="`pageDetailDraggableItem ${getTheme()}`">
								<Drag class="pageDetailDragHandle" :size="40" />
								<NcListItem :name="pageContent.type"
									:bold="false"
									:force-display-actions="true">
									<template #subname>
										{{ JSON.stringify(pageContent.data) }}
									</template>
									<template #actions>
										<NcActionButton close-after-click
											:disabled="saveContentsLoading"
											@click="onContentActionButtonClick(pageContent, 'edit')">
											<template #icon>
												<Pencil :size="20" />
											</template>
											Bewerken
										</NcActionButton>
										<NcActionButton close-after-click
											:disabled="saveContentsLoading"
											@click="onContentActionButtonClick(pageContent, 'delete')">
											<template #icon>
												<Delete :size="20" />
											</template>
											Verwijderen
										</NcActionButton>
									</template>
								</NcListItem>
							</div>
						</VueDraggable>
						<NcButton :disabled="(JSON.stringify(page?.contents) === JSON.stringify(pageContents)) || saveContentsLoading"
							@click="savePageContents">
							Opslaan
						</NcButton>
					</div>
					<div v-else>
						Geen page content gevonden
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
import { Page } from '../../entities/index.js'

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

export default {
	name: 'PageDetail',
	components: {
		NcLoadingIcon,
		NcActionButton,
		NcActions,
		NcButton,
		VueDraggable,
		BTabs,
		BTab,
		DotsHorizontal,
		Pencil,
		Delete,
		ContentCopy,
		HelpCircleOutline,
		Drag,
		CheckCircleOutline,
		Plus,
		NcListItem,
	},
	data() {
		return {
			saveContentsLoading: false,
			saveContentsSuccess: false,
			pageContents: [],
		}
	},
	computed: {
		/**
		 * Get the current page from the store
		 * @return {object | null}
		 */
		page() {
			return objectStore.getActiveObject('page')
		},

	},
	watch: {
		page: {
			handler(newVal, oldVal) {
				if (!oldVal || !_.isEqual(newVal?.contents, oldVal?.contents)) {
					this.pageContents = newVal?.contents || []
				}
			},
			deep: true,
			immediate: true,
		},
	},
	methods: {
		/**
		 * Delete a content item
		 * @param {string} contentId - The ID of the content to delete
		 * @return {Promise<void>}
		 */
		async deleteContent(contentId) {
			if (!this.page) return

			const newContents = this.page.contents.filter((content) => content.id !== contentId)
			const newPageItem = new Page({
				...this.page,
				contents: newContents,
			})
			await objectStore.updateObject('page', newPageItem.id, newPageItem).then(() => {
				this.pageContents = newContents
			}).catch((err) => {
				objectStore.setState('page', { error: err })
			}).finally(() => {
				objectStore.setState('page', { success: null, error: null, loading: false })
			})
			objectStore.clearActiveObject('pageContent')
		},
		/**
		 * Save the page contents
		 * @return {Promise<void>}
		 */
		async savePageContents() {
			if (!this.page) return
			this.saveContentsLoading = true
			this.saveContentsSuccess = false

			try {
				const pageItemClone = _.cloneDeep(this.page)
				pageItemClone.contents = this.pageContents
				const newPageItem = new Page(pageItemClone)
				await objectStore.updateObject('page', newPageItem.id, newPageItem)
				this.saveContentsSuccess = true
				setTimeout(() => {
					this.saveContentsSuccess = false
				}, 1500)
			} finally {
				this.saveContentsLoading = false
			}
		},
		/**
		 * Open a link in a new window
		 * @param {string} url - The URL to open
		 * @param {string} [type] - The window type
		 * @return {void}
		 */
		openLink(url, type = '') {
			window.open(url, type)
		},
		onActionButtonClick(page, action) {
			objectStore.setActiveObject('page', page)
			switch (action) {
			case 'edit':
				navigationStore.setModal('page')
				break
			case 'addContent':
				navigationStore.setModal('pageContentForm')
				break
			case 'copyObject':
			case 'deleteObject':
				navigationStore.setDialog(action, { objectType: 'page', dialogTitle: 'Pagina' })
				break
			}
		},
		onContentActionButtonClick(pageContent, action) {
			objectStore.setActiveObject('pageContent', pageContent)
			switch (action) {
			case 'edit':
				navigationStore.setModal('pageContentForm')
				break
			case 'delete':
				this.deleteContent(pageContent.id)
				break
			}
		},
	},
}
</script>

<style scoped>
.pageDetail {
	padding: 20px;
}

.pageDetailHeader {
	display: flex;
	justify-content: space-between;
	align-items: center;
	margin-bottom: 20px;
}

.pageDetailContent {
	margin-bottom: 20px;
}

.pageDetailGrid {
	display: grid;
	grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
	gap: 20px;
}

.pageDetailTabTitle {
	display: flex;
	justify-content: center;
	align-items: center;
	position: relative;
}

.pageDetailTabIcon {
	position: absolute;
	right: 0;
}

.pageDetailDraggableItem {
	display: flex;
	align-items: center;
	gap: 3px;
	background-color: rgba(255, 255, 255, 0.05);
	padding: 4px;
	border-radius: 8px;
	margin-block: 8px;
}

.pageDetailDraggableItem.light {
	background-color: rgba(0, 0, 0, 0.05);
}

.pageDetailDragHandle {
	cursor: move;
}
</style>
