/**
 * PageList.vue
 * Component for displaying pages
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
					:value="objectStore.getSearchTerm('page')"
					label="Zoeken"
					trailing-button-icon="close"
					:show-trailing-button="objectStore.getSearchTerm('page') !== ''"
					@update:value="(value) => objectStore.setSearchTerm('page', value)"
					@trailing-button-click="objectStore.clearSearchTerm('page')">
					<Magnify :size="20" />
				</NcTextField>
				<NcActions>
					<NcActionButton
						title="Bekijk de documentatie over pagina's"
						@click="openLink('https://conduction.gitbook.io/opencatalogi-nextcloud/beheerders/pages', '_blank')">
						<template #icon>
							<HelpCircleOutline :size="20" />
						</template>
						Help
					</NcActionButton>
					<NcActionButton close-after-click
						:disabled="objectStore.isLoading('page')"
						@click="objectStore.fetchCollection('page')">
						<template #icon>
							<Refresh :size="20" />
						</template>
						Ververs
					</NcActionButton>
					<NcActionButton close-after-click @click="openAddPageModal">
						<template #icon>
							<Plus :size="20" />
						</template>
						Pagina toevoegen
					</NcActionButton>
				</NcActions>
			</div>

			<div v-if="!objectStore.isLoading('page')">
				<NcListItem v-for="(page, i) in objectStore.getCollection('page').results"
					:key="`${page}${i}`"
					:name="page.title"
					:bold="false"
					:force-display-actions="true"
					:active="objectStore.getActiveObject('page')?.id === page?.id"
					:details="page.slug"
					:counter-number="page.contents?.length || '0'"
					@click="toggleActive(page)">
					<template #icon>
						<Web :class="objectStore.getActiveObject('page')?.id === page.id && 'selectedZaakIcon'"
							disable-menu
							:size="44" />
					</template>
					<template #subname>
						{{ page?.description }}
					</template>
					<template #actions>
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
							Verwijder
						</NcActionButton>
					</template>
				</NcListItem>
			</div>

			<NcLoadingIcon v-if="objectStore.isLoading('page')"
				class="loadingIcon"
				:size="64"
				appearance="dark"
				name="Pagina's aan het laden" />

			<div v-if="!objectStore.getCollection('page').results.length" class="emptyListHeader">
				Er zijn nog geen pagina's gedefinieerd.
			</div>
		</ul>
	</NcAppContentList>
</template>

<script>
import { NcListItem, NcActionButton, NcAppContentList, NcTextField, NcLoadingIcon, NcActions } from '@nextcloud/vue'
import Magnify from 'vue-material-design-icons/Magnify.vue'
import Web from 'vue-material-design-icons/Web.vue'
import Plus from 'vue-material-design-icons/Plus.vue'
import Pencil from 'vue-material-design-icons/Pencil.vue'
import Delete from 'vue-material-design-icons/Delete.vue'
import Refresh from 'vue-material-design-icons/Refresh.vue'
import HelpCircleOutline from 'vue-material-design-icons/HelpCircleOutline.vue'
import ContentCopy from 'vue-material-design-icons/ContentCopy.vue'

export default {
	name: 'PageList',
	components: {
		NcListItem,
		NcActions,
		NcActionButton,
		NcAppContentList,
		NcTextField,
		NcLoadingIcon,
		HelpCircleOutline,
		Web,
		Magnify,
		Refresh,
		Plus,
		Pencil,
		Delete,
		ContentCopy,
	},
	methods: {
		openLink(url, type = '') {
			window.open(url, type)
		},
		toggleActive(page) {
			objectStore.getActiveObject('page')?.id === page?.id ? objectStore.clearActiveObject('page') : objectStore.setActiveObject('page', page)
		},
		openAddPageModal() {
			navigationStore.setModal('page')
			objectStore.clearActiveObject('page')
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
    display: flex;
    flex-direction: row;
    align-items: center;
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
