<script setup>
import { catalogiStore, navigationStore, organizationStore } from '../../store/store.js'
</script>

<template>
	<NcModal v-if="navigationStore.modal === 'addCatalog'"
		ref="modalRef"
		label-id="addCatalogModal"
		@close="closeModal">
		<div class="modal__content">
			<h2>Catalogus toevoegen</h2>
			<div v-if="success !== null || error">
				<NcNoteCard v-if="success" type="success">
					<p>Catalogus succesvol toegevoegd</p>
				</NcNoteCard>
				<NcNoteCard v-if="!success" type="error">
					<p>Er is iets fout gegaan bij het toevoegen van catalogus</p>
				</NcNoteCard>
				<NcNoteCard v-if="error" type="error">
					<p>{{ error }}</p>
				</NcNoteCard>
			</div>
			<div v-if="success === null" class="form-group">
				<NcTextField :disabled="loading"
					label="Titel*"
					maxlength="255"
					:value.sync="catalogi.title"
					:error="!!inputValidation.fieldErrors?.['title']"
					:helper-text="inputValidation.fieldErrors?.['title']?.[0]" />
				<NcTextField :disabled="loading"
					label="Samenvatting"
					maxlength="255"
					:value.sync="catalogi.summary"
					:error="!!inputValidation.fieldErrors?.['summary']"
					:helper-text="inputValidation.fieldErrors?.['summary']?.[0]" />
				<NcTextField :disabled="loading"
					label="Beschrijving"
					maxlength="255"
					:value.sync="catalogi.description"
					:error="!!inputValidation.fieldErrors?.['description']"
					:helper-text="inputValidation.fieldErrors?.['description']?.[0]" />
				<NcCheckboxRadioSwitch :disabled="loading"
					label="Publiek vindbaar"
					:checked.sync="catalogi.listed">
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
				:disabled="!inputValidation.success || loading"
				type="primary"
				class="acm-submit-button"
				@click="addCatalog">
				<template #icon>
					<NcLoadingIcon v-if="loading" :size="20" />
					<Plus v-if="!loading" :size="20" />
				</template>
				Toevoegen
			</NcButton>
		</div>
	</NcModal>
</template>

<script>
import { NcButton, NcModal, NcTextField, NcLoadingIcon, NcNoteCard, NcCheckboxRadioSwitch, NcSelect } from '@nextcloud/vue'
import Plus from 'vue-material-design-icons/Plus.vue'

import { Catalogi } from '../../entities/index.js'

export default {
	name: 'AddCatalogModal',
	components: {
		NcModal,
		NcTextField,
		NcButton,
		NcLoadingIcon,
		NcNoteCard,
		NcCheckboxRadioSwitch,
		NcSelect,
		// Icons
		Plus,
	},
	data() {
		return {
			catalogi: {
				title: '',
				summary: '',
				description: '',
				listed: false,
			},
			loading: false,
			success: null,
			error: false,
			errorCode: '',
			organizations: {},
			organizationsLoading: false,
			hasUpdated: false,
		}
	},
	computed: {
		inputValidation() {
			const catalogiItem = new Catalogi({
				...this.catalogi,
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
	updated() {
		if (navigationStore.modal === 'addCatalog' && !this.hasUpdated) {
			this.fetchOrganizations()
			this.hasUpdated = true
		}
	},
	methods: {
		closeModal() {
			navigationStore.setModal(false)
			this.hasUpdated = false
			this.catalogi = {
				title: '',
				summary: '',
				description: '',
				listed: false,
			}
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
		addCatalog() {
			this.loading = true
			this.error = false

			const catalogiItem = new Catalogi({
				...this.catalogi,
				organization: this.organizations.value?.id,
			})

			catalogiStore.addCatalogi(catalogiItem)
				.then(({ response }) => {
					this.loading = false
					this.success = response.ok

					// Wait for the user to read the feedback then close the model
					const self = this
					setTimeout(function() {
						self.success = null
						self.closeModal()
					}, 2000)
				})
				.catch((err) => {
					this.error = err
					this.loading = false
					this.hasUpdated = false
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

.acm-submit-button {
    margin-block-start: 1rem;
}
</style>
