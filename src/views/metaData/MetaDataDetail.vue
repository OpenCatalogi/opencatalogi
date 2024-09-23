<script setup>
import { navigationStore, metadataStore } from '../../store/store.js'
</script>

<template>
	<div class="detailContainer">
		<div class="head">
			<div>
				<h1 class="h1">
					{{ metadata.title }}
				</h1>
				<span>{{ metadata.description || metadata.summary }}</span>
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
					title="Bekijk de documentatie over metadata"
					@click="openLink('https://conduction.gitbook.io/opencatalogi-nextcloud/beheerders/metadata', '_blank')">
					<template #icon>
						<HelpCircleOutline :size="20" />
					</template>
					Help
				</NcActionButton>
				<NcActionButton @click="navigationStore.setModal('editMetaData')">
					<template #icon>
						<Pencil :size="20" />
					</template>
					Bewerken
				</NcActionButton>
				<NcActionButton @click="navigationStore.setModal('addMetadataDataModal')">
					<template #icon>
						<PlusCircleOutline :size="20" />
					</template>
					Eigenschap toevoegen
				</NcActionButton>
				<NcActionButton @click="navigationStore.setDialog('deleteMetaData')">
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
					<div v-if="Object.entries(metadataStore.metaDataItem.properties).length > 0">
						<NcListItem v-for="(value, key, i) in metadataStore.metaDataItem.properties"
							:key="`${key}${i}`"
							:name="key"
							:active="metadataStore.metadataDataKey === key"
							:bold="false"
							:details="value.type ?? 'Onbekend'"
							:force-display-actions="true"
							@click="metadataStore.setMetadataDataKey(key)">
							<template #icon>
								<CircleOutline :class="metadataStore.metadataDataKey === key && 'selectedZaakIcon'"
									disable-menu
									:size="44" />
							</template>
							<template #subname>
								{{ value.description }}
							</template>
							<template #actions>
								<NcActionButton @click="metadataStore.setMetadataDataKey(key); navigationStore.setModal('editMetadataDataModal')">
									<template #icon>
										<Pencil :size="20" />
									</template>
									Bewerken
								</NcActionButton>
								<NcActionButton @click="metadataStore.setMetadataDataKey(key); navigationStore.setDialog('copyMetaDataProperty')">
									<template #icon>
										<ContentCopy :size="20" />
									</template>
									Kopiëren
								</NcActionButton>
								<NcActionButton @click="metadataStore.setMetadataDataKey(key); navigationStore.setDialog('deleteMetaDataProperty')">
									<template #icon>
										<Delete :size="20" />
									</template>
									Verwijderen
								</NcActionButton>
							</template>
						</NcListItem>
					</div>

					<div v-if="Object.entries(metadataStore.metaDataItem.properties).length <= 0">
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
	name: 'MetaDataDetail',
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
		metaDataItem: {
			type: Object,
			required: true,
		},
	},
	data() {
		return {

			metadata: [],
			loading: false,
			upToDate: false,
		}
	},
	watch: {
		metaDataItem: {
			handler(newMetaDataItem, oldMetaDataItem) {
				if (!this.upToDate || JSON.stringify(newMetaDataItem) !== JSON.stringify(oldMetaDataItem)) {
					this.metadata = newMetaDataItem
					newMetaDataItem && this.fetchData(newMetaDataItem?.id)
					this.upToDate = true
				}
			},
			deep: true,
		},
	},
	mounted() {
		this.metadata = metadataStore.metaDataItem
		metadataStore.metaDataItem && this.fetchData(metadataStore.metaDataItem?.id)
	},
	methods: {
		fetchData(metadataId) {
			this.loading = true
			fetch(
				`/index.php/apps/opencatalogi/api/metadata/${metadataId}`,
				{
					method: 'GET',
				},
			)
				.then((response) => {
					response.json().then((data) => {
						this.metaData = data
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

.float-right {
    float: right;
}
</style>
