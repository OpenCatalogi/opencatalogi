<script setup>
import { catalogiStore, navigationStore, searchStore } from '../../store/store.js'
</script>

<template>
	<NcAppContentList>
		<ul>
			<div class="listHeader">
				<NcTextField class="searchField"
					:value.sync="searchStore.search"
					label="Search"
					trailing-button-icon="close"
					:show-trailing-button="search !== ''"
					@trailing-button-click="searchStore.setSearch('')">
					<Magnify :size="20" />
				</NcTextField>
				<NcActions>
					<NcActionButton @click="fetchData">
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
					:active="catalogiStore.catalogiItem?.id === catalogus?.id"
					:details="'1h'"
					:counter-number="44"
					:force-display-actions="true"
					@click="catalogiStore.setCatalogiItem(catalogus)">
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
						<NcActionButton @click="catalogiStore.setCatalogiItem(catalogus); navigationStore.setDialog('deleteCatalog')">
							<template #icon>
								<Delete :size="20" />
							</template>
							Delete
						</NcActionButton>
					</template>
				</NcListItem>
			</div>

			<NcLoadingIcon v-if="loading"
				class="loadingIcon"
				:size="64"
				appearance="dark"
				name="Zaken aan het laden" />
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

export default {
	name: 'CatalogiList',
	components: {
		NcListItem,
		NcActions,
		NcActionButton,
		NcAppContentList,
		NcTextField,
		DatabaseOutline,
		Magnify,
		NcLoadingIcon,
		Refresh,
		Plus,
		Pencil,
		Delete,
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
			catalogiStore.refreshCatalogiList()
			this.loading = false
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

.selectedZaakIcon>svg {
    fill: white;
}

.loadingIcon {
    margin-block-start: var(--OC-margin-20);
}
</style>
