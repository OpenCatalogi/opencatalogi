<script setup>
import { catalogiStore, metadataStore, navigationStore, publicationStore } from '../../store/store.js'
</script>

<template>
	<div class="detailContainer">
		<div class="head">
			<h1 class="h1">
				{{ publicationStore.publicationItem.title }}
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
					@click="openLink('https://conduction.gitbook.io/opencatalogi-nextcloud/gebruikers/publicaties', '_blank')">
					<template #icon>
						<HelpCircleOutline :size="20" />
					</template>
					Help
				</NcActionButton>
				<NcActionButton @click="navigationStore.setModal('editPublication')">
					<template #icon>
						<Pencil :size="20" />
					</template>
					Bewerken
				</NcActionButton>
				<NcActionButton @click="navigationStore.setDialog('copyPublication')">
					<template #icon>
						<ContentCopy :size="20" />
					</template>
					Kopiëren
				</NcActionButton>
				<NcActionButton v-if="publicationStore.publicationItem.status !== 'published'" @click="publicationStore.setPublicationItem(publication); navigationStore.setDialog('publishPublication')">
					<template #icon>
						<Publish :size="20" />
					</template>
					Publiceren
				</NcActionButton>
				<NcActionButton v-if="publicationStore.publicationItem.status === 'published'" @click="publicationStore.setPublicationItem(publication); navigationStore.setDialog('depublishPublication')">
					<template #icon>
						<PublishOff :size="20" />
					</template>
					Depubliceren
				</NcActionButton>
				<NcActionButton @click="navigationStore.setDialog('archivePublication')">
					<template #icon>
						<ArchivePlusOutline :size="20" />
					</template>
					Archiveren
				</NcActionButton>
				<NcActionButton @click="navigationStore.setModal('addPublicationData')">
					<template #icon>
						<FileTreeOutline :size="20" />
					</template>
					Eigenschap toevoegen
				</NcActionButton>
				<NcActionButton @click="navigationStore.setModal('AddAttachment')">
					<template #icon>
						<FilePlusOutline :size="20" />
					</template>
					Bijlage toevoegen
				</NcActionButton>
				<NcActionButton @click="navigationStore.setDialog('deletePublication')">
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
					<b>Referentie:</b>
					<span>{{ publicationStore.publicationItem.reference }}</span>
				</div>
				<div>
					<b>Samenvatting:</b>
					<span>{{ publicationStore.publicationItem.summary }}</span>
				</div>
				<div>
					<b>Beschrijving:</b>
					<span>{{ publicationStore.publicationItem.description }}</span>
				</div>
				<div>
					<b>Categorie:</b>
					<span>{{ publicationStore.publicationItem.category }}</span>
				</div>
				<div>
					<b>Portal:</b>
					<span><a target="_blank" :href="publicationStore.publicationItem.portal">{{ publicationStore.publicationItem.portal }}</a></span>
				</div>
				<div>
					<b>Foto:</b>
					<span>{{ publicationStore.publicationItem.image }}</span>
				</div>
				<div>
					<b>Thema's:</b>
					<span>{{ publicationStore.publicationItem.themes.join(", ") }}</span>
				</div>
				<div>
					<b>Featured:</b>
					<span>{{ publicationStore.publicationItem.featured ? "Yes" : "No" }}</span>
				</div>
				<div>
					<b>Licentie:</b>
					<span>{{ publicationStore.publicationItem.license }}</span>
				</div>
				<div>
					<b>Status:</b>
					<span>{{ publicationStore.publicationItem.status }}</span>
				</div>
				<div>
					<b>Gepubliceerd:</b>
					<span>{{ publicationStore.publicationItem.published }}</span>
				</div>
				<div>
					<b>Gewijzigd:</b>
					<span>{{ publicationStore.publicationItem.modified }}</span>
				</div>
				<div>
					<b>Catalogi:</b>
					<span v-if="catalogiLoading">Loading...</span>
					<div v-if="!catalogiLoading" class="buttonLinkContainer">
						<span>{{ catalogi.title }}</span>
						<NcActions>
							<NcActionLink :aria-label="`got to ${catalogi.title}`"
								:name="catalogi.title"
								@click="goToCatalogi()">
								<template #icon>
									<OpenInApp :size="20" />
								</template>
								{{ catalogi.title }}
							</NcActionLink>
						</NcActions>
					</div>
				</div>
				<div>
					<b>Metadata:</b>
					<span v-if="metaDataLoading">Loading...</span>
					<div v-if="!metaDataLoading" class="buttonLinkContainer">
						<span>{{ metadata.title }}</span>
						<NcActions>
							<NcActionLink :aria-label="`got to ${metadata.title}`"
								:name="metadata.title"
								@click="goToMetadata()">
								<template #icon>
									<OpenInApp :size="20" />
								</template>
								{{ metadata.title }}
							</NcActionLink>
						</NcActions>
					</div>
				</div>
			</div>
			<div class="tabContainer">
				<BTabs content-class="mt-3" justified>
					<BTab title="Eigenschappen" active>
						<div v-if="Object.keys(publicationStore.publicationItem?.data).length > 0">
							<NcListItem v-for="(value, key, i) in publicationStore.publicationItem?.data"
								:key="`${key}${i}`"
								:name="key"
								:bold="false"
								:force-display-actions="true"
								@click="publicationStore.setPublicationDataKey(key)
								">
								<template #icon>
									<CircleOutline :class="publicationStore.publicationDataKey === key && 'selectedZaakIcon'"
										disable-menu
										:size="44" />
								</template>
								<template #subname>
									{{ value }}
								</template>
								<template #actions>
									<NcActionButton @click="editPublicationDataItem(key)">
										<template #icon>
											<Pencil :size="20" />
										</template>
										Bewerken
									</NcActionButton>
									<NcActionButton @click="deletePublicationDataItem(key)">
										<template #icon>
											<Delete :size="20" />
										</template>
										Verwijderen
									</NcActionButton>
								</template>
							</NcListItem>
						</div>
						<div v-if="publicationStore.publicationItem?.data.length === 0" class="tabPanel">
							Geen eigenschappen gevonden
						</div>
					</BTab>
					<BTab title="Bijlagen">
						<div
							v-if="publicationStore.publicationAttachments.length > 0"
							class="tabPanel">
							<NcListItem v-for="(attachment, i) in publicationStore.publicationAttachments"
								:key="`${attachment}${i}`"
								:name="attachment.name ?? attachment.title"
								:bold="false"
								:active="publicationStore.attachmentId === attachment.id"
								:force-display-actions="true"
								:details="attachment?.status">
								<template #icon>
									<CheckCircle v-if="attachment?.status === 'published'"
										:class="attachment?.published && 'publishedIcon'"
										disable-menu
										:size="44" />
									<ExclamationThick v-if="attachment?.status !== 'published'"
										:class="!attachment?.published && 'warningIcon'"
										disable-menu
										:size="44" />
								</template>
								<template #subname>
									{{ attachment?.description }}
								</template>
								<template #actions>
									<NcActionButton @click="publicationStore.setAttachmentItem(attachment); navigationStore.setModal('EditAttachment')">
										<template #icon>
											<Pencil :size="20" />
										</template>
										Bewerken
									</NcActionButton>
									<NcActionButton @click="openLink(attachment?.downloadUrl, '_blank')">
										<template #icon>
											<Download :size="20" />
										</template>
										Download
									</NcActionButton>
									<NcActionButton v-if="attachment.status !== 'published'" @click="publicationStore.setAttachmentItem(attachment); navigationStore.setDialog('publishAttachment')">
										<template #icon>
											<Publish :size="20" />
										</template>
										Publiceren
									</NcActionButton>
									<NcActionButton v-if="attachment.status === 'published'" @click="publicationStore.setAttachmentItem(attachment); navigationStore.setDialog('depublishAttachment')">
										<template #icon>
											<PublishOff :size="20" />
										</template>
										Depubliceren
									</NcActionButton>
									<NcActionButton @click="publicationStore.setAttachmentItem(attachment); navigationStore.setDialog('copyAttachment')">
										<template #icon>
											<ContentCopy :size="20" />
										</template>
										Kopiëren
									</NcActionButton>
									<NcActionButton @click="publicationStore.setAttachmentItem(attachment); navigationStore.setDialog('deleteAttachment')">
										<template #icon>
											<Delete :size="20" />
										</template>
										Verwijderen
									</NcActionButton>
								</template>
							</NcListItem>
						</div>
						<div v-if="publicationStore.publicationAttachments.length === 0" class="tabPanel">
							Geen bijlagen gevonden
						</div>
						<div v-if="publicationStore.publicationAttachments.length !== 0 && !publicationStore.publicationAttachments.length > 0" class="tabPanel">
							<NcLoadingIcon
								:size="64"
								class="loadingIcon"
								appearance="dark"
								name="Bijlagen aan het laden" />
						</div>
					</BTab>
					<BTab title="Logging">
						<table width="100%">
							<tr>
								<th><b>Tijdstip</b></th>
								<th><b>Gebruiker</b></th>
								<th><b>Actie</b></th>
								<th><b>Details</b></th>
							</tr>
							<tr>
								<td>18-07-2024 11:55:21</td>
								<td>Ruben van der Linde</td>
								<td>Created</td>
								<td>
									<NcButton @click="navigationStore.setDialog('viewLog')">
										<template #icon>
											<TimelineQuestionOutline
												:size="20" />
										</template>
										Bekijk details
									</NcButton>
								</td>
							</tr>
						</table>
					</BTab>
					<BTab title="Rechten">
						<table width="100%">
							<tr>
								<td>Deze publicatie is <b v-if="prive">NIET</b> openbaar toegankelijk</td>
								<td>
									<NcButton @click="prive = !prive">
										<template #icon>
											<LockOpenVariantOutline v-if="!prive"
												:size="20" />
											<LockOutline v-if="prive"
												:size="20" />
										</template>
										<span v-if="!prive">Privé maken</span>
										<span v-if="prive">Openbaar maken</span>
									</NcButton>
								</td>
							</tr>
							<tr v-if="prive">
								<td>Gebruikersgroepen</td>
								<td><NcSelectTags v-model="userGroups" input-label="gebruikers groepen" :multiple="true" /></td>
							</tr>
						</table>
					</BTab>
					<BTab title="Statistieken">
						<apexchart v-if="publication.status === 'published'"
							width="100%"
							type="line"
							:options="chart.options"
							:series="chart.series" />
						<NcNoteCard type="info">
							<p>Er zijn nog geen statistieken over deze publicatie bekend</p>
						</NcNoteCard>
					</BTab>
				</BTabs>
			</div>
		</div>
	</div>
</template>

<script>
// Components
import { NcActionButton, NcActions, NcButton, NcListItem, NcLoadingIcon, NcNoteCard, NcSelectTags } from '@nextcloud/vue'
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
	name: 'PublicationDetail',
	components: {
		// Components
		NcLoadingIcon,
		NcActionButton,
		NcActions,
		NcButton,
		NcListItem,
		NcSelectTags,
		NcNoteCard,
		BTab,
		BTabs,
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
		publicationItem: {
			type: Object,
			required: true,
		},
	},
	data() {
		return {
			publication: [],
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
		publicationItem: {
			handler(newPublicationItem, oldPublicationItem) {
				if (!this.upToDate || JSON.stringify(newPublicationItem) !== JSON.stringify(oldPublicationItem)) {
					this.publication = publicationStore.publicationItem
					this.fetchCatalogi(publicationStore.publicationItem.catalogi)
					this.fetchMetaData(publicationStore.publicationItem.metaData)
					publicationStore.publicationItem && this.fetchData(publicationStore.publicationItem.id)
				}
			},
			deep: true,
		},

	},
	mounted() {

		this.publication = publicationStore.publicationItem

		this.fetchCatalogi(publicationStore.publicationItem.catalogi, true)
		this.fetchMetaData(publicationStore.publicationItem.metaData, true)
		publicationStore.publicationItem && this.fetchData(publicationStore.publicationItem.id)

	},
	methods: {
		fetchData(id) {
			// this.loading = true
			fetch(`/index.php/apps/opencatalogi/api/publications/${id}`, {
				method: 'GET',
			})
				.then((response) => {
					response.json().then((data) => {
						this.publication = data
						// this.oldZaakId = id
						this.fetchCatalogi(data.catalogi)
						this.fetchMetaData(data.metaData)
						publicationStore.getPublicationAttachments()
						// this.loading = false
					})
				})
				.catch((err) => {
					console.error(err)
					// this.oldZaakId = id
					// this.loading = false
				})
		},
		fetchCatalogi(catalogiId, loading) {
			if (loading) { this.catalogiLoading = true }

			fetch(`/index.php/apps/opencatalogi/api/catalogi/${catalogiId}`, {
				method: 'GET',
			})
				.then((response) => {
					response.json().then((data) => {
						this.catalogi = data
					})
					if (loading) { this.catalogiLoading = false }
				})
				.catch((err) => {
					console.error(err)
					if (loading) { this.catalogiLoading = false }
				})
		},
		fetchMetaData(metadataId, loading) {

			if (loading) { this.metaDataLoading = true }

			fetch(`/index.php/apps/opencatalogi/api/metadata/${metadataId}`, {
				method: 'GET',
			})
				.then((response) => {
					response.json().then((data) => {
						this.metadata = data
					})
					if (loading) { this.metaDataLoading = false }
				})
				.catch((err) => {
					console.error(err)
					if (loading) { this.metaDataLoading = false }
				})
		},
		deletePublication() {
			publicationStore.setPublicationItem(this.publication)
			navigationStore.setModal('deletePublication')
		},
		editPublicationDataItem(key) {
			publicationStore.setPublicationDataKey(key)
			navigationStore.setModal('editPublicationData')
		},
		deletePublicationDataItem(key) {
			publicationStore.setPublicationDataKey(key)
			navigationStore.setDialog('deletePublicationDataDialog')
		},
		editPublicationAttachmentItem(key) {
			publicationStore.setPublicationDataKey(key)
			navigationStore.setModal('editPublicationDataModal')
		},
		goToMetadata() {
			metadataStore.setMetaDataItem(this.metadata)
			navigationStore.setSelected('metaData')
		},
		goToCatalogi() {
			catalogiStore.setCatalogiItem(this.catalogi)
			navigationStore.setSelected('catalogi')
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

.active.publicationDetails-actionsDelete {
    background-color: var(--color-error) !important;
}
.active.publicationDetails-actionsDelete button {
    color: #EBEBEB !important;
}

.PublicationDetail-clickable {
    cursor: pointer !important;
}

.buttonLinkContainer{
	display: flex;
    align-items: center;
}

.flex-hor {
    display: flex;
    gap: 4px;
}

.float-right {
    float: right;
}
</style>
