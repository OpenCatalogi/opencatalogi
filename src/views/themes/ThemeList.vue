<script setup>
import { navigationStore, objectStore } from '../../store/store.js'
</script>

<template>
	<NcAppContentList>
		<ul>
			<div class="listHeader">
				<NcTextField class="searchField"
					:value="objectStore.getSearchTerm('theme')"
					label="Zoeken"
					trailing-button-icon="close"
					:show-trailing-button="objectStore.getSearchTerm('theme') !== ''"
					@update:value="(value) => objectStore.setSearchTerm('theme', value)"
					@trailing-button-click="objectStore.clearSearchTerm('theme')">
					<Magnify :size="20" />
				</NcTextField>
				<NcActions>
					<NcActionButton
						title="Bekijk de documentatie over themas"
						@click="openLink('https://conduction.gitbook.io/opencatalogi-nextcloud/beheerders/themas', '_blank')">
						<template #icon>
							<HelpCircleOutline :size="20" />
						</template>
						Help
					</NcActionButton>
					<NcActionButton close-after-click
						:disabled="objectStore.isLoading('theme')"
						@click="objectStore.fetchCollection('theme')">
						<template #icon>
							<Refresh :size="20" />
						</template>
						Ververs
					</NcActionButton>
					<NcActionButton close-after-click @click="openAddThemeModal">
						<template #icon>
							<Plus :size="20" />
						</template>
						Thema toevoegen
					</NcActionButton>
				</NcActions>
			</div>

			<div v-if="!objectStore.isLoading('theme')">
				<NcListItem v-for="(theme, i) in objectStore.getCollection('theme').results"
					:key="`${theme}${i}`"
					:name="theme.title"
					:bold="false"
					:force-display-actions="true"
					:active="objectStore.getActiveObject('theme')?.id === theme?.id"
					:details="theme?.status"
					@click="toggleActive(theme)">
					<template #icon>
						<ShapeOutline :class="objectStore.getActiveObject('theme')?.id === theme.id && 'selectedZaakIcon'"
							disable-menu
							:size="44" />
					</template>
					<template #actions>
						<NcActionButton close-after-click @click="objectStore.setActiveObject('theme', theme); navigationStore.setModal('theme')">
							<template #icon>
								<Pencil :size="20" />
							</template>
							Bewerken
						</NcActionButton>
						<NcActionButton close-after-click @click="objectStore.setActiveObject('theme', theme); navigationStore.setDialog('copyObject', { objectType: 'theme', dialogTitle: 'Theme'})">
							<template #icon>
								<ContentCopy :size="20" />
							</template>
							Kopiëren
						</NcActionButton>
						<NcActionButton close-after-click @click="objectStore.setActiveObject('theme', theme); navigationStore.setDialog('deleteObject', { objectType: 'theme', dialogTitle: 'Theme'})">
							<template #icon>
								<Delete :size="20" />
							</template>
							Verwijderen
						</NcActionButton>
					</template>
				</NcListItem>
			</div>

			<NcLoadingIcon v-if="objectStore.isLoading('theme')"
				:size="64"
				class="loadingIcon"
				appearance="dark"
				name="Themas aan het laden" />

			<div v-if="!objectStore.getCollection('theme').results.length" class="emptyListHeader">
				Er zijn nog geen thema's gedefinieerd.
			</div>
		</ul>
	</NcAppContentList>
</template>

<script>
import { NcListItem, NcActionButton, NcAppContentList, NcTextField, NcLoadingIcon, NcActions } from '@nextcloud/vue'
import Magnify from 'vue-material-design-icons/Magnify.vue'
import ShapeOutline from 'vue-material-design-icons/ShapeOutline.vue'
import Plus from 'vue-material-design-icons/Plus.vue'
import Pencil from 'vue-material-design-icons/Pencil.vue'
import Delete from 'vue-material-design-icons/Delete.vue'
import Refresh from 'vue-material-design-icons/Refresh.vue'
import HelpCircleOutline from 'vue-material-design-icons/HelpCircleOutline.vue'
import ContentCopy from 'vue-material-design-icons/ContentCopy.vue'

export default {
	name: 'ThemeList',
	components: {
		NcListItem,
		NcActionButton,
		NcAppContentList,
		NcTextField,
		NcLoadingIcon,
		NcActions,
		// Icons
		Magnify,
		ShapeOutline,
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
		toggleActive(theme) {
			objectStore.getActiveObject('theme')?.id === theme?.id ? objectStore.clearActiveObject('theme') : objectStore.setActiveObject('theme', theme)
		},
		openAddThemeModal() {
			navigationStore.setModal('theme')
			objectStore.clearActiveObject('theme')
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
