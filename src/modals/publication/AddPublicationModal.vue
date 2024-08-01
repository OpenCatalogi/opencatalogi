<script setup>
import { navigationStore, publicationStore } from '../../store/store.js'
</script>
<template>
	<NcModal v-if="navigationStore.modal === 'publicationAdd'"
		ref="modalRef"
		label-id="addPublicationModal"
		@close="navigationStore.setModal(false)">
		<div class="modal__content">
			<h2>Publicatie toevoegen</h2>
			<div v-if="success !== null || error">
				<NcNoteCard v-if="success" type="success">
					<p>Publicatie succesvol toegevoegd</p>
				</NcNoteCard>
				<NcNoteCard v-if="!success" type="error">
					<p>Er is iets fout gegaan bij het toevoegen van Publicatie</p>
				</NcNoteCard>
				<NcNoteCard v-if="error" type="error">
					<p>{{ error }}</p>
				</NcNoteCard>
			</div>
			<div class="formContainer">
				<div v-if="success === null" class="form-group">
					<NcTextField :disabled="loading"
						label="Titel"
						:value.sync="publication.title" />
					<NcTextField :disabled="loading"
						label="Samenvatting"
						:value.sync="publication.summary" />
					<NcTextArea :disabled="loading"
						label="Beschrijving"
						:value.sync="publication.description" />
					<NcTextField :disabled="loading"
						label="Reference"
						:value.sync="publication.reference" />
					<NcTextField :disabled="loading"
						label="Categorie"
						:value.sync="publication.category" />
					<NcTextField :disabled="loading"
						label="Portaal"
						:value.sync="publication.portal" />
					<span>
						<p>Published</p>
						<NcDateTimePicker v-model="publication.published"
							:disabled="loading"
							label="Publicatie datum" />
					</span>
					<span>
						<p>Modified</p>
						<NcDateTimePicker v-model="publication.modified"
							:disabled="loading"
							label="Modified" />
					</span>
					<NcTextField :disabled="loading"
						label="Organisatie"
						:value.sync="publication.organization" />
					<NcTextField :disabled="loading"
						label="Attachments"
						:value.sync="publication.attachments" />
					<NcTextField :disabled="loading"
						label="Schema"
						:value.sync="publication.schema" />
					<NcTextField :disabled="loading"
						label="Thema's"
						:value.sync="publication.themes" />
					<span class="APM-horizontal">
						<NcCheckboxRadioSwitch :disabled="loading"
							label="Featured"
							:checked.sync="publication.featured">
							Featured
						</NcCheckboxRadioSwitch>
					</span>
					<NcTextField :disabled="loading"
						label="Image"
						:value.sync="publication.image" />
					<b>Juridisch</b>
					<NcTextField :disabled="loading"
						label="Licentie"
						:value.sync="publication.license" />
					<NcSelect v-bind="catalogi"
						v-model="catalogi.value"
						input-label="Catalogi"
						:loading="catalogiLoading"
						:disabled="publicationLoading"
						required />
					<NcSelect v-bind="metaData"
						v-model="metaData.value"
						input-label="MetaData"
						:loading="metaDataLoading"
						:disabled="publicationLoading"
						required />
				</div>
			</div>
			<NcButton v-if="success === null"
				:disabled="(!publication.title && !catalogi?.value?.id && !metaData?.value?.id) || loading"
				type="primary"
				@click="addPublication()">
				<template #icon>
					<NcLoadingIcon v-if="loading" :size="20" />
					<ContentSaveOutline v-if="!loading" :size="20" />
				</template>
				Opslaan
			</NcButton>
		</div>
	</NcModal>
</template>

<script>
import {
	NcButton,
	NcModal,
	NcTextField,
	NcTextArea,
	NcSelect,
	NcLoadingIcon,
	NcCheckboxRadioSwitch,
	NcNoteCard,
	NcDateTimePicker,
} from '@nextcloud/vue'
import ContentSaveOutline from 'vue-material-design-icons/ContentSaveOutline.vue'

export default {
	name: 'AddPublicationModal',
	components: {
		NcModal,
		NcTextField,
		NcTextArea,
		NcButton,
		NcSelect,
		NcLoadingIcon,
		NcCheckboxRadioSwitch,
		NcNoteCard,
		NcDateTimePicker,
		// Icons
		ContentSaveOutline,
	},
	data() {
		return {
			publication: {
				title: '',
				summary: '',
				description: '',
				reference: '',
				license: '',
				modified: new Date(),
				featured: false,
				portal: '',
				category: '',
				published: new Date(),
				organization: '',
				attachments: '[""]',
				schema: '',
				image: '',
				themes: '',
				data: {},
			},
			catalogi: {},
			metaData: {},
			errorCode: '',
			catalogiLoading: false,
			metaDataLoading: false,
			publicationLoading: false,
			hasUpdated: false,
			loading: false,
			success: null,
			error: false,
			dataIsValidJson: false,
			attachmentsIsValidJson: false,
		}
	},
	watch: {
		data: {
			handler(data) {
				this.dataIsValidJson = this.isJsonString(data)
			},
			deep: true,
		},
		attachments: {
			handler(attachments) {
				this.attachmentsIsValidJson = this.isJsonString(attachments)
			},
			deep: true,
		},
	},
	updated() {
		if (navigationStore.modal === 'publicationAdd' && !this.hasUpdated) {
			this.fetchCatalogi()
			this.fetchMetaData()
			this.hasUpdated = true
		}
	},
	methods: {
		fetchCatalogi() {
			this.catalogiLoading = true
			fetch('/index.php/apps/opencatalogi/api/catalogi', {
				method: 'GET',
			})
				.then((response) => {
					response.json().then((data) => {

						this.catalogi = {
							options: Object.entries(data.results).map((catalog) => ({
								id: catalog[1].id,
								label: catalog[1].title,
							})),

						}
					})
					this.catalogiLoading = false
				})
				.catch((err) => {
					console.error(err)
					this.catalogiLoading = false
				})
		},
		fetchMetaData() {
			this.metaDataLoading = true
			fetch('/index.php/apps/opencatalogi/api/metadata', {
				method: 'GET',
			})
				.then((response) => {
					response.json().then((data) => {

						this.metaData = {
							options: Object.entries(data.results).map((metaData) => ({
								id: metaData[1].id ?? metaData[1]._id,
								label: metaData[1].title ?? metaData[1].name,
							})),

						}
					})
					this.metaDataLoading = false
				})
				.catch((err) => {
					console.error(err)
					this.metaDataLoading = false
				})
		},
		isJsonString(str) {
			try {
				JSON.parse(str)
			} catch (e) {
				return false
			}
			return true
		},
		addPublication() {
			this.loading = true
			this.error = false
			fetch(
				'/index.php/apps/opencatalogi/api/publications',
				{
					method: 'POST',
					headers: {
						'Content-Type': 'application/json',
					},
					body: JSON.stringify({
						...this.publication,
						catalogi: this.catalogi.value.id,
						metaData: this.metaData.value.id,
					}),
				},
			)
				.then((response) => {
					this.loading = false
					this.success = response.ok
					// Lets refresh the catalogiList
					publicationStore.refreshPublicationList()
					response.json().then((data) => {
						publicationStore.setPublicationItem(data)
					})
					navigationStore.setSelected('publication')
					// Wait for the user to read the feedback then close the model
					const self = this
					setTimeout(function() {
						self.success = null
						navigationStore.setModal(false)
						self.publication = {
							title: '',
							summary: '',
							description: '',
							reference: '',
							license: '',
							modified: new Date(),
							status: '',
							featured: false,
							portal: '',
							category: '',
							published: new Date(),
							organization: '',
							attachments: '[""]',
							schema: '',
							image: '',
							themes: '',
							data: {},
						}
						self.catalogi = {}
						self.metaData = {}
						self.hasUpdated = false
					}, 2000)
				})
				.catch((err) => {
					this.error = err
					this.loading = false
					self.hasUpdated = false
				})
		},
	},
}
</script>

<style>
.modal__content {
	margin: var(--OC-margin-50);
	text-align: center;
}

.formContainer>* {
	margin-block-end: 10px;
}

.selectGrid {
	display: grid;
	grid-gap: 5px;
	grid-template-columns: 1fr 1fr;
}

.zaakDetailsContainer {
	margin-block-start: var(--OC-margin-20);
	margin-inline-start: var(--OC-margin-20);
	margin-inline-end: var(--OC-margin-20);
}

.success {
	color: green;
}

.APM-horizontal {
    display: flex;
    gap: 4px;
    flex-direction: row;
    align-items: center;
}
</style>
