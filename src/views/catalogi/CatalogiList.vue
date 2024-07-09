<script setup>
import { store } from '../../store.js'
</script>

<template>
	<NcAppContentList>
		<ul>
			<div class="listHeader">
				<NcTextField class="searchField"
					disabled
					:value.sync="search"
					label="Search"
					trailing-button-icon="close"
					:show-trailing-button="search !== ''"
					@trailing-button-click="clearText">
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
					:active="store.catalogItem === catalogus?.id"
					:details="'1h'"
					:counter-number="44"
					@click="setActive(catalogus.id)">
					<template #icon>
						<DatabaseOutline :class="store.catalogItem === catalogus.id && 'selectedZaakIcon'"
							disable-menu
							:size="44"
							user="janedoe"
							display-name="Jane Doe" />
					</template>
					<template #subname>
						{{ catalogus?.summary }}
					</template>
					<template #actions>
						<NcActionButton>
							Button one
						</NcActionButton>
						<NcActionButton>
							Button two
						</NcActionButton>
						<NcActionButton>
							Button three
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
import { NcListItem, NcActionButton, NcAppContentList, NcTextField, NcLoadingIcon, NcActions } from '@nextcloud/vue'
// eslint-disable-next-line n/no-missing-import
import Magnify from 'vue-material-design-icons/Magnify'
// eslint-disable-next-line n/no-missing-import
import DatabaseOutline from 'vue-material-design-icons/DatabaseOutline'
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
	data() {
		return {
			search: '',
			loading: false,
			catalogi: [],
		}
	},
	mounted() {
		this.fetchData()
	},
	methods: {
		fetchData(newPage) {
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
		setActive(id) {
			store.setCatalogItem(id)
		},
		clearText() {
			this.search = ''
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
    margin-block-start: var(--zaa-margin-20);
}
</style>
