<script setup>
import { navigationStore, metadataStore } from '../../store/store.js'
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
					<NcActionButton @click="navigationStore.setModal('addMetaData')">
						<template #icon>
							<Plus :size="20" />
						</template>
						Metadata toevoegen
					</NcActionButton>
				</NcActions>
			</div>

			<div v-if="!loading">
				<NcListItem v-for="(metaData, i) in metadataStore.metaDataList"
					:key="`${metaData}${i}`"
					:name="metaData.title ?? metaData.name"
					:active="metadataStore.metaDataItem?.id === metaData?.id"
					:details="metaData.version ?? 'Onbekend'"
					:force-display-actions="true"
					@click="metadataStore.setMetaDataItem(metaData)">
					<template #icon>
						<FileTreeOutline :class="metadataStore.metaDataItem?.id === metaData?.id && 'selectedIcon'"
							disable-menu
							:size="44" />
					</template>
					<template #subname>
						{{ metaData.description }}
					</template>
					<template #actions>
						<NcActionButton @click="metadataStore.setMetaDataItem(metaData); navigationStore.setModal('editMetaData')">
							<template #icon>
								<Pencil :size="20" />
							</template>
							Bewerken
						</NcActionButton>
						<NcActionButton @click="metadataStore.setMetaDataItem(metaData); navigationStore.setDialog('copyMetaData')">
							<template #icon>
								<ContentCopy :size="20" />
							</template>
							Kopiëren
						</NcActionButton>
						<NcActionButton @click="metadataStore.setMetaDataItem(metaData); navigationStore.setDialog('deleteMetaData')">
							<template #icon>
								<Delete :size="20" />
							</template>
							Verwijderen
						</NcActionButton>
					</template>
				</NcListItem>
			</div>

			<NcLoadingIcon v-if="loading"
				class="loadingIcon"
				:size="64"
				appearance="dark"
				name="Metadata aan het laden" />
		</ul>
	</NcAppContentList>
</template>
<script>
// Components
import { NcListItem, NcActionButton, NcAppContentList, NcTextField, NcLoadingIcon, NcActions } from '@nextcloud/vue'

// Icons
import Magnify from 'vue-material-design-icons/Magnify.vue'
import FileTreeOutline from 'vue-material-design-icons/FileTreeOutline.vue'
import Plus from 'vue-material-design-icons/Plus.vue'
import Pencil from 'vue-material-design-icons/Pencil.vue'
import ContentCopy from 'vue-material-design-icons/ContentCopy.vue'
import Delete from 'vue-material-design-icons/Delete.vue'
import Refresh from 'vue-material-design-icons/Refresh.vue'
import { debounce } from 'lodash'

export default {
	name: 'MetaDataList',
	components: {
		// Components
		NcListItem,
		NcActions,
		NcActionButton,
		NcAppContentList,
		NcTextField,
		NcLoadingIcon,
		// Icons
		FileTreeOutline,
		Magnify,
		Refresh,
		Plus,
		Pencil,
		ContentCopy,
		Delete,
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
			metadataStore.refreshMetaDataList(search)
				.then(() => {
					this.loading = false
				})
		},
		debouncedFetchData: debounce(function(search) {
			this.fetchData(search)
		}, 500),
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

.selecteIcon>svg {
    fill: white;
}

.loadingIcon {
    margin-block-start: var(--OC-margin-20);
}
</style>
