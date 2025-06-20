/**
 * GlossaryList.vue
 * Component for displaying glossary items
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
					:value="objectStore.getSearchTerm('glossary')"
					label="Zoeken"
					trailing-button-icon="close"
					:show-trailing-button="objectStore.getSearchTerm('glossary') !== ''"
					@update:value="(value) => objectStore.setSearchTerm('glossary', value)"
					@trailing-button-click="objectStore.clearSearch('glossary')">
					<Magnify :size="20" />
				</NcTextField>
				<NcActions>
					<NcActionButton
						title="Bekijk de documentatie over glossaries"
						@click="openLink('https://conduction.gitbook.io/opencatalogi-nextcloud/beheerders/glossaries', '_blank')">
						<template #icon>
							<HelpCircleOutline :size="20" />
						</template>
						Help
					</NcActionButton>
					<NcActionButton close-after-click
						:disabled="objectStore.isLoading('glossary')"
						@click="objectStore.fetchCollection('glossary')">
						<template #icon>
							<Refresh :size="20" />
						</template>
						Ververs
					</NcActionButton>
					<NcActionButton close-after-click @click="objectStore.clearActiveObject('glossary'); navigationStore.setModal('glossary')">
						<template #icon>
							<Plus :size="20" />
						</template>
						Term toevoegen
					</NcActionButton>
				</NcActions>
			</div>

			<div v-if="!objectStore.isLoading('glossary')">
				<NcListItem v-for="(glossary, i) in objectStore.getCollection('glossary').results"
					:key="`${glossary}${i}`"
					:name="glossary.title"
					:details="glossary.published ? 'Publiek vindbaar' : 'Niet publiek vindbaar'"
					:active="objectStore.getActiveObject('glossary')?.id === glossary?.id"
					:counter-number="glossary.relatedTerms?.length || '0'"
					:force-display-actions="true"
					@click="objectStore.getActiveObject('glossary')?.id === glossary?.id ? objectStore.clearActiveObject('glossary') : objectStore.setActiveObject('glossary', glossary)">
					<template #icon>
						<BookOpenOutline :class="objectStore.getActiveObject('glossary')?.id === glossary.id && 'selectedZaakIcon'"
							disable-menu
							:size="44" />
					</template>
					<template #subname>
						{{ glossary?.description }}
					</template>
					<template #actions>
						<NcActionButton close-after-click @click="objectStore.setActiveObject('glossary', glossary); navigationStore.setModal('glossary')">
							<template #icon>
								<Pencil :size="20" />
							</template>
							Bewerken
						</NcActionButton>
						<NcActionButton close-after-click @click="objectStore.setActiveObject('glossary', glossary); navigationStore.setDialog('copyObject', { objectType: 'glossary', dialogTitle: 'Term'})">
							<template #icon>
								<ContentCopy :size="20" />
							</template>
							Kopiëren
						</NcActionButton>
						<NcActionButton close-after-click @click="objectStore.setActiveObject('glossary', glossary); navigationStore.setDialog('deleteObject', { objectType: 'glossary', dialogTitle: 'Term'})">
							<template #icon>
								<Delete :size="20" />
							</template>
							Verwijder
						</NcActionButton>
					</template>
				</NcListItem>
			</div>

			<NcLoadingIcon v-if="objectStore.isLoading('glossary')"
				class="loadingIcon"
				:size="64"
				appearance="dark"
				name="Termen aan het laden" />

			<div v-if="!objectStore.getCollection('glossary').results.length" class="emptyListHeader">
				Er zijn nog geen termen gedefinieerd.
			</div>
		</ul>
	</NcAppContentList>
</template>

<script>
import { NcListItem, NcActionButton, NcAppContentList, NcTextField, NcLoadingIcon, NcActions } from '@nextcloud/vue'
import Magnify from 'vue-material-design-icons/Magnify.vue'
import BookOpenOutline from 'vue-material-design-icons/BookOpenOutline.vue'
import Plus from 'vue-material-design-icons/Plus.vue'
import Pencil from 'vue-material-design-icons/Pencil.vue'
import Delete from 'vue-material-design-icons/Delete.vue'
import Refresh from 'vue-material-design-icons/Refresh.vue'
import HelpCircleOutline from 'vue-material-design-icons/HelpCircleOutline.vue'
import ContentCopy from 'vue-material-design-icons/ContentCopy.vue'

export default {
	name: 'GlossaryList',
	components: {
		NcListItem,
		NcActions,
		NcActionButton,
		NcAppContentList,
		NcTextField,
		NcLoadingIcon,
		HelpCircleOutline,
		BookOpenOutline,
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
