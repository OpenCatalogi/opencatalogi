<script setup>
import { navigationStore, publicationTypeStore } from '../../store/store.js'
</script>

<template>
	<div class="detailContainer">
		<div class="head">
			<div>
				<h1 class="h1">
					{{ publicationType.title }}
				</h1>
				<span>{{ publicationType.description || publicationType.summary }}</span>
			</div>

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
					title="Bekijk de documentatie over publicatietypes"
					@click="openLink('https://conduction.gitbook.io/opencatalogi-nextcloud/beheerders/publicatietypes', '_blank')">
					<template #icon>
						<HelpCircleOutline :size="20" />
					</template>
					Help
				</NcActionButton>
				<NcActionButton @click="navigationStore.setModal('editPublicationType')">
					<template #icon>
						<Pencil :size="20" />
					</template>
					Bewerken
				</NcActionButton>
				<NcActionButton @click="navigationStore.setModal('addPublicationTypeProperty')">
					<template #icon>
						<PlusCircleOutline :size="20" />
					</template>
					Eigenschap toevoegen
				</NcActionButton>
				<NcActionButton @click="navigationStore.setDialog('deletePublicationType')">
					<template #icon>
						<Delete :size="20" />
					</template>
					Verwijderen
				</NcActionButton>
			</NcActions>
		</div>
		<div class="tabContainer">
			<BTabs content-class="mt-3" justified>
				<BTab title="Eigenschappen" active>
					<div v-if="Object.entries(publicationTypeStore.publicationTypeItem.properties).length > 0">
						<NcListItem v-for="(value, key, i) in publicationTypeStore.publicationTypeItem.properties"
							:key="`${key}${i}`"
							:name="key"
							:active="publicationTypeStore.publicationTypeDataKey === key"
							:bold="false"
							:details="value.type ?? 'Onbekend'"
							:force-display-actions="true"
							@click="setActiveProperty(key)">
							<template #icon>
								<CircleOutline :class="publicationTypeStore.publicationTypeDataKey === key && 'selectedZaakIcon'"
									disable-menu
									:size="44" />
							</template>
							<template #subname>
								{{ value.description }}
							</template>
							<template #actions>
								<NcActionButton @click="publicationTypeStore.setPublicationTypeDataKey(key); navigationStore.setModal('editPublicationTypeProperty')">
									<template #icon>
										<Pencil :size="20" />
									</template>
									Bewerken
								</NcActionButton>
								<NcActionButton @click="publicationTypeStore.setPublicationTypeDataKey(key); navigationStore.setDialog('copyPublicationTypeProperty')">
									<template #icon>
										<ContentCopy :size="20" />
									</template>
									Kopiëren
								</NcActionButton>
								<NcActionButton @click="publicationTypeStore.setPublicationTypeDataKey(key); navigationStore.setDialog('deletePublicationTypeProperty')">
									<template #icon>
										<Delete :size="20" />
									</template>
									Verwijderen
								</NcActionButton>
							</template>
						</NcListItem>
					</div>

					<div v-if="Object.entries(publicationTypeStore.publicationTypeItem.properties).length <= 0">
						Nog geen eigenschappen toegevoegd
					</div>
				</BTab>
				<BTab title="Logging">
					<table width="100%">
						<tr>
							<th><b>Tijdstip</b></th>
							<th><b>Gebruiker</b></th>
							<th><b>Actie</b></th>
							<th><b>Details</b></th>
						</tr>
						<tr>
							<td>18-07-2024 11:55:21</td>
							<td>Ruben van der Linde</td>
							<td>Created</td>
							<td>
								<NcButton>
									<template #icon>
										<TimelineQuestionOutline
											:size="20" />
									</template>
									Bekijk details
								</NcButton>
							</td>
						</tr>
					</table>
				</BTab>
			</BTabs>
		</div>
	</div>
</template>

<script>
import { NcLoadingIcon, NcActions, NcActionButton, NcListItem, NcButton } from '@nextcloud/vue'
import { BTabs, BTab } from 'bootstrap-vue'

import DotsHorizontal from 'vue-material-design-icons/DotsHorizontal.vue'
import Pencil from 'vue-material-design-icons/Pencil.vue'
import Delete from 'vue-material-design-icons/Delete.vue'
import CircleOutline from 'vue-material-design-icons/CircleOutline.vue'
import PlusCircleOutline from 'vue-material-design-icons/PlusCircleOutline.vue'
import ContentCopy from 'vue-material-design-icons/ContentCopy.vue'
import TimelineQuestionOutline from 'vue-material-design-icons/TimelineQuestionOutline.vue'
import HelpCircleOutline from 'vue-material-design-icons/HelpCircleOutline.vue'

export default {
	name: 'PublicationTypeDetail',
	components: {
		NcLoadingIcon,
		NcActions,
		NcActionButton,
		NcButton,
		// Icons
		PlusCircleOutline,
		ContentCopy,
		Pencil,
		Delete,
		CircleOutline,
	},
	props: {
		publicationTypeItem: {
			type: Object,
			required: true,
		},
	},
	data() {
		return {

			publicationType: [],
			loading: false,
			upToDate: false,
		}
	},
	watch: {
		publicationTypeItem: {
			handler(newPublicationTypeItem, oldPublicationTypeItem) {
				if (!this.upToDate || JSON.stringify(newPublicationTypeItem) !== JSON.stringify(oldPublicationTypeItem)) {
					this.publicationType = newPublicationTypeItem
					newPublicationTypeItem && this.fetchData(newPublicationTypeItem?.id)
					this.upToDate = true
				}
			},
			deep: true,
		},
	},
	mounted() {
		this.publicationType = publicationTypeStore.publicationTypeItem
		publicationTypeStore.publicationTypeItem && this.fetchData(publicationTypeStore.publicationTypeItem?.id)
	},
	methods: {
		fetchData(publicationTypeId) {
			this.loading = true
			fetch(
				`/index.php/apps/opencatalogi/api/publication_types/${publicationTypeId}`,
				{
					method: 'GET',
				},
			)
				.then((response) => {
					response.json().then((data) => {
						this.publicationType = data
						// this.oldZaakId = id
					})
					this.loading = false
				})
				.catch((err) => {
					console.error(err)
					// this.oldZaakId = id
					this.loading = false
				})
		},
		openLink(url, type = '') {
			window.open(url, type)
		},
		setActiveProperty(property) {
			if (JSON.stringify(publicationTypeStore.publicationTypeDataKey) === JSON.stringify(property)) {
				publicationTypeStore.setPublicationTypeDataKey(false)
			} else { publicationTypeStore.setPublicationTypeDataKey(property) }

		},
	},
}
</script>

<style scoped>
.head {
    align-items: flex-start;
}
.head .h1 {
    margin-block-start: 0 !important;
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

.float-right {
    float: right;
}
</style>
