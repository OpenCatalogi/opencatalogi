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
					<NcActions>
						<NcActionButton @click="store.setModal('publicationEdit')">
							<template #icon>
								<CogOutline :size="20" />
							</template>
							Bewerken
						</NcActionButton>
					</NcActions>
				</div>
				<div class="tabContainer">
					<BTabs content-class="mt-3" justified>
						<BTab title="Publicatie" active>
							<div>
								<h4>Beschrijving:</h4>
								<span>{{ publication.description }}</span>
							</div>
							<div>
								<h4>Catalogi:</h4>
								<span>{{ publication.catalogi }}</span>
							</div>
							<div>
								<h4>Metadata:</h4>
								<span>{{ publication.metaData }}</span>
							</div>
							<div>
								<h4>Data:</h4>
								<div class="dataContent">
									<span
										v-for="(value, name, i) in publication.data"
										:key="`${(name, value)}${i}`">{{ `${name}: ${value}` }}</span>
								</div>
							</div>
						</BTab>
						<BTab title="Bijlagen">
							<div
								v-if="publication?.data?.attachments?.length > 0"
								class="tabPanel">
								<table
									ref="table"
									class="vld-parent table"
									:current-page="currentPage">
									<tr>
										<th>Titel</th>
										<th>Beschrijving</th>
										<th>Licentie</th>
										<th>Type</th>
										<th>Gepubliseerd</th>
										<th>bewerkt</th>
										<th>download</th>
									</tr>
									<tr
										v-for="(attachment, i) in publication?.data?.attachments"
										:key="i"
										class="table-rows">
										<td class="td">
											{{ attachment.title }}
										</td>
										<td class="td">
											{{ attachment.description }}
										</td>
										<td class="td">
											{{ attachment.license }}
										</td>
										<td class="td">
											{{ attachment.type }}
										</td>
										<td class="td">
											{{ attachment.published }}
										</td>
										<td class="td">
											{{ attachment.modified }}
										</td>
										<td class="td">
											<a class="link"
												:href="attachment.downloadURL">Download</a>
										</td>
									</tr>
								</table>
							</div>
							<div v-else class="tabPanel">
								Geen bijlagen gevonden
							</div>
						</BTab>
					</BTabs>
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
import { NcLoadingIcon, NcActions, NcActionButton } from '@nextcloud/vue'
import { BTabs, BTab } from 'bootstrap-vue'
import CogOutline from 'vue-material-design-icons/CogOutline.vue'

export default {
	name: 'PublicationDetail',
	components: {
		NcLoadingIcon,
		NcActionButton,
		NcActions,
		CogOutline,
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
			loading: false,
		}
	},
	watch: {
		publicationId: {
			handler(publicationId) {
				this.fetchData(publicationId)
			},
			deep: true,
		},
	},
	mounted() {
		this.fetchData(this.publicationId)
	},
	methods: {
		fetchData(id) {
			this.loading = true
			fetch(`/index.php/apps/opencatalogi/api/publications/${id}`, {
				method: 'GET',
			})
				.then((response) => {
					response.json().then((data) => {
						this.publication = data
						// this.oldZaakId = id
					})
					this.loading = false
				})
				.catch((err) => {
					console.error(err)
					// this.oldZaakId = id
					this.loading = false
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

.tabContainer > * ul > li {
  display: flex;
  flex: 1;
}

.tabContainer > * ul > li:hover {
  background-color: var(--color-background-hover);
}

.tabContainer > * ul > li > a {
  flex: 1;
  text-align: center;
}

.tabContainer > * ul > li > .active {
  background: transparent !important;
  color: var(--color-main-text) !important;
  border-bottom: var(--default-grid-baseline) solid var(--color-primary-element) !important;
}

.tabContainer > * ul {
  display: flex;
  margin: 10px 8px 0 8px;
  justify-content: space-between;
  border-bottom: 1px solid var(--color-border);
}

.tabPanel {
  padding: 20px 10px;
  min-height: 100%;
  max-height: 100%;
  height: 100%;
  overflow: auto;
}
</style>
