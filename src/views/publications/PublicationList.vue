<script setup>
import { store } from '../../store.js'
</script>

<template>
	<NcAppContentList>
		<ul>
			<div class="listHeader">
				<NcTextField class="searchField"
					:value.sync="store.search"
					label="Search"
					trailing-button-icon="close"
					:show-trailing-button="search !== ''"
					@trailing-button-click="store.setSearch('')">
					<Magnify :size="20" />
				</NcTextField>
				<NcActions>
					<NcActionButton @click="fetchData">
						<template #icon>
							<Refresh :size="20" />
						</template>
						Ververs
					</NcActionButton>
					<NcActionButton @click="store.setModal('publicationAdd')">
						<template #icon>
							<Plus :size="20" />
						</template>
						Publicatie toevoegen
					</NcActionButton>
				</NcActions>
			</div>
			<div v-if="!loading">
				<NcListItem v-for="(publication, i) in store.publicationList.results"
					:key="`${publication}${i}`"
					:name="publication?.title"
					:bold="false"
					:force-display-actions="true"
					:active="store.publicationItem.id === publication.id"
					:details="publication?.license"
					:counter-number="1"
					@click="store.setPublicationItem(publication)">
					<template #icon>
						<ListBoxOutline :class="store.publicationItem.id === publication.id && 'selectedZaakIcon'"
							disable-menu
							:size="44" />
					</template>
					<template #subname>
						{{ publication?.description }}
					</template>
					<template #actions>
						<NcActionButton @click="store.setPublicationItem(publication); store.setModal('editPublication')">
							<template #icon>
								<Pencil :size="20" />
							</template>
							Bewerken
						</NcActionButton>
						<NcActionButton>
							<template #icon>
								<PublishOff :size="20" />
							</template>
							Depubliceren
						</NcActionButton>
						<NcActionButton>
							<template #icon>
								<FileTreeOutline :size="20" />
							</template>
							Eigenschap toevoegen
						</NcActionButton>
						<NcActionButton>
							<template #icon>
								<FilePlusOutline :size="20" />
							</template>
							Bijlage toevoegen
						</NcActionButton>
						<NcActionButton class="publicationsList-actionsDelete" @click="store.setPublicationItem(publication); store.setDialog('deletePublication')">
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
				name="Zaken aan het laden" />
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
	watch: {
		search: {
			handler(search) {
				this.fetchData()
			},
		},
	},
	mounted() {
		this.fetchData()
	},
	methods: {
		fetchData() {
			this.loading = true
			store.refreshPublicationList()
			this.loading = false
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

.active.publicationDetails-actionsDelete {
    background-color: var(--color-error) !important;
}
.active.publicationDetails-actionsDelete button {
    color: #EBEBEB !important;
}
</style>
