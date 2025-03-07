<script setup>
import { navigationStore, pageStore } from '../../store/store.js'
import { getTheme } from '../../services/getTheme.js'
import { EventBus } from '../../eventBus.js'
</script>

<template>
	<div class="detailContainer">
		<div class="head">
			<h1 class="h1">
				{{ pageStore.pageItem.name }}
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
				<NcActionButton @click="navigationStore.setModal('pageForm')">
					<template #icon>
						<Pencil :size="20" />
					</template>
					Bewerken
				</NcActionButton>
				<NcActionButton @click="navigationStore.setDialog('copyPage')">
					<template #icon>
						<ContentCopy :size="20" />
					</template>
					Kopiëren
				</NcActionButton>
				<NcActionButton @click="navigationStore.setModal('addPageContents')">
					<template #icon>
						<Plus :size="20" />
					</template>
					Content toevoegen
				</NcActionButton>
				<NcActionButton @click="navigationStore.setModal('deletePage')">
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
					<b>Name:</b>
					<span>{{ pageStore.pageItem.name }}</span>
				</div>
				<div>
					<b>Slug:</b>
					<span>{{ pageStore.pageItem.slug }}</span>
				</div>
				<div>
					<b>Laatst bijgewerkt:</b>
					<span>{{ pageStore.pageItem.updatedAt }}</span>
				</div>
			</div>
		</div>
		<div class="tabContainer">
			<BTabs content-class="mt-3" justified>
				<BTab active>
					<template #title>
						<div class="tabTitleLoadingContainer">
							<p>Data</p>
							<NcLoadingIcon v-if="saveContentsLoading" class="tabTitleIcon" :size="24" />
							<CheckCircleOutline v-if="saveContentsSuccess" class="tabTitleIcon" :size="24" />
						</div>
					</template>

					<!-- if menu has items -->
					<div v-if="pageContents.length > 0">
						<!-- show draggable list -->
						<VueDraggable v-model="pageContents" easing="ease-in-out">
							<!-- show a div which is draggable for each item -->
							<div v-for="(pageContent, i) in pageContents" :key="i" :class="`draggable-list-item ${getTheme()}`">
								<!-- show a drag handle and NcListItem -->
								<Drag class="drag-handle" :size="40" />
								<NcListItem :name="pageContent.type"
									:bold="false"
									:force-display-actions="true">
									<template #subname>
										{{ JSON.stringify(pageContent.data) }}
									</template>
									<template #actions>
										<NcActionButton :disabled="saveContentsLoading"
											@click="pageStore.contentId = pageContent.id; navigationStore.setModal('addPageContents')">
											<template #icon>
												<Pencil :size="20" />
											</template>
											Bewerken
										</NcActionButton>
										<NcActionButton :disabled="saveContentsLoading"
											@click="deleteContent(pageContent.id)">
											<template #icon>
												<Delete :size="20" />
											</template>
											Verwijderen
										</NcActionButton>
									</template>
								</NcListItem>
							</div>
						</VueDraggable>

						<NcButton :disabled="(JSON.stringify(pageStore.pageItem.contents) === JSON.stringify(pageContents)) || saveContentsLoading"
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

/**
 * Component for displaying and managing page details
 */
export default {
	name: 'PageDetail',
	components: {
		// Components
		NcLoadingIcon,
		NcActionButton,
		NcActions,
		NcButton,
		VueDraggable,
		// Bootstrap
		BTabs,
		BTab,
		// Icons
		DotsHorizontal,
		Pencil,
		Delete,
		ContentCopy,
		HelpCircleOutline,
		Drag,
	},
	data() {
		return {
			pageContents: [],
			saveContentsLoading: false,
			saveContentsSuccess: false,
			loading: false,
			upToDate: false,
		}
	},
	computed: {
		pageItemId() {
			return pageStore.pageItem?.id
		},
	},
	watch: {
		pageItemId: {
			handler() {
				// fetch up-to-date data on id change
				this.fetchData()
			},
			immediate: true,
		},
	},
	created() {
		// Listen for the event that gets emitted when the page content is saved or deleted
		EventBus.$on(['edit-page-content-success', 'delete-page-content-success'], () => {
			this.fetchData()
		})
	},
	beforeDestroy() {
		// Clean up the event listener
		EventBus.$off(['edit-page-content-success', 'delete-page-content-success'])
	},
	mounted() {
		this.pageContents = pageStore.pageItem.contents
		// fetch up-to-date data on mount
		this.fetchData()
	},
	methods: {
		fetchData() {
			pageStore.getOnePage(pageStore.pageItem.id)
				.then(({ data }) => {
					this.pageContents = data.contents
				})
		},
		deleteContent(contentId) {
			const newContents = pageStore.pageItem.contents.filter((content) => content.id !== contentId)

			const newPageItem = new Page({
				...pageStore.pageItem,
				contents: newContents,
			})

			pageStore.savePage(newPageItem)
				.then(({ response }) => {
					this.fetchData()
				})
				.catch((err) => {
					this.error = err
					this.loading = false
					this.hasUpdated = false
				})
				.finally(() => {
					this.loading = false
				})
		},
		savePageContents() {
			this.saveContentsLoading = true
			this.saveContentsSuccess = false

			const pageItemClone = _.cloneDeep(pageStore.pageItem)

			pageItemClone.contents = this.pageContents

			const newPageItem = new Page(pageItemClone)

			pageStore.savePage(newPageItem)
				.then(() => {
					this.saveContentsSuccess = true
					setTimeout(() => {
						this.saveContentsSuccess = false
					}, 1500)
				})
				.finally(() => {
					this.saveContentsLoading = false
				})
		},
		openLink(url, type = '') {
			window.open(url, type)
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
