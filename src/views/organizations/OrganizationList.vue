/**
 * OrganizationList.vue
 * Component for displaying a list of organizations
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
					:value="objectStore.getSearchTerm('organization')"
					label="Zoeken"
					trailing-button-icon="close"
					:show-trailing-button="objectStore.getSearchTerm('organization') !== ''"
					@update:value="(value) => objectStore.setSearchTerm('organization', value)"
					@trailing-button-click="objectStore.clearSearchTerm('organization')">
					<Magnify :size="20" />
				</NcTextField>
				<NcActions>
					<NcActionButton
						title="Bekijk de documentatie over organisaties"
						@click="openLink('https://conduction.gitbook.io/opencatalogi-nextcloud/beheerders/organisaties', '_blank')">
						<template #icon>
							<HelpCircleOutline :size="20" />
						</template>
						Help
					</NcActionButton>
					<NcActionButton close-after-click
						:disabled="objectStore.isLoading('organization')"
						@click="objectStore.fetchCollection('organization')">
						<template #icon>
							<Refresh :size="20" />
						</template>
						Ververs
					</NcActionButton>
					<NcActionButton close-after-click @click="openAddOrganizationModal">
						<template #icon>
							<Plus :size="20" />
						</template>
						Organisatie toevoegen
					</NcActionButton>
				</NcActions>
			</div>

			<div v-if="!objectStore.isLoading('organization')">
				<NcListItem v-for="(organization, i) in objectStore.getCollection('organization').results"
					:key="`${organization}${i}`"
					:name="organization.name"
					:details="organization.summary"
					:active="objectStore.getActiveObject('organization')?.id === organization?.id"
					:force-display-actions="true"
					@click="toggleActive(organization)">
					<template #icon>
						<OfficeBuilding :class="objectStore.getActiveObject('organization')?.id === organization.id && 'selectedZaakIcon'"
							disable-menu
							:size="44" />
					</template>
					<template #actions>
						<NcActionButton close-after-click @click="onActionButtonClick(organization, 'edit')">
							<template #icon>
								<Pencil :size="20" />
							</template>
							Bewerken
						</NcActionButton>
						<NcActionButton close-after-click @click="onActionButtonClick(organization, 'copyObject')">
							<template #icon>
								<ContentCopy :size="20" />
							</template>
							Kopiëren
						</NcActionButton>
						<NcActionButton close-after-click @click="onActionButtonClick(organization, 'deleteObject')">
							<template #icon>
								<Delete :size="20" />
							</template>
							Verwijderen
						</NcActionButton>
					</template>
				</NcListItem>
			</div>

			<NcLoadingIcon v-if="objectStore.isLoading('organization')"
				:size="64"
				class="loadingIcon"
				appearance="dark"
				name="Organisaties aan het laden" />

			<div v-if="!objectStore.getCollection('organization').results.length" class="emptyListHeader">
				Er zijn nog geen organisaties gedefinieerd.
			</div>
		</ul>
	</NcAppContentList>
</template>

<script>
import { NcListItem, NcActionButton, NcAppContentList, NcTextField, NcLoadingIcon, NcActions } from '@nextcloud/vue'
import Magnify from 'vue-material-design-icons/Magnify.vue'
import OfficeBuilding from 'vue-material-design-icons/OfficeBuilding.vue'
import Plus from 'vue-material-design-icons/Plus.vue'
import Pencil from 'vue-material-design-icons/Pencil.vue'
import Delete from 'vue-material-design-icons/Delete.vue'
import Refresh from 'vue-material-design-icons/Refresh.vue'
import HelpCircleOutline from 'vue-material-design-icons/HelpCircleOutline.vue'
import ContentCopy from 'vue-material-design-icons/ContentCopy.vue'

export default {
	name: 'OrganizationList',
	components: {
		NcListItem,
		NcActionButton,
		NcAppContentList,
		NcTextField,
		NcLoadingIcon,
		NcActions,
		// Icons
		Magnify,
		OfficeBuilding,
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
		toggleActive(organization) {
			objectStore.getActiveObject('organization')?.id === organization?.id ? objectStore.clearActiveObject('organization') : objectStore.setActiveObject('organization', organization)
		},
		openAddOrganizationModal() {
			navigationStore.setModal('organization')
			objectStore.clearActiveObject('organization')
		},
		onActionButtonClick(organization, action) {
			objectStore.setActiveObject('organization', organization)
			switch (action) {
			case 'edit':
				navigationStore.setModal('organization')
				break
			case 'copyObject':
			case 'deleteObject':
				navigationStore.setDialog(action, { objectType: 'organization', dialogTitle: 'Organisatie' })
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
