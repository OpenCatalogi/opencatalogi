<script setup>
import { store } from '../../store.js'
</script>

<template>
	<div class="detailContainer">
		<div class="head">
			<h1 class="h1">
				{{ metadata.title }}
			</h1>
			<span>{{ metadata.description }}</span>
			<NcActions :disabled="loading" :primary="true" :menu-name="loading ? 'Laden...' : 'Acties'">
				<template #icon>
					<span>
						<NcLoadingIcon v-if="loading"
							:size="20"
							appearance="dark" />
						<DotsHorizontal v-if="!loading" :size="20" />
					</span>
				</template>
				<NcActionButton @click="store.setModal('editMetaData')">
					<template #icon>
						<Pencil :size="20" />
					</template>
					Bewerken
				</NcActionButton>
				<NcActionButton @click="store.setModal('addMetadataDataModal')">
					<template #icon>
						<PlusCircleOutline :size="20" />
					</template>
					Eigenschap toevoegen
				</NcActionButton>
				<NcActionButton @click="store.setDialog('deleteMetaData')">
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
					<NcListItem v-for="(value, key, i) in metadata?.properties"
						:key="`${key}${i}`"
						:name="key"
						:bold="false"
						:details="value.type ?? 'Onbekend'"
						:force-display-actions="true">
						<template #icon>
							<CircleOutline :class="store.metadataDataKey === key && 'selectedZaakIcon'"
								disable-menu
								:size="44" />
						</template>
						<template #subname>
							{{ value.description }}
						</template>
						<template #actions>
							<NcActionButton @click="store.setMetadataDataKey(key); store.setModal('editMetadataDataModal')">
								<template #icon>
									<Pencil :size="20" />
								</template>
								Bewerken
							</NcActionButton>
							<NcActionButton @click="store.setMetadataDataKey(key); store.setDialog('copyMetaDataProperty')">
								<template #icon>
									<ContentCopy :size="20" />
								</template>
								Kopieren
							</NcActionButton>
							<NcActionButton @click="store.setMetadataDataKey(key); store.setDialog('deleteMetaDataProperty')">
								<template #icon>
									<Delete :size="20" />
								</template>
								Verwijderen
							</NcActionButton>
						</template>
					</NcListItem>
				</BTab>
			</BTabs>
		</div>
	</div>
</template>

<script>
import { NcLoadingIcon, NcActions, NcActionButton, NcListItem } from '@nextcloud/vue'
import { BTabs, BTab } from 'bootstrap-vue'

import DotsHorizontal from 'vue-material-design-icons/DotsHorizontal.vue'
import Pencil from 'vue-material-design-icons/Pencil.vue'
import Delete from 'vue-material-design-icons/Delete.vue'
import CircleOutline from 'vue-material-design-icons/CircleOutline.vue'
import PlusCircleOutline from 'vue-material-design-icons/PlusCircleOutline.vue'
import ContentCopy from 'vue-material-design-icons/ContentCopy.vue'

export default {
	name: 'MetaDataDetail',
	components: {
		NcLoadingIcon,
		NcActions,
		NcActionButton,
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
		}
	},
	watch: {
		metaDataItem: {
			handler(metaDataItem) {
				this.metadata = metaDataItem
				this.fetchData(metaDataItem?._id)
			},
			deep: true,
		},
	},
	mounted() {
		this.metadata = store.metaDataItem
		this.fetchData(store.metaDataItem?._id)
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

.float-right {
    float: right;
}
</style>
