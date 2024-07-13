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
					<NcActionButton @click="store.setModal('publicationAdd')">
						<template #icon>
							<Plus :size="20" />
						</template>
						Publicatie toevoegen
					</NcActionButton>
				</NcActions>
			</div>
			<div v-if="!loading">
				<NcListItem v-for="(publication, i) in publications.results"
					:key="`${publication}${i}`"
					:name="publication?.title"
					:bold="false"
					:force-display-actions="true"
					:active="store.publicationItem.id === publication.id"
					:details="publication?.license"
					:counter-number="1"
					@click="store.setPublicationItem(publication)">
					<template #icon>
						<ListBoxOutline :class="store.publicationItem.id === publication.id && 'selectedZaakIcon'"
							disable-menu
							:size="44"
							user="janedoe"
							display-name="Jane Doe" />
					</template>
					<template #subname>
						{{ publication?.description }}
					</template>
					<template #actions>
						<NcActionButton @click="editPublication(publication)">
							<template #icon>
								<Pencil :size="20" />
							</template>
							Bewerken
						</NcActionButton>
						<NcActionButton>
							<template #icon>
								<PublishOff :size="20" />
							</template>
							Depubliceren
						</NcActionButton>
						<NcActionButton class="publicationsList-actionsDelete" @click="deletePublication(publication)">
							<template #icon>
								<Delete :size="20" />
							</template>
							Verwijderen
						</NcActionButton>
					</template>
				</NcListItem>
			</div>

			<NcLoadingIcon v-if="loading"
				:size="64"
				class="loadingIcon"
				appearance="dark"
				name="Zaken aan het laden" />
		</ul>
	</NcAppContentList>
</template>
<script>
import { NcListItem, NcActionButton, NcAppContentList, NcTextField, NcLoadingIcon, NcActions } from '@nextcloud/vue'
import Magnify from 'vue-material-design-icons/Magnify.vue'
import Refresh from 'vue-material-design-icons/Refresh.vue'
import Plus from 'vue-material-design-icons/Plus.vue'
import ListBoxOutline from 'vue-material-design-icons/ListBoxOutline.vue'
import Pencil from 'vue-material-design-icons/Pencil.vue'
import Delete from 'vue-material-design-icons/Delete.vue'
import PublishOff from 'vue-material-design-icons/PublishOff.vue'

export default {
	name: 'PublicationList',
	components: {
		NcListItem,
		NcActionButton,
		NcAppContentList,
		NcTextField,
		ListBoxOutline,
		Magnify,
		NcLoadingIcon,
		NcActions,
		Refresh,
		Plus,
	},
	data() {
		return {
			search: '',
			loading: false,
			publications: [],
		}
	},
	watch: {
		store: {
			handler() {
				store.refresh && this.fetchData()
			},
			deep: true,
		},
	},
	mounted() {
		this.fetchData()
	},
	methods: {
		fetchData() {
			this.loading = true
			fetch(
				'/index.php/apps/opencatalogi/api/publications',
				{
					method: 'GET',
				},
			)
				.then((response) => {
					response.json().then((data) => {
						this.publications = data
					})
					this.loading = false
				})
				.catch((err) => {
					console.error(err)
					this.loading = false
				})
		},
		editPublication(publication) {
			store.setPublicationITem(publication)
			store.setModal('publicationEdit')
		},
		deletePublication(publication) {
			store.setPublicationId(publication.id)
			store.setPublicationItem(publication)
			store.setModal('deletePublication')
		},
		clearText() {
			this.search = ''
		},
	},
}
</script>
<style>
.listHeader{
	display: flex;
}

.refresh{
	margin-block-start: 11px !important;
    margin-block-end: 11px !important;
    margin-inline-end: 10px;
}

.active.publicationDetails-actionsDelete {
    background-color: var(--color-error) !important;
}
.active.publicationDetails-actionsDelete button {
    color: #EBEBEB !important;
}
</style>
