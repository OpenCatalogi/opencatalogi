<script setup>
import { catalogiStore, navigationStore, organizationStore } from '../../store/store.js'
</script>

<template>
	<NcModal v-if="navigationStore.modal === 'editCatalog'"
		ref="modalRef"
		label-id="editCatalogModal"
		@close="navigationStore.setModal(false)">
		<div class="modal__content">
			<h2>Catalogus bewerken</h2>
			<div v-if="success !== null || error">
				<NcNoteCard v-if="success" type="success">
					<p>Catalogus succesvol bewerkt</p>
				</NcNoteCard>
				<NcNoteCard v-if="!success" type="error">
					<p>Er is iets fout gegaan bij het bewerken van de catalogus</p>
				</NcNoteCard>
				<NcNoteCard v-if="error" type="error">
					<p>{{ error }}</p>
				</NcNoteCard>
			</div>
			<div v-if="success === null" class="form-group">
				<NcTextField :disabled="loading"
					label="Titel"
					maxlength="255"
					:value.sync="catalogiItem.title"
					:error="!!inputValidation.fieldErrors?.['title']"
					:helper-text="inputValidation.fieldErrors?.['title']?.[0]" />
				<NcTextField :disabled="loading"
					label="Samenvatting"
					maxlength="255"
					:value.sync="catalogiItem.summary"
					:error="!!inputValidation.fieldErrors?.['summary']"
					:helper-text="inputValidation.fieldErrors?.['summary']?.[0]" />
				<NcTextField :disabled="loading"
					label="Beschrijving"
					maxlength="255"
					:value.sync="catalogiItem.description"
					:error="!!inputValidation.fieldErrors?.['description']"
					:helper-text="inputValidation.fieldErrors?.['description']?.[0]" />
				<NcCheckboxRadioSwitch :disabled="loading"
					label="Publiek vindbaar"
					:checked.sync="catalogiItem.listed">
					Publiek vindbaar
				</NcCheckboxRadioSwitch>
				<NcSelect v-bind="organizations"
					v-model="organizations.value"
					input-label="Organisatie"
					:loading="organizationsLoading"
					:disabled="loading" />
			</div>
			<NcButton v-if="success === null"
				v-tooltip="inputValidation.errorMessages?.[0]"
				:disabled="loading || !inputValidation.success"
				type="primary"
				class="ecm-submit-button"
				@click="editCatalog()">
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
import { NcButton, NcModal, NcTextField, NcNoteCard, NcLoadingIcon, NcCheckboxRadioSwitch, NcSelect } from '@nextcloud/vue'
import ContentSaveOutline from 'vue-material-design-icons/ContentSaveOutline.vue'

import { Catalogi } from '../../entities/index.js'

export default {
	name: 'EditCatalogModal',
	components: {
		NcModal,
		NcTextField,
		NcButton,
		NcNoteCard,
		NcLoadingIcon,
		NcCheckboxRadioSwitch,
		NcSelect,
		// Icons
		ContentSaveOutline,
	},
	data() {
		return {
			catalogiItem: {
				title: '',
				summary: '',
				description: '',
				image: '',
				listed: false,
				organization: '',
			},
			loading: false,
			success: null,
			error: false,
			organizations: {},
			organizationsLoading: false,
			hasUpdated: false,
		}
	},
	computed: {
		inputValidation() {
			const catalogiItem = new Catalogi({
				...this.catalogiItem,
				organization: this.organizations.value?.id,
			})

			const result = catalogiItem.validate()

			return {
				success: result.success,
				errorMessages: result?.error?.issues.map((issue) => `${issue.path.join('.')}: ${issue.message}`) || [],
				fieldErrors: result?.error?.formErrors?.fieldErrors || {},
			}
		},
	},
	mounted() {
		// catalogiStore.catalogiItem can be false, so only assign catalogiStore.catalogiItem to catalogiItem if its NOT false
		catalogiStore.catalogiItem && (this.catalogiItem = catalogiStore.catalogiItem)
	},
	updated() {
		if (navigationStore.modal === 'editCatalog' && this.hasUpdated) {
			if (this.catalogiItem.id === catalogiStore.catalogiItem.id) return
			this.hasUpdated = false
		}
		if (navigationStore.modal === 'editCatalog' && !this.hasUpdated) {
			catalogiStore.catalogiItem && (this.catalogiItem = catalogiStore.catalogiItem)
			this.fetchData(catalogiStore.catalogiItem.id)
			this.fetchOrganizations()
			this.hasUpdated = true
		}
	},
	methods: {
		closeModal() {
			navigationStore.modal = false
		},
		fetchData(id) {
			this.loading = true

			catalogiStore.getOneCatalogi(id)
				.then(({ response, data }) => {
					this.catalogiItem = data
					this.loading = false
				})
				.catch((err) => {
					console.error(err)
					this.loading = false
				})
		},
		fetchOrganizations() {
			this.organizationsLoading = true

			organizationStore.getAllOrganization()
				.then(({ response, data }) => {
					const selectedOrganization = data.filter((org) => org?.id.toString() === catalogiStore.catalogiItem?.organization.toString())[0] || null

					this.organizations = {
						options: data.map((organization) => ({
							id: organization.id,
							label: organization.title,
						})),
						value: selectedOrganization
							? {
								id: selectedOrganization?.id,
								label: selectedOrganization?.title,
							}
							: null,
					}

					this.organizationsLoading = false
				})
				.catch((err) => {
					console.error(err)
					this.organizationsLoading = false
				})
		},
		editCatalog() {
			this.loading = true
			this.error = false

			const CatalogiItem = new Catalogi({
				...this.catalogiItem,
				organization: this.organizations.value?.id,
			})

			catalogiStore.editCatalogi(CatalogiItem)
				.then(({ response }) => {
					this.loading = false
					this.success = response.ok

					// Wait for the user to read the feedback then close the model
					const self = this
					setTimeout(function() {
						self.success = null
						navigationStore.setModal(false)
					}, 2000)
				})
				.catch((err) => {
					this.loading = false
					this.error = err
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

.zaakDetailsContainer {
    margin-block-start: var(--OC-margin-20);
    margin-inline-start: var(--OC-margin-20);
    margin-inline-end: var(--OC-margin-20);
}

.success {
    color: green;
}

.ecm-submit-button {
    margin-block-start: 1rem;
}
</style>
