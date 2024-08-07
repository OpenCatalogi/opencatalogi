<script setup>
import { navigationStore, organisationStore } from '../../store/store.js'
</script>

<template>
	<NcAppContentList>
		<ul>
			<div class="listHeader">
				<NcTextField class="searchField"
					:value.sync="search"
					label="Zoeken"
					trailing-button-icon="close"
					:show-trailing-button="search !== ''"
					@trailing-button-click="search = ''">
					<Magnify :size="20" />
				</NcTextField>
				<NcActions>
					<NcActionButton
						title="Bekijk de documentatie over organisaties"
						@click="linkToOtherWindow('https://conduction.gitbook.io/opencatalogi-nextcloud/gebruikers/organisaties')">
						<template #icon>
							<HelpCircleOutline :size="20" />
						</template>
						Help
					</NcActionButton>
					<NcActionButton :disabled="loading" @click="fetchData">
						<template #icon>
							<Refresh :size="20" />
						</template>
						Ververs
					</NcActionButton>
					<NcActionButton @click="navigationStore.setModal('organisationAdd')">
						<template #icon>
							<Plus :size="20" />
						</template>
						Publicatie toevoegen
					</NcActionButton>
				</NcActions>
			</div>
			<div v-if="!loading">
				<NcListItem v-for="(organisation, i) in filteredOrganisations"
					:key="`${organisation}${i}`"
					:name="organisation.title"
					:bold="false"
					:force-display-actions="true"
					:active="organisationStore.organisationItem.id === organisation.id"
					:details="organisation?.status"
					@click="organisationStore.setOrganisationItem(organisation)">
					<template #icon>
						<OfficeBuildingOutline :size="44" />
					</template>
					<template #subname>
						{{ organisation?.summary }}
					</template>
					<template #actions>
						<NcActionButton @click="organisationStore.setOrganisationItem(organisation); navigationStore.setModal('editOrganisation')">
							<template #icon>
								<Pencil :size="20" />
							</template>
							Bewerken
						</NcActionButton>
						<NcActionButton @click="organisationStore.setOrganisationItem(organisation); navigationStore.setDialog('copyOrganisation')">
							<template #icon>
								<ContentCopy :size="20" />
							</template>
							Kopiëren
						</NcActionButton>
						<NcActionButton class="organisationsList-actionsDelete" @click="organisationStore.setOrganisationItem(organisation); navigationStore.setDialog('deleteOrganisation')">
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
				name="Publicaties aan het laden" />
		</ul>
	</NcAppContentList>
</template>
<script>
import { NcActionButton, NcActions, NcAppContentList, NcListItem, NcLoadingIcon, NcTextField } from '@nextcloud/vue'
import { debounce } from 'lodash'

// Icons
import ContentCopy from 'vue-material-design-icons/ContentCopy.vue'
import Delete from 'vue-material-design-icons/Delete.vue'
import HelpCircleOutline from 'vue-material-design-icons/HelpCircleOutline.vue'
import Magnify from 'vue-material-design-icons/Magnify.vue'
import OfficeBuildingOutline from 'vue-material-design-icons/OfficeBuildingOutline.vue'
import Pencil from 'vue-material-design-icons/Pencil.vue'
import Plus from 'vue-material-design-icons/Plus.vue'
import Refresh from 'vue-material-design-icons/Refresh.vue'

export default {
	name: 'OrganisationList',
	components: {
		NcListItem,
		NcActionButton,
		NcAppContentList,
		NcTextField,
		Magnify,
		NcLoadingIcon,
		NcActions,
		// Icons
		Refresh,
		Plus,
		ContentCopy,
		OfficeBuildingOutline,
		Pencil,
		HelpCircleOutline,
	},
	beforeRouteLeave(to, from, next) {
		search = ''
		next()
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
		}
	},
	computed: {
		filteredOrganisations() {
			if (!organisationStore?.organisationList) return []
			return organisationStore.organisationList.filter((organisation) => {
				return organisation
			})
		},
	},
	watch: {
		search: {
			handler(search) {
				this.debouncedFetchData(search)
			},
		},
	},
	mounted() {
		this.fetchData()
	},
	methods: {
		fetchData(search = null) {
			this.loading = true
			organisationStore.refreshOrganisationList(search)
				.then(() => {
					this.loading = false
				})
		},
		debouncedFetchData: debounce(function(search) {
			this.fetchData(search)
		}, 500),
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

.active.organisationDetails-actionsDelete {
    background-color: var(--color-error) !important;
}
.active.organisationDetails-actionsDelete button {
    color: #EBEBEB !important;
}

.loadingIcon {
    margin-block-start: var(--OC-margin-20);
}
</style>
