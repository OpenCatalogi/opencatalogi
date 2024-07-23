<script setup>
import { useCatalogiStore } from '../../store/catalogi.js'
import { Catalogi } from '../../entities/index.js'
</script>

<template>
	<div class="detailContainer">
		<div class="head">
			<h1 class="h1">
				{{ catalogi.name }}
			</h1>
			<NcActions :disabled="loading" :primary="true" :menu-name="loading ? 'Laden...' : 'Acties'">
				<template #icon>
					<span>
						<NcLoadingIcon v-if="loading"
							:size="20"
							appearance="dark" />
						<DotsHorizontal v-if="!loading" :size="20" />
					</span>
				</template>
				<NcActionButton @click="store.setModal('editCatalog')">
					<template #icon>
						<Pencil :size="20" />
					</template>
					Bewerken
				</NcActionButton>
				<NcActionButton disabled class="catalogiDetails-actionsDelete">
					<template #icon>
						<Delete :size="20" />
					</template>
					Verwijderen
				</NcActionButton>
			</NcActions>
		</div>
		<span>{{ catalogi.description }}</span>
		<div class="tabContainer">
			<BTabs content-class="mt-3" justified>
				<BTab title="Eigenschappen" active>
					adsa
				</BTab>
				<BTab title="Toegang">
					Publiek of alleen bepaalde rollen
				</BTab>
				<BTab title="Metadata">
					adsa
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
} from '@nextcloud/vue'
import { BTabs, BTab } from 'bootstrap-vue'

import DotsHorizontal from 'vue-material-design-icons/DotsHorizontal.vue'
import Pencil from 'vue-material-design-icons/Pencil.vue'
import Delete from 'vue-material-design-icons/Delete.vue'

export default {
	name: 'CatalogiDetails',
	components: {
		NcActions,
		NcActionButton,
		NcLoadingIcon,
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
			loading: false,
		}
	},
	watch: {
		catalogiItem: {
			handler(catalogiItem) {
				this.catalogi = catalogiItem
				this.fetchData(catalogiItem._id)
			},
			deep: true,
		},
	},
	mounted() {
		this.catalogi = useCatalogiStore().catalogiItem
		this.fetchData(useCatalogiStore().catalogiItem._id)
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
						this.catalogi = new Catalogi(
							data.id,
							data.name,
							data.summary,
							data._schema,
							data._id,
						)
					})
					this.loading = false
				})
				.catch((err) => {
					console.error(err)
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

.active.catalogiDetails-actionsDelete {
    background-color: var(--color-error) !important;
}
.active.catalogiDetails-actionsDelete button {
    color: #EBEBEB !important;
}
</style>
