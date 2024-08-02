<script setup>
import { navigationStore, publicationStore } from '../../store/store.js'
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
					<NcActionButton :disabled="loading" @click="fetchData">
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
					:active="publicationStore.publicationItem.id === publication.id"
					:details="publication?.status"
					:counter-number="publication?.attachmentCount.toString()"
					@click="publicationStore.setPublicationItem(publication)">
					<template #icon>
						<ListBoxOutline v-if="publication.status === 'published'" :size="44" />
						<ArchiveOutline v-if="publication.status === 'archived'" :size="44" />
						<Pencil v-if="publication.status === 'concept'" :size="44" />
						<AlertOutline v-if="publication.status === 'retracted'" :size="44" />
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
						<NcActionButton v-if="publication.status !== 'published'" @click="publicationStore.setPublicationItem(publication); navigationStore.setDialog('publishPublication')">
							<template #icon>
								<Publish :size="20" />
							</template>
							Publiceren
						</NcActionButton>
						<NcActionButton v-if="publication.status === 'published'" @click="publicationStore.setPublicationItem(publication); navigationStore.setDialog('depublishPublication')">
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
		</ul>
	</NcAppContentList>
</template>
<script>
import { NcListItem, NcActionButton, NcAppContentList, NcTextField, NcLoadingIcon, NcActions } from '@nextcloud/vue'
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
import { debounce } from 'lodash'

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
			search: '',
		}
	},
	computed: {
		filteredPublications() {
			if (!publicationStore?.publicationList) return []
			return publicationStore.publicationList.filter((publication) => {
				return publication.catalogi.toString() === navigationStore.selectedCatalogus.toString()
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
		fetchData(search = null) {
			this.loading = true
			publicationStore.refreshPublicationList(search)
				.then(() => {
					this.loading = false
				})
		},
		debouncedFetchData: debounce(function(search) {
			this.fetchData(search)
		}, 500),
	},
	beforeRouteLeave(to, from, next) {
		search = ''
		next()
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
