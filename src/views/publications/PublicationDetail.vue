<script setup>
import { store } from '../../store.js'
</script>

<template>
	<div class="detailContainer">
		<div v-if="!loading" id="app-content">
			<!-- app-content-wrapper is optional, only use if app-content-list  -->
			<div>
				<div class="head">
					<h1 class="h1">
						{{ publication.title }}
					</h1>
					<NcActions :primary="true" menu-name="Acties">
						<template #icon>
							<DotsHorizontal :size="20" />
						</template>
						<NcActionButton @click="store.setModal('publicationEdit')">
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
						<NcActionButton class="publicationDetails-actionsDelete" @click="deletePublication()">
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
							<h4>Beschrijving:</h4>
							<span>{{ publication.description }}</span>
						</div>
						<div>
							<h4>Catalogi:</h4>
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
							<h4>Metadata:</h4>
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
								<NcListItem v-for="(value, key, i) in publication?.data?.data"
									:key="`${key}${i}`"
									:name="key"
									:bold="false"
									:force-display-actions="true"
									@click=" store.setPublicationDataKey(key)
									">
									<template #icon>
										<ListBoxOutline :class="store.publicationDataKey === key && 'selectedZaakIcon'"
											disable-menu
											:size="44"
											user="janedoe"
											display-name="Jane Doe" />
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
									</template>
								</NcListItem>
							</BTab>
							<BTab title="Bijlagen">
								<div
									v-if="publication?.attachments?.length > 0"
									class="tabPanel">
									<NcListItem v-for="(attachment, i) in publication?.attachments"
										:key="`${attachment}${i}`"
										:name="attachment?.title"
										:bold="false"
										:active="store.attachmentId === attachment.id"
										:force-display-actions="true"
										:details="attachment?.published ? 'Published' : 'Not Published'"
										@click="store.setAttachmentId(attachment.id)">
										<template #icon>
											<CheckCircle v-if="attachment?.published"
												:class="attachment?.published && 'publishedIcon'"
												disable-menu
												:size="44"
												user="janedoe"
												display-name="Jane Doe" />

											<ExclamationThick v-if="!attachment?.published"
												:class="!attachment?.published && 'warningIcon'"
												disable-menu
												:size="44"
												user="janedoe"
												display-name="Jane Doe" />
										</template>
										<template #subname>
											{{ attachment?.description }}
										</template>
										<template #actions>
											<NcActionButton @click="updatePublication(attachment.id)">
												<template #icon>
													<Pencil :size="20" />
												</template>
												Bewerken
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
		</div>
		<NcLoadingIcon
			v-if="loading"
			:size="100"
			appearance="dark"
			name="Publicatie details aan het laden" />
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
import ListBoxOutline from 'vue-material-design-icons/ListBoxOutline.vue'
import Pencil from 'vue-material-design-icons/Pencil.vue'
import Delete from 'vue-material-design-icons/Delete.vue'
import PublishOff from 'vue-material-design-icons/PublishOff.vue'
import OpenInApp from 'vue-material-design-icons/OpenInApp.vue'

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
		ListBoxOutline,
		OpenInApp,
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
			store.setPublicationId(this.publicationId)
			store.setPublicationDataKey(key)
			store.setModal('editPublicationDataModal')
		},
		editPublicationAttachmentItem(key) {
			store.setPublicationId(this.publicationId)
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
		updatePublication() {
			this.loading = true

			fetch(
				`/index.php/apps/opencatalogi/api/publications/${this.publicationId}`,
				{
					method: 'PUT',
					headers: {
						'Content-Type': 'application/json',
					},
					body: JSON.stringify(
						{

							attachments: JSON.parse(this.publication.attachments),

						},
					),
				},
			)
				.then((response) => {
					this.closeModal()
				})
				.catch((err) => {
					this.loading = false
					console.error(err)
				})
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
</style>
