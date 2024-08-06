<script setup>
import { catalogiStore, metadataStore, navigationStore, organisationStore } from '../../store/store.js'
</script>

<template>
	<div class="detailContainer">
		<div class="head">
			<h1 class="h1">
				{{ organisationStore.organisationItem.title }}
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
					title="Bekijk de documentatie over publicaties"
					@click="linkToOtherWindow('https://conduction.gitbook.io/opencatalogi-nextcloud/gebruikers/publicaties')">
					<template #icon>
						<HelpCircleOutline :size="20" />
					</template>
					Help
				</NcActionButton>
				<NcActionButton @click="navigationStore.setModal('editOrganisation')">
					<template #icon>
						<Pencil :size="20" />
					</template>
					Bewerken
				</NcActionButton>
				<NcActionButton @click="navigationStore.setDialog('copyOrganisation')">
					<template #icon>
						<ContentCopy :size="20" />
					</template>
					Kopiëren
				</NcActionButton>
				<NcActionButton @click="navigationStore.setDialog('deleteOrganisation')">
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
					<span>{{ organisationStore.organisationItem.summary }}</span>
				</div>
				<div>
					<b>Beschrijving:</b>
					<span>{{ organisationStore.organisationItem.description }}</span>
				</div>
			</div>
		</div>
	</div>
</template>

<script>
// Components
import { NcActionButton, NcActionLink, NcActions, NcButton, NcListItem, NcLoadingIcon, NcNoteCard, NcSelectTags } from '@nextcloud/vue'
import { BTab, BTabs } from 'bootstrap-vue'
import VueApexCharts from 'vue-apexcharts'

// Icons
import ArchivePlusOutline from 'vue-material-design-icons/ArchivePlusOutline.vue'
import CheckCircle from 'vue-material-design-icons/CheckCircle.vue'
import CircleOutline from 'vue-material-design-icons/CircleOutline.vue'
import ContentCopy from 'vue-material-design-icons/ContentCopy.vue'
import Delete from 'vue-material-design-icons/Delete.vue'
import DotsHorizontal from 'vue-material-design-icons/DotsHorizontal.vue'
import Download from 'vue-material-design-icons/Download.vue'
import ExclamationThick from 'vue-material-design-icons/ExclamationThick.vue'
import FilePlusOutline from 'vue-material-design-icons/FilePlusOutline.vue'
import FileTreeOutline from 'vue-material-design-icons/FileTreeOutline.vue'
import HelpCircleOutline from 'vue-material-design-icons/HelpCircleOutline.vue'
import LockOpenVariantOutline from 'vue-material-design-icons/LockOpenVariantOutline.vue'
import LockOutline from 'vue-material-design-icons/LockOutline.vue'
import OpenInApp from 'vue-material-design-icons/OpenInApp.vue'
import Pencil from 'vue-material-design-icons/Pencil.vue'
import Publish from 'vue-material-design-icons/Publish.vue'
import PublishOff from 'vue-material-design-icons/PublishOff.vue'
import TimelineQuestionOutline from 'vue-material-design-icons/TimelineQuestionOutline.vue'

export default {
	name: 'OrganisationDetail',
	components: {
		// Components
		NcLoadingIcon,
		NcActionButton,
		NcActions,
		NcButton,
		NcListItem,
		NcSelectTags,
		NcNoteCard,
		apexchart: VueApexCharts,
		// Icons
		CheckCircle,
		ExclamationThick,
		DotsHorizontal,
		Pencil,
		Delete,
		Publish,
		PublishOff,
		OpenInApp,
		FilePlusOutline,
		FileTreeOutline,
		CircleOutline,
		ContentCopy,
		TimelineQuestionOutline,
		LockOutline,
		LockOpenVariantOutline,
		Download,
		ArchivePlusOutline,
		HelpCircleOutline,
	},
	props: {
		organisationItem: {
			type: Object,
			required: true,
		},
	},
	data() {
		return {
			organisation: [],
			catalogi: [],
			metadata: [],
			prive: false,
			loading: false,
			catalogiLoading: false,
			metaDataLoading: false,
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
		organisationItem: {
			handler(newOrganisationItem, oldOrganisationItem) {
				if (!this.upToDate || JSON.stringify(newOrganisationItem) !== JSON.stringify(oldOrganisationItem)) {
					this.organisation = organisationStore.organisationItem
					organisationStore.organisationItem && this.fetchData(organisationStore.organisationItem.id)
				}
			},
			deep: true,
		},

	},
	mounted() {

		this.organisation = organisationStore.organisationItem
		organisationStore.organisationItem && this.fetchData(organisationStore.organisationItem.id)

	},
	methods: {
		fetchData(id) {
			// this.loading = true
			fetch(`/index.php/apps/opencatalogi/api/organisations/${id}`, {
				method: 'GET',
			})
				.then((response) => {
					response.json().then((data) => {
						this.organisation = data
						// this.oldZaakId = id
						this.fetchCatalogi(data.catalogi)
						this.fetchMetaData(data.metaData)
						organisationStore.getOrganisationAttachments()
						// this.loading = false
					})
				})
				.catch((err) => {
					console.error(err)
					// this.oldZaakId = id
					// this.loading = false
				})
		},
		linkToOtherWindow(url) {
			window.open(url, '_blank')
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

.active.organisationDetails-actionsDelete {
    background-color: var(--color-error) !important;
}
.active.organisationDetails-actionsDelete button {
    color: #EBEBEB !important;
}

.OrganisationDetail-clickable {
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
