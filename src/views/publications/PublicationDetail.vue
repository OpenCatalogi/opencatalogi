<script setup>
import { store } from '../../store.js'
</script>

<template>
	<div class="detailContainer">
		<div class="head">
			<h1 class="h1">
				{{ publication.title }}
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
				<NcActionButton @click="store.setModal('editPublication')">
					<template #icon>
						<Pencil :size="20" />
					</template>
					Bewerken
				</NcActionButton>
				<NcActionButton>
					<template #icon>
						<ContentCopy :size="20" />
					</template>
					Kopieren
				</NcActionButton>
				<NcActionButton>
					<template #icon>
						<PublishOff :size="20" />
					</template>
					Depubliceren
				</NcActionButton>
				<NcActionButton @click="store.setModal('addPublicationData')">
					<template #icon>
						<FileTreeOutline :size="20" />
					</template>
					Eigenschap toevoegen
				</NcActionButton>
				<NcActionButton @click="store.setModal('AddAttachment')">
					<template #icon>
						<FilePlusOutline :size="20" />
					</template>
					Bijlage toevoegen
				</NcActionButton>
				<NcActionButton @click="store.setDialog('deletePublication')">
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
					<span>{{ publication?.data?.reference }}</span>
				</div>
				<div>
					<b>Samenvatting:</b>
					<span>{{ publication?.data?.summary }}</span>
				</div>
				<div>
					<b>Beschrijving:</b>
					<span>{{ publication.description }}</span>
				</div>
				<div>
					<b>Categorie:</b>
					<span>{{ publication.category }}</span>
				</div>
				<div>
					<b>Portal:</b>
					<span><a target="_blank" :href="publication.portal">{{ publication.portal }}</a></span>
				</div>
				<div>
					<b>Foto:</b>
					<span>{{ publication.image }}</span>
				</div>
				<div>
					<b>Themas:</b>
					<ul>
						<li v-for="(theme, index) in publication?.data?.themes" :key="index">
							{{ theme }}
						</li>
					</ul>
				</div>
				<div>
					<b>Featured:</b>
					<span>{{ publication?.data?.featured }}</span>
				</div>
				<div>
					<b>Licentie:</b>
					<span>{{ publication.license }}</span>
				</div>
				<div>
					<b>Status:</b>
					<span>{{ publication.status }}</span>
				</div>
				<div>
					<b>Gepubliceerd:</b>
					<span>{{ publication.published }}</span>
				</div>
				<div>
					<b>Gemodificeerd:</b>
					<span>{{ publication.modified }}</span>
				</div>
				<div>
					<b>Catalogi:</b>
					<span v-if="catalogiLoading">Loading...</span>
					<div v-if="!catalogiLoading" class="buttonLinkContainer">
						<span>{{ catalogi.name }}</span>
						<NcActions>
							<NcActionLink :aria-label="`got to ${catalogi.name}`"
								:name="catalogi.name"
								@click="goToCatalogi(catalogi._id)">
								<template #icon>
									<OpenInApp :size="20" />
								</template>
								{{ catalogi.name }}
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
								@click="goToMetadata(metadata)">
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
						<NcListItem v-for="(value, key, i) in publication?.data"
							:key="`${key}${i}`"
							:name="key"
							:bold="false"
							:force-display-actions="true"
							@click=" store.setPublicationDataKey(key)
							">
							<template #icon>
								<CircleOutline :class="store.publicationDataKey === key && 'selectedZaakIcon'"
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
								<NcActionButton @click="editPublicationDataItem(key)">
									<template #icon>
										<ContentCopy :size="20" />
									</template>
									Kopieren
								</NcActionButton>
								<NcActionButton>
									<template #icon>
										<PublishOff :size="20" />
									</template>
									Depubliceren
								</NcActionButton>
								<NcActionButton @click="deletePublicationDataItem(key)">
									<template #icon>
										<Delete :size="20" />
									</template>
									Verwijderen
								</NcActionButton>
							</template>
						</NcListItem>
					</BTab>
					<BTab title="Bijlagen">
						<div
							v-if="store.publicationAttachments.results?.length > 0"
							class="tabPanel">
							<NcListItem v-for="(attachment, i) in store.publicationAttachments.results"
								:key="`${attachment}${i}`"
								:name="attachment.name ?? attachment.title"
								:bold="false"
								:active="store.attachmentId === attachment.id"
								:force-display-actions="true"
								:details="attachment?.published ? 'Published' : 'Not Published'"
								@click="store.setAttachmentId(attachment.id)">
								<template #icon>
									<CheckCircle v-if="attachment?.published"
										:class="attachment?.published && 'publishedIcon'"
										disable-menu
										:size="44" />
									<ExclamationThick v-if="!attachment?.published"
										:class="!attachment?.published && 'warningIcon'"
										disable-menu
										:size="44" />
								</template>
								<template #subname>
									{{ attachment?.description }}
								</template>
								<template #actions>
									<NcActionButton @click="store.setAttachmentItem(attachment); store.setModal('EditAttachment')">
										<template #icon>
											<Pencil :size="20" />
										</template>
										Bewerken
									</NcActionButton>
									<NcActionButton @click="store.setAttachmentItem(attachment); store.setDialog('deleteAttachment')">
										<template #icon>
											<Delete :size="20" />
										</template>
										Verwijderen
									</NcActionButton>
								</template>
							</NcListItem>
						</div>
						<div v-else class="tabPanel">
							Geen bijlagen gevonden
						</div>
					</BTab>
				</BTabs>
			</div>
		</div>
	</div>
</template>

<script>
// Components
import { NcLoadingIcon, NcActions, NcActionButton, NcListItem, NcActionLink } from '@nextcloud/vue'
import { BTabs, BTab } from 'bootstrap-vue'

// Icons
import CheckCircle from 'vue-material-design-icons/CheckCircle.vue'
import ExclamationThick from 'vue-material-design-icons/ExclamationThick.vue'
import DotsHorizontal from 'vue-material-design-icons/DotsHorizontal.vue'
import Pencil from 'vue-material-design-icons/Pencil.vue'
import Delete from 'vue-material-design-icons/Delete.vue'
import PublishOff from 'vue-material-design-icons/PublishOff.vue'
import OpenInApp from 'vue-material-design-icons/OpenInApp.vue'
import FilePlusOutline from 'vue-material-design-icons/FilePlusOutline.vue'
import FileTreeOutline from 'vue-material-design-icons/FileTreeOutline.vue'
import CircleOutline from 'vue-material-design-icons/CircleOutline.vue'
import ContentCopy from 'vue-material-design-icons/ContentCopy.vue'

export default {
	name: 'PublicationDetail',
	components: {
		// Components
		NcLoadingIcon,
		NcActionButton,
		NcActions,
		NcListItem,
		// Icons
		CheckCircle,
		ExclamationThick,
		OpenInApp,
		FilePlusOutline,
		FileTreeOutline,
		CircleOutline,
		ContentCopy,
	},
	props: {
		publicationId: {
			type: String,
			required: true,
		},
	},
	data() {
		return {
			publication: [],
			catalogi: [],
			metadata: [],
			loading: false,
			catalogiLoading: false,
			metaDataLoading: false,
			hasUpdated: false,
		}
	},
	watch: {
		publicationId: {
			handler(publicationId) {
				this.publication = store.publicationItem
				this.fetchCatalogi(store.publicationItem.catalogi)
				this.fetchMetaData(store.publicationItem.metaData)
				this.fetchData(store.publicationItem.id)
			},
			deep: true,
		},

	},
	mounted() {

		this.publication = store.publicationItem

		this.fetchCatalogi(store.publicationItem.catalogi, true)
		this.fetchMetaData(store.publicationItem.metaData, true)
		this.fetchData(store.publicationItem.id)

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
						store.getPublicationAttachments()
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
			store.setPublicationItem(this.publication)
			store.setModal('deletePublication')
		},
		editPublicationDataItem(key) {
			store.setPublicationDataKey(key)
			store.setModal('editPublicationDataModal')
		},
		deletePublicationDataItem(key) {
			store.setPublicationDataKey(key)
			store.setDialog('deletePublicationDataDialog')
		},
		editPublicationAttachmentItem(key) {
			store.setPublicationDataKey(key)
			store.setModal('editPublicationDataModal')
		},
		goToMetadata(metadata) {
			store.setMetaDataItem(metadata)
			store.setSelected('metaData')
		},
		goToCatalogi(id) {
			store.setCatalogiId(id)
			store.setSelected('catalogi')
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

.float-right {
    float: right;
}
</style>
