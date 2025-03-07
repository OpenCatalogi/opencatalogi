<script setup>
import { catalogiStore, navigationStore } from '../../store/store.js'
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
						title="Bekijk de documentatie over catalogi"
						@click="openLink('https://conduction.gitbook.io/opencatalogi-nextcloud/beheerders/catalogi', '_blank')">
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
					<NcActionButton @click="navigationStore.setModal('addCatalog')">
						<template #icon>
							<Plus :size="20" />
						</template>
						Catalogus toevoegen
					</NcActionButton>
				</NcActions>
			</div>

			<div v-if="!loading">
				<NcListItem v-for="(catalogus, i) in catalogiStore.catalogiList"
					:key="`${catalogus}${i}`"
					:name="catalogus.title"
					:details="catalogus.listed ? 'Publiek vindbaar' : 'Niet publiek vindbaar'"
					:active="catalogiStore.catalogiItem?.id === catalogus?.id"
					:counter-number="catalogus.publicationTypes.length || '0'"
					:force-display-actions="true"
					@click="setActive(catalogus)">
					<template #icon>
						<DatabaseOutline :class="catalogiStore.catalogiItem?.id === catalogus.id && 'selectedZaakIcon'"
							disable-menu
							:size="44" />
					</template>
					<template #subname>
						{{ catalogus?.summary }}
					</template>
					<template #actions>
						<NcActionButton @click="catalogiStore.setCatalogiItem(catalogus); navigationStore.setModal('editCatalog')">
							<template #icon>
								<Pencil :size="20" />
							</template>
							Bewerken
						</NcActionButton>
						<NcActionButton @click="navigationStore.setSelected('publication'); navigationStore.setSelectedCatalogus(catalogus?.id)">
							<template #icon>
								<OpenInApp :size="20" />
							</template>
							Catalogus bekijken
						</NcActionButton>
						<NcActionButton @click="catalogiStore.setCatalogiItem(catalogus); navigationStore.setModal('addCatalogiPublicationType')">
							<template #icon>
								<Plus :size="20" />
							</template>
							Publicatietype toevoegen
						</NcActionButton>
						<NcActionButton @click="catalogiStore.setCatalogiItem(catalogus); navigationStore.setDialog('deleteCatalog')">
							<template #icon>
								<Delete :size="20" />
							</template>
							Verwijder
						</NcActionButton>
					</template>
				</NcListItem>
			</div>

			<NcLoadingIcon v-if="loading"
				class="loadingIcon"
				:size="64"
				appearance="dark"
				name="Zaken aan het laden" />

			<div v-if="!catalogiStore.catalogiList.length" class="emptyListHeader">
				Er zijn nog geen catalogi gedefinieerd.
			</div>
		</ul>
	</NcAppContentList>
</template>
<script>
// Components
import { NcListItem, NcActionButton, NcAppContentList, NcTextField, NcLoadingIcon, NcActions } from '@nextcloud/vue'

// Icons
import Magnify from 'vue-material-design-icons/Magnify.vue'
import DatabaseOutline from 'vue-material-design-icons/DatabaseOutline.vue'
import Plus from 'vue-material-design-icons/Plus.vue'
import Pencil from 'vue-material-design-icons/Pencil.vue'
import Delete from 'vue-material-design-icons/Delete.vue'
import Refresh from 'vue-material-design-icons/Refresh.vue'
import OpenInApp from 'vue-material-design-icons/OpenInApp.vue'
import HelpCircleOutline from 'vue-material-design-icons/HelpCircleOutline.vue'
import { debounce } from 'lodash'

export default {
	name: 'CatalogiList',
	components: {
		NcListItem,
		NcActions,
		NcActionButton,
		NcAppContentList,
		NcTextField,
		NcLoadingIcon,
		// Icons
		HelpCircleOutline,
		DatabaseOutline,
		Magnify,
		Refresh,
		Plus,
		Pencil,
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
			catalogi: [],
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
		refresh(e) {
			e.preventDefault()
			this.fetchData()
		},
		fetchData(search = null) {
			this.loading = true
			catalogiStore.refreshCatalogiList(search)
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
		setActive(catalog) {
			if (JSON.stringify(catalogiStore.catalogiItem) === JSON.stringify(catalog)) {
				catalogiStore.setCatalogiItem(false)
			} else { catalogiStore.setCatalogiItem(catalog) }
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
