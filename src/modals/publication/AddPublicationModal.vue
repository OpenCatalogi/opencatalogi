<script setup>
import { publicationTypeStore, navigationStore, publicationStore, catalogiStore, organizationStore } from '../../store/store.js'
</script>

<template>
	<NcDialog v-if="navigationStore.modal === 'publicationAdd'"
		name="Publicatie toevoegen"
		size="normal"
		:can-close="false">
		<div v-if="success !== null || error">
			<NcNoteCard v-if="success" type="success">
				<p>Publicatie succesvol aangemaakt</p>
			</NcNoteCard>
			<NcNoteCard v-if="!success" type="error">
				<p>Er is iets fout gegaan bij het aanmaken van Publicatie</p>
			</NcNoteCard>
			<NcNoteCard v-if="error" type="error">
				<p>{{ error }}</p>
			</NcNoteCard>
		</div>

		<template #actions>
			<NcButton v-if="catalogi?.value?.id && !publicationType?.value?.id"
				:disabled="loading"
				@click="catalogi.value = null">
				<template #icon>
					<ArrowLeft :size="20" />
				</template>
				Terug naar Catalogi
			</NcButton>
			<NcButton v-if="catalogi.value?.id && publicationType.value?.id"
				:disabled="loading"
				@click="publicationType.value = null">
				<template #icon>
					<ArrowLeft :size="20" />
				</template>
				Terug naar publicatietype
			</NcButton>
			<NcButton
				@click="closeModal">
				<template #icon>
					<Cancel :size="20" />
				</template>
				{{ success ? 'Sluiten' : 'Annuleer' }}
			</NcButton>
			<NcButton
				@click="openLink('https://conduction.gitbook.io/opencatalogi-nextcloud/gebruikers/publicaties', '_blank')">
				<template #icon>
					<Help :size="20" />
				</template>
				Help
			</NcButton>
			<NcButton v-if="success === null"
				v-tooltip="inputValidation.errorMessages?.[0]"
				:disabled="!inputValidation.success || loading"
				type="primary"
				@click="addPublication()">
				<template #icon>
					<NcLoadingIcon v-if="loading" :size="20" />
					<Plus v-if="!loading" :size="20" />
				</template>
				Toevoegen
			</NcButton>
		</template>

		<div class="formContainer">
			<div v-if="catalogi?.value?.id && success === null">
				<b>Catalogus:</b> {{ catalogi.value.label }}
				<NcButton @click="catalogi.value = null">
					Catalogus wijzigen
				</NcButton>
			</div>
			<div v-if=" publicationType.value?.id && success === null">
				<b>Publicatietype:</b> {{ publicationType.value.label }}
				<NcButton @click="publicationType.value = null">
					Publicatietype wijzigen
				</NcButton>
			</div>
			<div v-if="success === null" class="form-group">
				<!-- STAGE 1 -->
				<div v-if="!catalogi?.value?.id">
					<p>Publicaties horen in een <a @click="openLink('https://conduction.gitbook.io/opencatalogi-nextcloud/beheerders/catalogi', '_blank')">catalogus</a>, aan welke catlogus wilt u deze publicatie toevoegen?</p>
					<NcSelect v-bind="catalogi"
						v-model="catalogi.value"
						input-label="Catalogus*"
						:loading="catalogiLoading"
						:disabled="publicationLoading"
						required />
				</div>
				<!-- STAGE 2 -->
				<div v-if="catalogi?.value?.id && !publicationType?.value?.id">
					<p>Publicaties worden gedefineerd door <a @click="openLink('https://conduction.gitbook.io/opencatalogi-nextcloud/beheerders/publication-types', '_blank')">publicatietypes</a>, van welk publicatietype wit u een publicatie aanmaken?</p>
					<div v-if="!filteredPublicationTypeOptions.options?.length">
						<p>
							<strong>Er zijn nog geen publicatietypes toegevoegd aan deze Catalogus.</strong>
						</p>
						<p>
							<strong>Voeg een publicatietype toe om een publicatie aan te maken.</strong>
						</p>
					</div>
					<div v-if="filteredPublicationTypeOptions.options?.length > 0">
						<NcSelect v-bind="filteredPublicationTypeOptions"
							v-model="publicationType.value"
							input-label="Publicatietype*"
							:loading="publicationTypeLoading"
							:disabled="publicationTypeLoading || publicationLoading"
							required />
					</div>
				</div>
				<!-- STAGE 3 -->
				<div v-if="catalogi.value?.id && publicationType.value?.id">
					<NcTextField :disabled="loading"
						label="Titel*"
						:value.sync="publication.title"
						:error="!!inputValidation.fieldErrors?.['title']"
						:helper-text="inputValidation.fieldErrors?.['title']?.[0]" />
					<NcTextField :disabled="loading"
						label="Samenvatting*"
						required
						:value.sync="publication.summary"
						:error="!!inputValidation.fieldErrors?.['summary']"
						:helper-text="inputValidation.fieldErrors?.['summary']?.[0]" />
					<NcTextArea :disabled="loading"
						label="Beschrijving"
						:value.sync="publication.description"
						:error="!!inputValidation.fieldErrors?.['description']"
						:helper-text="inputValidation.fieldErrors?.['description']?.[0]" />
					<NcTextField :disabled="loading"
						label="Reference"
						:value.sync="publication.reference"
						:error="!!inputValidation.fieldErrors?.['reference']"
						:helper-text="inputValidation.fieldErrors?.['reference']?.[0]" />
					<NcTextField :disabled="loading"
						label="Categorie"
						:value.sync="publication.category"
						:error="!!inputValidation.fieldErrors?.['category']"
						:helper-text="inputValidation.fieldErrors?.['category']?.[0]" />
					<NcTextField :disabled="loading"
						label="Portaal"
						:value.sync="publication.portal"
						:error="!!inputValidation.fieldErrors?.['portal']"
						:helper-text="inputValidation.fieldErrors?.['portal']?.[0]" />
					<span>
						<p>Publicatie datum</p>
						<NcDateTimePicker v-model="publication.published"
							:disabled="loading"
							label="Publicatie datum" />
					</span>
					<span class="APM-horizontal">
						<NcCheckboxRadioSwitch :disabled="loading"
							label="Featured"
							:checked.sync="publication.featured">
							Uitgelicht
						</NcCheckboxRadioSwitch>
					</span>
					<NcTextField :disabled="loading"
						label="Image"
						:value.sync="publication.image"
						:error="!!inputValidation.fieldErrors?.['image']"
						:helper-text="inputValidation.fieldErrors?.['image']?.[0]" />
					<b>Juridisch</b>
					<NcTextField :disabled="loading"
						label="Licentie"
						:value.sync="publication.license"
						:error="!!inputValidation.fieldErrors?.['license']"
						:helper-text="inputValidation.fieldErrors?.['license']?.[0]" />
					<NcSelect v-bind="organizations"
						v-model="organizations.value"
						input-label="Organisatie"
						:loading="organizationsLoading"
						:disabled="loading" />
				</div>
			</div>
		</div>
	</NcDialog>
</template>

<script>
import {
	NcButton,
	NcDialog,
	NcTextField,
	NcTextArea,
	NcSelect,
	NcLoadingIcon,
	NcCheckboxRadioSwitch,
	NcNoteCard,
	NcDateTimePicker,
} from '@nextcloud/vue'

import Plus from 'vue-material-design-icons/Plus.vue'
import Help from 'vue-material-design-icons/Help.vue'
import Cancel from 'vue-material-design-icons/Cancel.vue'
import ArrowLeft from 'vue-material-design-icons/ArrowLeft.vue'
import { Publication } from '../../entities/index.js'

export default {
	name: 'AddPublicationModal',
	components: {
		NcDialog,
		NcTextField,
		NcTextArea,
		NcButton,
		NcSelect,
		NcLoadingIcon,
		NcCheckboxRadioSwitch,
		NcNoteCard,
		NcDateTimePicker,
		// Icons
		Plus,
		Help,
		Cancel,
		ArrowLeft,
	},
	data() {
		return {
			publication: {
				title: '',
				summary: '',
				description: '',
				reference: '',
				license: '',
				featured: false,
				portal: '',
				category: '',
				published: '',
				image: '',
				data: {},
			},
			organizations: {
				value: [],
				options: [],
			},
			catalogiList: [], // this is the entire dataset of catalogi
			catalogi: {},
			publicationTypeList: [], // this is the entire dataset of publication types
			publicationType: {},
			errorCode: '',
			catalogiLoading: false,
			publicationTypeLoading: false,
			publicationLoading: false,
			hasUpdated: false,
			loading: false,
			success: null,
			error: false,
			dataIsValidJson: false,
			attachmentsIsValidJson: false,
		}
	},
	computed: {
		filteredPublicationTypeOptions() {
			if (!this.catalogiList?.length) return {}
			if (!this.catalogi?.options?.length) return {}
			if (!this.catalogi?.value?.id) return {}
			if (!this.publicationTypeList?.length) return {}

			// step 1: get the selected catalogus from the catalogi dropdown
			const selectedCatalogus = this.catalogiList
				.filter((catalogus) => catalogus.id.toString() === this.catalogi.value.id.toString())[0]

			// step 2: get the full publication types from the publicationTypeIds
			const filteredPublicationType = this.publicationTypeList
				.filter((publicationType) => {
					const publicationTypeId = publicationType.id
					return selectedCatalogus.publicationTypes.includes(publicationTypeId.toString())
				})

			return {
				options: filteredPublicationType.map((publicationType) => ({
					id: publicationType.id,
					source: publicationType.source,
					label: publicationType.title,
				})),
			}
		},
		inputValidation() {
			const testClass = new Publication({
				...this.publication,
				catalog: this.catalogi.value?.id,
				publicationType: this.publicationType?.value?.id,
				published: this.publication.published !== '' ? new Date(this.publication.published).toISOString() : new Date().toISOString(),
				organization: this.organizations.value?.id,
			})

			const result = testClass.validate()

			return {
				success: result.success,
				errorMessages: result?.error?.issues.map((issue) => `${issue.path.join('.')}: ${issue.message}`) || [],
				fieldErrors: result?.error?.formErrors?.fieldErrors || {},
			}
		},
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
			this.fetchPublicationType()
			this.fetchOrganizations()
			this.hasUpdated = true
		}
	},
	methods: {
		fetchCatalogi() {
			this.catalogiLoading = true

			catalogiStore.getAllCatalogi()
				.then(({ response, data }) => {

					this.catalogiList = data

					const selectedCatalogus = navigationStore.getTransferData() !== 'ignore selectedCatalogus'
						? data.filter((catalogus) => catalogus.id.toString() === navigationStore.selectedCatalogus.toString())[0]
						: null

					this.catalogi = {
						options: Object.entries(data).map((catalog) => ({
							id: catalog[1].id,
							label: catalog[1].title,
						})),
						value: selectedCatalogus
							? {
								id: selectedCatalogus.id,
								label: selectedCatalogus.title,
							}
							: null,
					}

					this.catalogiLoading = false
				})
				.catch((err) => {
					console.error(err)
					this.catalogiLoading = false
				})
		},
		fetchPublicationType() {
			this.publicationTypeLoading = true

			publicationTypeStore.getAllPublicationTypes()
				.then(({ data }) => {
					this.publicationTypeList = data

					this.publicationTypeLoading = false
				})
		},
		fetchOrganizations() {
			this.organizationsLoading = true

			organizationStore.getAllOrganization()
				.then(({ response, data }) => {

					this.organizations = {
						options: data.map((organization) => ({
							id: organization.id,
							label: organization.title,
						})),
					}

					this.organizationsLoading = false
				})
				.catch((err) => {
					console.error(err)
					this.organizationsLoading = false
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

			const publicationItem = new Publication({
				...this.publication,
				catalog: this.catalogi?.value?.id,
				publicationType: this.publicationType?.value?.id,
				published: this.publication.published !== '' ? new Date(this.publication.published).toISOString() : new Date().toISOString(),
				organization: this.organizations.value?.id,
			})

			delete publicationItem['@self']

			publicationStore.addPublication(publicationItem)
				.then(({ response }) => {
					this.loading = false
					this.success = response.ok

					navigationStore.setSelected('publication')
					// Wait for the user to read the feedback then close the model
					const self = this
					setTimeout(function() {
						self.closeModal()
					}, 2000)
				})
				.catch((err) => {
					this.error = err
					this.loading = false
					this.hasUpdated = false
				})
		},
		closeModal() {
			this.success = null
			navigationStore.selectedCatalogus && navigationStore.setSelected('publication')
			navigationStore.setModal(false)
			this.publication = {
				title: '',
				summary: '',
				description: '',
				reference: '',
				license: '',
				featured: false,
				portal: '',
				category: '',
				published: '',
				image: '',
				data: {},
			}
			this.catalogi = {}
			this.publicationType = {}
			this.hasUpdated = false
		},
		openLink(url, type = '') {
			window.open(url, type)
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

.apm-submit-button {
    margin-block-start: 1rem
}
</style>
