<script setup>
import { navigationStore, publicationTypeStore } from '../../store/store.js'
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
						title="Bekijk de documentatie over publicatietypes"
						@click="openLink('https://conduction.gitbook.io/opencatalogi-nextcloud/beheerders/publicatietypes', '_blank')">
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
					<NcActionButton @click="navigationStore.setModal('addPublicationType')">
						<template #icon>
							<Plus :size="20" />
						</template>
						Publicatietype toevoegen
					</NcActionButton>
				</NcActions>
			</div>

			<div v-if="!loading">
				<NcListItem v-for="(publicationType, i) in publicationTypeStore.publicationTypeList"
					:key="`${publicationType}${i}`"
					:name="publicationType.title ?? publicationType.name"
					:active="publicationTypeStore.publicationTypeItem?.id === publicationType?.id"
					:details="publicationType.version ?? 'Onbekend'"
					:force-display-actions="true"
					@click="setActive(publicationType)">
					<template #icon>
						<FileTreeOutline :class="publicationTypeStore.publicationTypeItem?.id === publicationType?.id && 'selectedIcon'"
							disable-menu
							:size="44" />
					</template>
					<template #subname>
						{{ publicationType.description }}
					</template>
					<template #actions>
						<NcActionButton @click="publicationTypeStore.setPublicationTypeItem(publicationType); navigationStore.setModal('editPublicationType')">
							<template #icon>
								<Pencil :size="20" />
							</template>
							Bewerken
						</NcActionButton>
						<NcActionButton @click="publicationTypeStore.setPublicationTypeItem(publicationType); navigationStore.setDialog('copyPublicationType')">
							<template #icon>
								<ContentCopy :size="20" />
							</template>
							Kopiëren
						</NcActionButton>
						<NcActionButton @click="publicationTypeStore.setPublicationTypeItem(publicationType); navigationStore.setDialog('deletePublicationType')">
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
				name="Publicatietypes aan het laden" />

			<div v-if="!publicationTypeStore.publicationTypeList.length" class="emptyListHeader">
				Er zijn nog geen publicatietypes gedefinieerd.
			</div>
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
import HelpCircleOutline from 'vue-material-design-icons/HelpCircleOutline.vue'
import { debounce } from 'lodash'

export default {
	name: 'PublicationTypeList',
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
		setActive(publicationType) {
			if (JSON.stringify(publicationTypeStore.publicationTypeItem) === JSON.stringify(publicationType)) {
				publicationTypeStore.setPublicationTypeItem(false)
			} else { publicationTypeStore.setPublicationTypeItem(publicationType) }
		},
		refresh(e) {
			e.preventDefault()
			this.fetchData()
		},
		fetchData(search = null) {
			this.loading = true
			publicationTypeStore.refreshPublicationTypeList(search)
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
