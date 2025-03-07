<script setup>
import { navigationStore, organizationStore } from '../../store/store.js'
</script>

<template>
	<div class="detailContainer">
		<div class="head">
			<h1 class="h1">
				{{ organization.title }}
			</h1>

			<NcActions
				:disabled="loading"
				:primary="true"
				:menu-name="loading ? 'Laden...' : 'Acties'"
				:inline="1"
				title="Acties die je kan uitvoeren op deze publicatie">
				<template #icon>
					<span>
						<NcLoadingIcon v-if="loading"
							:size="20"
							appearance="dark" />
						<DotsHorizontal v-if="!loading" :size="20" />
					</span>
				</template>
				<NcActionButton
					title="Bekijk de documentatie over organisaties"
					@click="openLink('https://conduction.gitbook.io/opencatalogi-nextcloud/beheerders/organisaties')">
					<template #icon>
						<HelpCircleOutline :size="20" />
					</template>
					Help
				</NcActionButton>
				<NcActionButton @click="navigationStore.setModal('organizationForm')">
					<template #icon>
						<Pencil :size="20" />
					</template>
					Bewerken
				</NcActionButton>
				<NcActionButton @click="navigationStore.setDialog('copyOrganization')">
					<template #icon>
						<ContentCopy :size="20" />
					</template>
					Kopiëren
				</NcActionButton>
				<NcActionButton @click="navigationStore.setDialog('deleteOrganization')">
					<template #icon>
						<Delete :size="20" />
					</template>
					Verwijderen
				</NcActionButton>
			</NcActions>
		</div>
		<div class="container">
			<div class="detailGrid">
				<div>
					<b>Samenvatting:</b>
					<span>{{ organization.summary }}</span>
				</div>
				<div>
					<b>Beschrijving:</b>
					<span>{{ organization.description }}</span>
				</div>
				<div>
					<b>Afbeelding:</b>
					<span>{{ organization.image }}</span>
				</div>
			</div>
		</div>
	</div>
</template>

<script>
// Components
import { NcActionButton, NcActions, NcLoadingIcon } from '@nextcloud/vue'

// Icons
import ContentCopy from 'vue-material-design-icons/ContentCopy.vue'
import Delete from 'vue-material-design-icons/Delete.vue'
import DotsHorizontal from 'vue-material-design-icons/DotsHorizontal.vue'
import HelpCircleOutline from 'vue-material-design-icons/HelpCircleOutline.vue'
import Pencil from 'vue-material-design-icons/Pencil.vue'

export default {
	name: 'OrganizationDetail',
	components: {
		// Components
		NcLoadingIcon,
		NcActionButton,
		NcActions,
		// Icons
		DotsHorizontal,
		Pencil,
		Delete,
		ContentCopy,
		HelpCircleOutline,
	},
	props: {
		organizationItem: {
			type: Object,
			required: true,
		},
	},
	data() {
		return {
			organization: [],
			prive: false,
			loading: false,
			catalogiLoading: false,
			publicationTypeLoading: false,
			hasUpdated: false,
			userGroups: [
				{
					id: '1',
					label: 'Content Beheerders',
				},
			],
			chart: {
				options: {
					chart: {
						id: 'Aantal bekeken publicaties',
					},
					xaxis: {
						categories: ['7-11', '7-12', '7-13', '7-15', '7-16', '7-17', '7-18'],
					},
				},
				series: [{
					name: 'Weergaven',
					data: [0, 0, 0, 0, 0, 0, 15],
				}],
			},
			upToDate: false,
		}
	},
	watch: {
		organizationItem: {
			handler(newOrganizationItem, oldOrganizationItem) {
				// why this? because when you fetch a new item it changes the reference to said item, which in return causes it to fetch again (a.k.a. infinite loop)
				// run the fetch only once to update the item
				if (!this.upToDate || JSON.stringify(newOrganizationItem) !== JSON.stringify(oldOrganizationItem)) {
					this.organization = newOrganizationItem
					newOrganizationItem && this.fetchData(newOrganizationItem?.id)
					this.upToDate = true
				}
			},
			deep: true,
		},

	},
	mounted() {
		this.organization = organizationStore.organizationItem
		organizationStore.organizationItem && this.fetchData(organizationStore.organizationItem.id)
	},
	methods: {
		fetchData(id) {
			organizationStore.getOneOrganization(id)
				.then(({ response, data }) => {
					this.organization = data
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
  font-weight: bold;
}

.head{
	display: flex;
	justify-content: space-between;
}

.button{
	max-height: 10px;
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

.dataContent {
  display: flex;
  flex-direction: column;
}

.active.organizationDetails-actionsDelete {
    background-color: var(--color-error) !important;
}
.active.organizationDetails-actionsDelete button {
    color: #EBEBEB !important;
}

.OrganizationDetail-clickable {
    cursor: pointer !important;
}

.buttonLinkContainer{
	display: flex;
    align-items: center;
}

.float-right {
    float: right;
}
</style>
