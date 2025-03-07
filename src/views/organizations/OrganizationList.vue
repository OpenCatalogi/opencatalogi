<script setup>
import { navigationStore, organizationStore } from '../../store/store.js'
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
						@click="openLink('https://conduction.gitbook.io/opencatalogi-nextcloud/beheerders/organisaties')">
						<template #icon>
							<HelpCircleOutline :size="20" />
						</template>
						Help
					</NcActionButton>
					<NcActionButton :disabled="loading" @click="refresh">
						<template #icon>
							<Refresh :size="20" />
						</template>
						Ververs
					</NcActionButton>
					<NcActionButton @click="organizationStore.setOrganizationItem(null); navigationStore.setModal('organizationForm')">
						<template #icon>
							<Plus :size="20" />
						</template>
						Organisatie toevoegen
					</NcActionButton>
				</NcActions>
			</div>
			<div v-if="!loading">
				<NcListItem v-for="(organization, i) in filteredOrganizations"
					:key="`${organization}${i}`"
					:name="organization.title"
					:bold="false"
					:force-display-actions="true"
					:active="organizationStore.organizationItem?.id === organization.id"
					:details="organization?.status"
					@click="setActive(organization)">
					<template #icon>
						<OfficeBuildingOutline :size="44" />
					</template>
					<template #subname>
						{{ organization?.summary }}
					</template>
					<template #actions>
						<NcActionButton @click="organizationStore.setOrganizationItem(organization); navigationStore.setModal('organizationForm')">
							<template #icon>
								<Pencil :size="20" />
							</template>
							Bewerken
						</NcActionButton>
						<NcActionButton @click="organizationStore.setOrganizationItem(organization); navigationStore.setDialog('copyOrganization')">
							<template #icon>
								<ContentCopy :size="20" />
							</template>
							Kopiëren
						</NcActionButton>
						<NcActionButton class="organizationsList-actionsDelete" @click="organizationStore.setOrganizationItem(organization); navigationStore.setDialog('deleteOrganization')">
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

			<div v-if="!filteredOrganizations.length" class="emptyListHeader">
				Er zijn nog geen organisaties gedefinieerd.
			</div>
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
	name: 'OrganizationList',
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
		filteredOrganizations() {
			if (!organizationStore?.organizationList) return []
			return organizationStore.organizationList.filter((organization) => {
				return organization
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
		refresh(e) {
			e.preventDefault()
			this.fetchData()
		},
		fetchData(search = null) {
			this.loading = true
			organizationStore.refreshOrganizationList(search)
				.then(() => {
					this.loading = false
				})
		},
		debouncedFetchData: debounce(function(search) {
			this.fetchData(search)
		}, 500),
		openLink(url, type = '') {
			window.open(url, type)
		},
		setActive(organization) {
			if (JSON.stringify(organizationStore.organizationItem) === JSON.stringify(organization)) {
				organizationStore.setOrganizationItem(false)
			} else { organizationStore.setOrganizationItem(organization) }
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

.active.organizationDetails-actionsDelete {
    background-color: var(--color-error) !important;
}
.active.organizationDetails-actionsDelete button {
    color: #EBEBEB !important;
}

.loadingIcon {
    margin-block-start: var(--OC-margin-20);
}
</style>
