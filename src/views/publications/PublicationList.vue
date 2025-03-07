<script setup>
import { navigationStore, publicationStore } from '../../store/store.js'
</script>

<template>
	<NcAppContentList>
		<ul>
			<div class="listHeader">
				<NcTextField class="searchField"
					:value.sync="advancedSearch"
					label="Zoeken"
					trailing-button-icon="close"
					:show-trailing-button="advancedSearch !== ''"
					@trailing-button-click="advancedSearch = ''">
					<Magnify :size="20" />
				</NcTextField>
				<NcActions>
					<NcActionCaption name="Zoeken" />
					<NcActionCheckbox
						:checked="conceptChecked"
						:value="'concept'"
						@change="handleCheckboxChange('concept', $event)">
						Concept
					</NcActionCheckbox>
					<NcActionCheckbox
						:checked="gepubliceerdChecked"
						:value="'gepubliceerd'"
						@change="handleCheckboxChange('gepubliceerd', $event)">
						Gepubliceerd
					</NcActionCheckbox>
					<NcActionSeparator />
					<NcActionCaption name="Sorteren" />
					<NcActionInput
						v-model="sortField"
						type="multiselect"
						input-label="Eigenschap"
						:options="['Titel', 'Datum gepubliceerd', 'Datum aangepast']">
						<template #icon>
							<Pencil :size="20" />
						</template>
						Kies een eigenschap
					</NcActionInput>
					<NcActionRadio
						:checked="sortDirection === 'asc'"
						name="sortDirection"
						value="asc"
						@update:checked="updateSortOrder('asc')">
						Oplopend
					</NcActionRadio>
					<NcActionRadio
						:checked="sortDirection === 'desc'"
						name="sortDirection"
						value="desc"
						@update:checked="updateSortOrder('desc')">
						Aflopend
					</NcActionRadio>
					<NcActionSeparator />
					<NcActionCaption name="Acties" />
					<NcActionButton
						title="Bekijk de documentatie over publicaties"
						@click="openLink('https://conduction.gitbook.io/opencatalogi-nextcloud/gebruikers/publicaties', '_blank')">
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
					<NcActionButton @click="navigationStore.setModal('publicationAdd')">
						<template #icon>
							<Plus :size="20" />
						</template>
						Publicatie toevoegen
					</NcActionButton>
				</NcActions>
			</div>
			<div v-if="!loading">
				<NcListItem v-for="(publication, i) in filteredPublications"
					:key="`${publication}${i}`"
					:name="publication.title"
					:bold="false"
					:force-display-actions="true"
					:active="publicationStore.publicationItem?.id === publication.id"
					:details="publication?.status"
					@click="setActive(publication)">
					<template #icon>
						<ListBoxOutline v-if="_.upperFirst(publication.status) === 'Published'" :size="44" />
						<ArchiveOutline v-if="_.upperFirst(publication.status) === 'Archived'" :size="44" />
						<Pencil v-if="_.upperFirst(publication.status) === 'Concept'" :size="44" />
						<AlertOutline v-if="_.upperFirst(publication.status) === 'Withdrawn'" :size="44" />
					</template>
					<template #subname>
						{{ publication?.summary }}
					</template>
					<template #actions>
						<NcActionButton @click="publicationStore.setPublicationItem(publication); navigationStore.setModal('editPublication')">
							<template #icon>
								<Pencil :size="20" />
							</template>
							Bewerken
						</NcActionButton>
						<NcActionButton @click="publicationStore.setPublicationItem(publication); navigationStore.setDialog('copyPublication')">
							<template #icon>
								<ContentCopy :size="20" />
							</template>
							Kopiëren
						</NcActionButton>
						<NcActionButton v-if="_.upperFirst(publication.status) !== 'Published'" @click="publicationStore.setPublicationItem(publication); navigationStore.setDialog('publishPublication')">
							<template #icon>
								<Publish :size="20" />
							</template>
							Publiceren
						</NcActionButton>
						<NcActionButton v-if="_.upperFirst(publication.status) === 'Published'" @click="publicationStore.setPublicationItem(publication); navigationStore.setDialog('depublishPublication')">
							<template #icon>
								<PublishOff :size="20" />
							</template>
							Depubliceren
						</NcActionButton>
						<NcActionButton @click="publicationStore.setPublicationItem(publication); navigationStore.setDialog('archivePublication')">
							<template #icon>
								<ArchivePlusOutline :size="20" />
							</template>
							Archiveren
						</NcActionButton>
						<NcActionButton @click="publicationStore.setPublicationItem(publication); navigationStore.setModal('addPublicationData')">
							<template #icon>
								<FileTreeOutline :size="20" />
							</template>
							Eigenschap toevoegen
						</NcActionButton>
						<NcActionButton @click="publicationStore.setPublicationItem(publication); navigationStore.setModal('AddAttachment')">
							<template #icon>
								<FilePlusOutline :size="20" />
							</template>
							Bijlage toevoegen
						</NcActionButton>
						<NcActionButton @click="navigationStore.setModal('addPublicationTheme')">
							<template #icon>
								<ShapeOutline :size="20" />
							</template>
							Thema toevoegen
						</NcActionButton>
						<NcActionButton class="publicationsList-actionsDelete" @click="publicationStore.setPublicationItem(publication); navigationStore.setDialog('deletePublication')">
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
				name="Publicaties aan het laden" />

			<div v-if="!filteredPublications.length" class="emptyListHeader">
				Er zijn nog geen publicaties gedefinieerd.
			</div>
		</ul>
	</NcAppContentList>
</template>
<script>
import { NcListItem, NcActionButton, NcAppContentList, NcTextField, NcLoadingIcon, NcActionRadio, NcActionCheckbox, NcActionInput, NcActionCaption, NcActionSeparator, NcActions } from '@nextcloud/vue'
import _ from 'lodash'

// Icons
import Magnify from 'vue-material-design-icons/Magnify.vue'
import Refresh from 'vue-material-design-icons/Refresh.vue'
import Plus from 'vue-material-design-icons/Plus.vue'
import ListBoxOutline from 'vue-material-design-icons/ListBoxOutline.vue'
import Pencil from 'vue-material-design-icons/Pencil.vue'
import Delete from 'vue-material-design-icons/Delete.vue'
import PublishOff from 'vue-material-design-icons/PublishOff.vue'
import FilePlusOutline from 'vue-material-design-icons/FilePlusOutline.vue'
import FileTreeOutline from 'vue-material-design-icons/FileTreeOutline.vue'
import ContentCopy from 'vue-material-design-icons/ContentCopy.vue'
import ArchiveOutline from 'vue-material-design-icons/ArchiveOutline.vue'
import AlertOutline from 'vue-material-design-icons/AlertOutline.vue'
import Publish from 'vue-material-design-icons/Publish.vue'
import ArchivePlusOutline from 'vue-material-design-icons/ArchivePlusOutline.vue'
import HelpCircleOutline from 'vue-material-design-icons/HelpCircleOutline.vue'
import ShapeOutline from 'vue-material-design-icons/ShapeOutline.vue'

export default {
	name: 'PublicationList',
	components: {
		NcListItem,
		NcActionButton,
		NcAppContentList,
		NcTextField,
		ListBoxOutline,
		Magnify,
		NcLoadingIcon,
		NcActionRadio,
		NcActionCheckbox,
		NcActionInput,
		NcActionCaption,
		NcActionSeparator,
		NcActions,
		// Icons
		Refresh,
		Plus,
		FilePlusOutline,
		FileTreeOutline,
		ContentCopy,
		ArchiveOutline,
		AlertOutline,
		Pencil,
		Publish,
		ArchivePlusOutline,
		HelpCircleOutline,
	},
	beforeRouteLeave(to, from, next) {
		this.advancedSearch = ''
		next()
	},
	data() {
		return {
			loading: false,
			sortField: '',
			sortDirection: 'desc',
			normalSearch: [],
			advancedSearch: '',
			conceptChecked: false,
			gepubliceerdChecked: false,
		}
	},
	computed: {
		filteredPublications() {
			if (!publicationStore?.publicationList) return []
			return publicationStore.publicationList.filter((publication) => {
				return (publication.catalogi?.id?.toString() ?? publication.catalog?.toString()) === navigationStore.selectedCatalogus?.toString()
			})
		},
	},
	watch: {
		advancedSearch: 'debouncedFetchData',
		sortField: 'fetchData',
		sortDirection: 'fetchData',
		normalSearch: 'fetchData',
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
			publicationStore.refreshPublicationList(this.normalSearch, this.advancedSearch, this.sortField, this.sortDirection)
				.then(() => {
					this.loading = false
				})
		},
		debouncedFetchData: _.debounce(function() {
			this.fetchData()
		}, 500),
		updateSortOrder(value) {
			this.sortDirection = value
		},
		updateNormalSearch() {
			this.normalSearch = []
			if (this.conceptChecked) {
				this.normalSearch.push({ key: 'status', value: 'Concept' })
			}
			if (this.gepubliceerdChecked) {
				this.normalSearch.push({ key: 'status', value: 'Published' })
			}
		},
		handleCheckboxChange(key, event) {
			const checked = event.target.checked

			if (key === 'concept') {
				this.conceptChecked = checked
			} else if (key === 'gepubliceerd') {
				this.gepubliceerdChecked = checked
			}
			this.updateNormalSearch()
		},
		setActive(publication) {
			if (JSON.stringify(publicationStore.publicationItem) === JSON.stringify(publication)) {
				publicationStore.setPublicationItem(false)
				publicationStore.setPublicationPublicationType(false)
			} else { publicationStore.setPublicationItem(publication) }
		},
	},
}
</script>
<style>
.listHeader {
	display: flex;
}

.refresh {
	margin-block-start: 11px !important;
    margin-block-end: 11px !important;
    margin-inline-end: 10px;
}

.active.publicationDetails-actionsDelete {
    background-color: var(--color-error) !important;
}
.active.publicationDetails-actionsDelete button {
    color: #EBEBEB !important;
}

.loadingIcon {
    margin-block-start: var(--OC-margin-20);
}
</style>
