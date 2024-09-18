<script setup>
import { catalogiStore, metadataStore, navigationStore, organisationStore } from '../../store/store.js'
</script>

<template>
	<div class="detailContainer">
		<div class="head">
			<h1 class="h1">
				{{ catalogi.title }}
			</h1>

			<NcActions :disabled="loading"
				:primary="true"
				:inline="1"
				:menu-name="loading ? 'Laden...' : 'Acties'">
				<template #icon>
					<span>
						<NcLoadingIcon v-if="loading"
							:size="20"
							appearance="dark" />
						<DotsHorizontal v-if="!loading" :size="20" />
					</span>
				</template>
				<NcActionButton
					title="Bekijk de documentatie over catalogi"
					@click="openLink('https://conduction.gitbook.io/opencatalogi-nextcloud/beheerders/catalogi', '_blank')">
					<template #icon>
						<HelpCircleOutline :size="20" />
					</template>
					Help
				</NcActionButton>
				<NcActionButton @click="navigationStore.setModal('editCatalog')">
					<template #icon>
						<Pencil :size="20" />
					</template>
					Bewerken
				</NcActionButton>
				<NcActionButton @click="navigationStore.setSelected('publication'); navigationStore.setSelectedCatalogus(catalogi?.id)">
					<template #icon>
						<OpenInApp :size="20" />
					</template>
					Catalogus bekijken
				</NcActionButton>
				<NcActionButton @click="navigationStore.setModal('addCatalogiMetadata')">
					<template #icon>
						<Plus :size="20" />
					</template>
					Publicatie type toevoegen
				</NcActionButton>
				<NcActionButton @click="navigationStore.setDialog('deleteCatalog')">
					<template #icon>
						<Delete :size="20" />
					</template>
					Verwijderen
				</NcActionButton>
			</NcActions>
		</div>
		<div class="container">
			<div class="catalogDetailGrid">
				<div>
					<b>Samenvatting:</b>
					<span>{{ catalogi.summary }}</span>
				</div>
				<div class="catalogDetailGridOrganisation">
					<b class="catalogDetailGridOrganisationTitle">Organisatie:</b>
					<span v-if="organisationLoading">Loading...</span>

					<div v-if="!organisation">
						Geen organisatie
					</div>
					<div v-if="organisation">
						<div v-if="!organisationLoading" class="buttonLinkContainer">
							<span>{{ organisation?.title }}</span>
							<NcActions>
								<NcActionLink :aria-label="`got to ${organisation?.title}`"
									:name="organisation?.title"
									@click="goToOrganisation()">
									<template #icon>
										<OpenInApp :size="20" />
									</template>
									{{ organisation?.title }}
								</NcActionLink>
							</NcActions>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="tabContainer">
			<BTabs content-class="mt-3" justified>
				<BTab title="Publicatie typen">
					<div v-if="catalogiStore.catalogiItem?.metadata.length > 0 && !metadataLoading">
						<NcListItem v-for="(url, i) in catalogiStore.catalogiItem?.metadata"
							:key="url + i"
							:name="filteredMetadata(url)?.title || 'loading...'"
							:bold="false"
							:force-display-actions="true">
							<template #icon>
								<FileTreeOutline disable-menu
									:size="44" />
							</template>
							<template #subname>
								{{ filteredMetadata(url)?.description }}
							</template>
							<template #actions>
								<NcActionButton @click="metadataStore.setMetaDataItem(filteredMetadata(url)); navigationStore.setSelected('metaData')">
									<template #icon>
										<OpenInApp :size="20" />
									</template>
									Bekijk publicatie type
								</NcActionButton>
								<NcActionButton @click="metadataStore.setMetaDataItem(filteredMetadata(url)); navigationStore.setDialog('deleteCatalogiMetadata')">
									<template #icon>
										<Delete :size="20" />
									</template>
									Verwijderen
								</NcActionButton>
							</template>
						</NcListItem>
					</div>
					<div v-if="catalogiStore.catalogiItem?.metadata.length === 0">
						Geen publicatie typen gevonden
					</div>
				</BTab>
				<BTab title="Toegang">
					Publiek of alleen bepaalde rollen
				</BTab>
			</BTabs>
		</div>
	</div>
</template>

<script>
import {
	NcActions,
	NcActionButton,
	NcLoadingIcon,
	NcListItem,
	NcActionLink,
} from '@nextcloud/vue'
import { BTabs, BTab } from 'bootstrap-vue'

import DotsHorizontal from 'vue-material-design-icons/DotsHorizontal.vue'
import Pencil from 'vue-material-design-icons/Pencil.vue'
import Delete from 'vue-material-design-icons/Delete.vue'
import OpenInApp from 'vue-material-design-icons/OpenInApp.vue'
import HelpCircleOutline from 'vue-material-design-icons/HelpCircleOutline.vue'
import Plus from 'vue-material-design-icons/Plus.vue'
import FileTreeOutline from 'vue-material-design-icons/FileTreeOutline.vue'

export default {
	name: 'CatalogiDetails',
	components: {
		NcActions,
		NcActionButton,
		NcLoadingIcon,
		NcListItem,
		NcActionLink,
	},
	props: {
		catalogiItem: {
			type: Object,
			required: true,
		},
	},
	data() {
		return {
			catalogi: false,
			organisation: [],
			organisationLoading: false,
			loading: false,
			upToDate: false,
			metadataLoading: false,
		}
	},
	watch: {
		catalogiItem: {
			handler(newCatalogiItem, oldCatalogiItem) {
				// why this? because when you fetch a new item it changes the reference to said item, which in return causes it to fetch again (a.k.a. infinite loop)
				// run the fetch only once to update the item
				if (!this.upToDate || JSON.stringify(newCatalogiItem) !== JSON.stringify(oldCatalogiItem)) {
					this.catalogi = newCatalogiItem
					// check if newCatalogiItem is not false
					newCatalogiItem && this.fetchData(newCatalogiItem?.id)
					this.upToDate = true
					newCatalogiItem?.organisation ? this.fetchOrganization(newCatalogiItem?.organisation) : this.organisation = false
				}
			},
			deep: true,
		},
	},
	mounted() {
		this.catalogi = catalogiStore.catalogiItem
		// check if catalogiItem is not false
		catalogiStore.catalogiItem && this.fetchData(catalogiStore.catalogiItem?.id)

		catalogiStore.catalogiItem.organisation && this.fetchOrganization(catalogiStore.catalogiItem.organisation)

		this.metadataLoading = true
	},
	methods: {
		fetchData(catalogId) {
			this.loading = true
			fetch(
				`/index.php/apps/opencatalogi/api/catalogi/${catalogId}`,
				{
					method: 'GET',
				},
			)
				.then((response) => {
					response.json().then((data) => {
						catalogiStore.setCatalogiItem(data)
						this.catalogi = catalogiStore.catalogiItem
						metadataStore.refreshMetaDataList()
							.then(() => {
								this.metadataLoading = false
							})
					})
					this.loading = false
				})
				.catch((err) => {
					console.error(err)
					this.loading = false
				})
		},
		fetchOrganization(organisationId, loading) {
			if (loading) { this.organisationLoading = true }

			fetch(`/index.php/apps/opencatalogi/api/organisations/${organisationId}`, {
				method: 'GET',
			})
				.then((response) => {
					response.json().then((data) => {
						this.organisation = data
					})
					if (loading) { this.organisationLoading = false }
				})
				.catch((err) => {
					console.error(err)
					if (loading) { this.organisationLoading = false }
				})
		},
		filteredMetadata(source) {
			if (this.metadataLoading) return null
			return metadataStore.metaDataList.filter((metadata) => metadata?.source === source)[0]
		},
		goToOrganisation() {
			organisationStore.setOrganisationItem(this.organisation)
			navigationStore.setSelected('organisations')
		},
		openLink(url, type = '') {
			window.open(url, type)
		},
	},
}
</script>

<style>
h4 {
  font-weight: bold
}

.h1 {
  display: block !important;
  font-size: 2em !important;
  margin-block-start: 0.67em !important;
  margin-block-end: 0.67em !important;
  margin-inline-start: 0px !important;
  margin-inline-end: 0px !important;
  font-weight: bold !important;
  unicode-bidi: isolate !important;
}

.grid {
  display: grid;
  grid-gap: 24px;
  grid-template-columns: 1fr 1fr;
  margin-block-start: var(--OC-margin-50);
  margin-block-end: var(--OC-margin-50);
}

.gridContent {
  display: flex;
  gap: 25px;
}

.tabContainer>* ul>li {
  display: flex;
  flex: 1;
}

.tabContainer>* ul>li:hover {
  background-color: var(--color-background-hover);
}

.tabContainer>* ul>li>a {
  flex: 1;
  text-align: center;
}

.tabContainer>* ul>li>.active {
  background: transparent !important;
  color: var(--color-main-text) !important;
  border-bottom: var(--default-grid-baseline) solid var(--color-primary-element) !important;
}

.tabContainer>* ul {
  display: flex;
  margin: 10px 8px 0 8px;
  justify-content: space-between;
  border-bottom: 1px solid var(--color-border);
}

.tabPanel {
  padding: 20px 10px;
  min-height: 100%;
  max-height: 100%;
  height: 100%;
  overflow: auto;
}

.flex-hor {
    display: flex;
    gap: 4px;
}

.buttonLinkContainer {
	display: flex;
	align-items: center;
}

.catalogDetailGrid {
	display: grid;
	grid-template-columns: 1fr;
}

.catalogDetailGridOrganisation {
	display: flex;
    align-items: center;
}

.catalogDetailGridOrganisationTitle {
	margin-inline-end: 1ch;
}
</style>
