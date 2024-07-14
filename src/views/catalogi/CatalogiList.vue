<script setup>
import { store } from '../../store.js'
</script>

<template>
	<NcAppContentList>
		<ul>
			<div class="listHeader">
				<NcTextField class="searchField"
					:value.sync="store.search"
					label="Search"
					trailing-button-icon="close"
					:show-trailing-button="search !== ''"
					@trailing-button-click="store.setSearch('')">
					<Magnify :size="20" />
				</NcTextField>
				<NcActions>
					<NcActionButton @click="fetchData">
						<template #icon>
							<Refresh :size="20" />
						</template>
						Ververs
					</NcActionButton>
					<NcActionButton @click="store.setModal('catalogusAdd')">
						<template #icon>
							<Plus :size="20" />
						</template>
						Catalogus toevoegen
					</NcActionButton>
				</NcActions>
			</div>
			<div v-if="!loading">
				<NcListItem v-for="(catalogus, i) in catalogi.results"
					:key="`${catalogus}${i}`"
					:name="catalogus?.name"
					:active="store.catalogiItem?._id === catalogus?._id"
					:details="'1h'"
					:counter-number="44"
					:force-display-actions="true"
					@click="toggleCatalogiDetailView(catalogus)">
					<template #icon>
						<DatabaseOutline :class="store.catalogiItem?.id === catalogus.id && 'selectedZaakIcon'"
							disable-menu
							:size="44"
							user="janedoe"
							display-name="Jane Doe" />
					</template>
					<template #subname>
						{{ catalogus?.summary }}
					</template>
					<template #actions>
						<NcActionButton @click="editCatalog(catalogus)">
							Bewerken
						</NcActionButton>
						<NcActionButton @click="deleteCatalog(catalogus?._id)">
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
			fetch(
				'/index.php/apps/opencatalogi/api/catalogi',
				{
					method: 'GET',
				},
			)
				.then((response) => {
					response.json().then((data) => {
						this.catalogi = data
					})
					this.loading = false
				})
				.catch((err) => {
					console.error(err)
					this.loading = false
				})
		},
		toggleCatalogiDetailView(catalogus) {
			if (store.catalogiItem?._id === catalogus?._id) store.setCatalogiItem(false)
			else store.setCatalogiItem(catalogus)
		},
		editCatalog(catalogiItem) {
			store.setModal('catalogEdit')
			store.setCatalogiItem(catalogiItem)
		},
		deleteCatalog(id) {
			fetch(
				`/index.php/apps/opencatalogi/api/catalogi/${id}`,
				{
					method: 'DELETE',
					headers: {
						'Content-Type': 'application/json',
					},
				},
			)
				.then((response) => {
					console.warn('catalogi removed')
					// this.succesMessage = true
					// setTimeout(() => (this.succesMessage = false), 2500)
				})
				.catch((err) => {
					console.error(err)
				})
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
