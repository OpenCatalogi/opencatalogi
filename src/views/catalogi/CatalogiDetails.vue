<script setup>
import { catalogiStore, publicationTypeStore, navigationStore, organizationStore } from '../../store/store.js'
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
				<NcActionButton @click="navigationStore.setModal('addCatalogiPublicationType')">
					<template #icon>
						<Plus :size="20" />
					</template>
					Publicatietype toevoegen
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
				<div class="catalogDetailGridOrganization">
					<b class="catalogDetailGridOrganizationTitle">Organisatie:</b>
					<span v-if="organizationLoading">Loading...</span>

					<div v-if="!organization">
						Geen organisatie
					</div>
					<div v-if="organization">
						<div v-if="!organizationLoading" class="buttonLinkContainer">
							<span>{{ organization?.title }}</span>
							<NcActions>
								<NcActionLink :aria-label="`got to ${organization?.title}`"
									:name="organization?.title"
									@click="goToOrganization()">
									<template #icon>
										<OpenInApp :size="20" />
									</template>
									{{ organization?.title }}
								</NcActionLink>
							</NcActions>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="tabContainer">
			<BTabs content-class="mt-3" justified>
				<BTab title="Publicatietypes">
					<div v-if="catalogiStore.catalogiItem?.publicationTypes.length > 0 && !publicationTypeLoading">
						<NcListItem v-for="(id, i) in catalogiStore.catalogiItem?.publicationTypes"
							:key="id + i"
							:name="filteredPublicationType(id)?.title || 'loading...'"
							:bold="false"
							:force-display-actions="true">
							<template #icon>
								<FileTreeOutline disable-menu
									:size="44" />
							</template>
							<template #subname>
								{{ filteredPublicationType(id)?.description }}
							</template>
							<template #actions>
								<NcActionButton @click="publicationTypeStore.setPublicationTypeItem(filteredPublicationType(id)); navigationStore.setSelected('publicationType')">
									<template #icon>
										<OpenInApp :size="20" />
									</template>
									Bekijk publicatietype
								</NcActionButton>
								<NcActionButton @click="publicationTypeStore.setPublicationTypeItem(filteredPublicationType(id)); navigationStore.setDialog('deleteCatalogiPublicationType')">
									<template #icon>
										<Delete :size="20" />
									</template>
									Verwijderen
								</NcActionButton>
							</template>
						</NcListItem>
					</div>
					<div v-if="catalogiStore.catalogiItem?.publicationTypes.length === 0">
						Geen publicatietypes gevonden
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
			organization: [],
			organizationLoading: false,
			loading: false,
			upToDate: false,
			publicationTypeLoading: false,
		}
	},
	watch: {
		catalogiItem: {
			handler(newCatalogiItem, oldCatalogiItem) {
				if (!this.upToDate || JSON.stringify(newCatalogiItem) !== JSON.stringify(oldCatalogiItem)) {
					this.catalogi = newCatalogiItem
					if (newCatalogiItem) {
						this.loading = true
						catalogiStore.getOneCatalogi(newCatalogiItem.id)
							.then(() => {
								this.catalogi = catalogiStore.catalogiItem
								this.loading = false
							})
					}
					this.upToDate = true
					if (newCatalogiItem?.organization) {
						this.organizationLoading = true
						organizationStore.getOneOrganization(newCatalogiItem.organization)
							.then(() => {
								this.organization = organizationStore.organizationItem
								this.organizationLoading = false
							})
					} else {
						this.organization = false
					}
				}
			},
			deep: true,
		},
	},
	mounted() {
		this.catalogi = catalogiStore.catalogiItem
		if (catalogiStore.catalogiItem) {
			this.loading = true
			catalogiStore.getOneCatalogi(catalogiStore.catalogiItem.id)
				.then(() => {
					this.catalogi = catalogiStore.catalogiItem
					this.loading = false
				})
		}

		if (catalogiStore.catalogiItem.organization) {
			this.organizationLoading = true
			organizationStore.getOneOrganization(catalogiStore.catalogiItem.organization)
				.then(() => {
					this.organization = organizationStore.organizationItem
					this.organizationLoading = false
				})
		}

		this.publicationTypeLoading = true
		publicationTypeStore.refreshPublicationTypeList()
			.then(() => {
				this.publicationTypeLoading = false
			})
	},
	methods: {
		filteredPublicationType(id) {
			if (this.publicationTypeLoading) return null
			return publicationTypeStore.publicationTypeList.filter((publicationType) => {
				return publicationType?.id === id
			})[0]
		},
		goToOrganization() {
			organizationStore.setOrganizationItem(this.organization)
			navigationStore.setSelected('organizations')
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

.catalogDetailGridOrganization {
	display: flex;
    align-items: center;
}

.catalogDetailGridOrganizationTitle {
	margin-inline-end: 1ch;
}
</style>
