<script setup>
import { navigationStore, pageStore } from '../../store/store.js'
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
						title="Bekijk de documentatie over paginas"
						@click="openLink('https://conduction.gitbook.io/opencatalogi-nextcloud/beheerders/paginas')">
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
					<NcActionButton @click="pageStore.setPageItem(null); navigationStore.setModal('pageForm')">
						<template #icon>
							<Plus :size="20" />
						</template>
						Pagina toevoegen
					</NcActionButton>
				</NcActions>
			</div>
			<div v-if="!loading">
				<NcListItem v-for="(page, i) in filteredPages"
					:key="`${page}${i}`"
					:name="page.name"
					:bold="false"
					:force-display-actions="true"
					:active="pageStore.pageItem?.id === page.id"
					:details="page?.status"
					@click="setActive(page)">
					<template #icon>
						<Web :size="44" />
					</template>
					<template #subname>
						{{ page?.slug }}
					</template>
					<template #actions>
						<NcActionButton @click="pageStore.setPageItem(page); navigationStore.setModal('pageForm')">
							<template #icon>
								<Pencil :size="20" />
							</template>
							Bewerken
						</NcActionButton>
						<NcActionButton @click="pageStore.setPageItem(page); navigationStore.setDialog('copyPage')">
							<template #icon>
								<ContentCopy :size="20" />
							</template>
							Kopiëren
						</NcActionButton>
						<NcActionButton @click="pageStore.setPageItem(page); navigationStore.setModal('deletePage')">
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
				name="Paginas aan het laden" />

			<div v-if="!filteredPages.length" class="emptyListHeader">
				Er zijn nog geen pagina's gedefinieerd.
			</div>
		</ul>
	</NcAppContentList>
</template>
<script>
import { NcActionButton, NcActions, NcAppContentList, NcListItem, NcLoadingIcon, NcTextField } from '@nextcloud/vue'
import { debounce } from 'lodash'

// Icons
import ContentCopy from 'vue-material-design-icons/ContentCopy.vue'
import Delete from 'vue-material-design-icons/Delete.vue'
import HelpCircleOutline from 'vue-material-design-icons/HelpCircleOutline.vue'
import Magnify from 'vue-material-design-icons/Magnify.vue'
import Pencil from 'vue-material-design-icons/Pencil.vue'
import Plus from 'vue-material-design-icons/Plus.vue'
import Refresh from 'vue-material-design-icons/Refresh.vue'
import Web from 'vue-material-design-icons/Web.vue'

export default {
	name: 'PageList',
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
		ContentCopy,
		Web,
		Pencil,
		HelpCircleOutline,
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
		filteredPages() {
			if (!pageStore?.pageList) return []
			return pageStore.pageList.filter((page) => {
				return page
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
			pageStore.refreshPageList(search)
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
		setActive(page) {
			if (JSON.stringify(pageStore.pageItem) === JSON.stringify(page)) {
				pageStore.setPageItem(false)
			} else { pageStore.setPageItem(page) }
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

.active.pageDetails-actionsDelete {
    background-color: var(--color-error) !important;
}
.active.pageDetails-actionsDelete button {
    color: #EBEBEB !important;
}

.loadingIcon {
    margin-block-start: var(--OC-margin-20);
}
</style>
