<script setup>
import { navigationStore, themeStore } from '../../store/store.js'
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
						title="Bekijk de documentatie over themas"
						@click="openLink('https://conduction.gitbook.io/opencatalogi-nextcloud/beheerders/themas')">
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
					<NcActionButton @click="navigationStore.setModal('themeAdd')">
						<template #icon>
							<Plus :size="20" />
						</template>
						Thema toevoegen
					</NcActionButton>
				</NcActions>
			</div>
			<div v-if="!loading">
				<NcListItem v-for="(theme, i) in filteredThemes"
					:key="`${theme}${i}`"
					:name="theme.title"
					:bold="false"
					:force-display-actions="true"
					:active="themeStore.themeItem?.id === theme.id"
					:details="theme?.status"
					@click="setActive(theme)">
					<template #icon>
						<ShapeOutline :size="44" />
					</template>
					<template #subname>
						{{ theme?.summary }}
					</template>
					<template #actions>
						<NcActionButton @click="themeStore.setThemeItem(theme); navigationStore.setModal('editTheme')">
							<template #icon>
								<Pencil :size="20" />
							</template>
							Bewerken
						</NcActionButton>
						<NcActionButton @click="themeStore.setThemeItem(theme); navigationStore.setDialog('copyTheme')">
							<template #icon>
								<ContentCopy :size="20" />
							</template>
							Kopiëren
						</NcActionButton>
						<NcActionButton @click="themeStore.setThemeItem(theme); navigationStore.setDialog('deleteTheme')">
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
				name="Themas aan het laden" />

			<div v-if="!filteredThemes.length" class="emptyListHeader">
				Er zijn nog geen thema's gedefinieerd.
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
import ShapeOutline from 'vue-material-design-icons/ShapeOutline.vue'

export default {
	name: 'ThemeList',
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
		ShapeOutline,
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
		filteredThemes() {
			if (!themeStore?.themeList) return []
			return themeStore.themeList.filter((theme) => {
				return theme
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
			themeStore.refreshThemeList(search)
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
		setActive(theme) {
			if (JSON.stringify(themeStore.themeItem) === JSON.stringify(theme)) {
				themeStore.setThemeItem(false)
			} else { themeStore.setThemeItem(theme) }
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

.active.themeDetails-actionsDelete {
    background-color: var(--color-error) !important;
}
.active.themeDetails-actionsDelete button {
    color: #EBEBEB !important;
}

.loadingIcon {
    margin-block-start: var(--OC-margin-20);
}
</style>
