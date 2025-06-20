<script setup>
import { navigationStore, objectStore } from '../../store/store.js'
</script>

<template>
	<NcModal v-if="navigationStore.modal === 'catalog'"
		ref="modalRef"
		:label-id="isEdit ? 'editCatalogModal' : 'addCatalogModal'"
		@close="closeModal">
		<div class="modal__content">
			<h2>Catalogus {{ isEdit ? 'bewerken' : 'toevoegen' }}</h2>
			<div v-if="objectStore.getState('catalog').success !== null || objectStore.getState('catalog').error">
				<NcNoteCard v-if="objectStore.getState('catalog').success" type="success">
					<p>{{ isEdit ? 'Catalogus succesvol bewerkt' : 'Catalogus succesvol toegevoegd' }}</p>
				</NcNoteCard>
				<NcNoteCard v-if="!objectStore.getState('catalog').success" type="error">
					<p>{{ isEdit ? 'Er is iets fout gegaan bij het bewerken van de catalogus' : 'Er is iets fout gegaan bij het toevoegen van catalogus' }}</p>
				</NcNoteCard>
				<NcNoteCard v-if="objectStore.getState('catalog').error" type="error">
					<p>{{ objectStore.getState('catalog').error }}</p>
				</NcNoteCard>
			</div>
			<div v-if="objectStore.getState('catalog').success === null && !objectStore.isLoading('catalog')" class="form-group">
				<NcTextField :disabled="objectStore.isLoading('catalog')"
					label="Titel*"
					maxlength="255"
					:value.sync="catalogi.title"
					:error="!!inputValidation.fieldErrors?.['title']"
					:helper-text="inputValidation.fieldErrors?.['title']?.[0]" />
				<NcTextField :disabled="objectStore.isLoading('catalog')"
					label="Samenvatting"
					maxlength="255"
					:value.sync="catalogi.summary"
					:error="!!inputValidation.fieldErrors?.['summary']"
					:helper-text="inputValidation.fieldErrors?.['summary']?.[0]" />
				<NcTextField :disabled="objectStore.isLoading('catalog')"
					label="Beschrijving"
					maxlength="255"
					:value.sync="catalogi.description"
					:error="!!inputValidation.fieldErrors?.['description']"
					:helper-text="inputValidation.fieldErrors?.['description']?.[0]" />
				<NcCheckboxRadioSwitch :disabled="objectStore.isLoading('catalog')"
					label="Publiek vindbaar"
					:checked.sync="catalogi.listed">
					Publiek vindbaar
				</NcCheckboxRadioSwitch>
				<NcSelect v-model="selectedOrganization"
					:options="organizationOptions"
					input-label="Organisatie"
					:disabled="objectStore.isLoading('catalog')" />
				<NcSelect v-model="selectedRegisters"
					:options="registerOptions"
					input-label="Registers"
					:disabled="objectStore.isLoading('catalog')"
					multiple />
				<NcSelect v-model="selectedSchemas"
					:options="schemaOptions"
					input-label="Schema's"
					:disabled="objectStore.isLoading('catalog')"
					multiple />
			</div>
			<div v-if="objectStore.isLoading('catalog')" class="loading-status">
				<NcLoadingIcon :size="20" />
				<span>{{ isEdit ? 'Catalogus wordt bewerkt...' : 'Catalogus wordt toegevoegd...' }}</span>
			</div>
			<NcButton v-if="objectStore.getState('catalog').success === null && !objectStore.isLoading('catalog')"
				v-tooltip="inputValidation.errorMessages?.[0]"
				:disabled="!inputValidation.success || objectStore.isLoading('catalog')"
				type="primary"
				class="catalog-submit-button"
				@click="saveCatalog">
				<template #icon>
					<ContentSaveOutline :size="20" />
				</template>
				{{ isEdit ? 'Opslaan' : 'Toevoegen' }}
			</NcButton>
		</div>
	</NcModal>
</template>

<script>
import { NcButton, NcModal, NcTextField, NcLoadingIcon, NcNoteCard, NcCheckboxRadioSwitch, NcSelect } from '@nextcloud/vue'
import ContentSaveOutline from 'vue-material-design-icons/ContentSaveOutline.vue'
import { Catalogi } from '../../entities/index.js'

export default {
	name: 'CatalogModal',
	components: {
		NcModal,
		NcTextField,
		NcButton,
		NcLoadingIcon,
		NcNoteCard,
		NcCheckboxRadioSwitch,
		NcSelect,
		// Icons
		ContentSaveOutline,
	},
	data() {
		return {
			catalogi: {
				title: '',
				summary: '',
				description: '',
				listed: false,
				registers: [],
				schemas: [],
				filters: {},
			},
			selectedOrganization: null,
			selectedRegisters: [],
			selectedSchemas: [],
			hasUpdated: false,
		}
	},
	computed: {
		isEdit() {
			return !!objectStore.getActiveObject('catalog')
		},
		organizationOptions() {
			return objectStore.getCollection('organization').results.map((organization) => ({
				id: organization.id,
				label: organization.name,
			}))
		},
		registerOptions() {
			return objectStore.availableRegisters.map(register => ({
				id: register.id,
				label: register.title,
			}))
		},
		schemaOptions() {
			// Get the selected register IDs
			const selectedRegisterIds = this.selectedRegisters.map(register => register.id)

			// Filter available registers to only those that are selected
			const selectedAvailableRegisters = objectStore.availableRegisters.filter(register =>
				selectedRegisterIds.includes(register.id),
			)

			// Get all unique schema IDs from the selected registers
			const availableSchemaIds = [...new Set(selectedAvailableRegisters.flatMap(register => register.schemas.map(schema => schema.id)))]

			// Filter and map the schemas
			return objectStore.availableSchemas
				.filter(schema => availableSchemaIds.includes(schema.id))
				.map(schema => ({
					id: schema.id,
					label: `${schema.title} (${schema.registerTitle})`,
				}))
		},
		inputValidation() {
			// Map selected objects to their IDs for validation
			const registers = this.selectedRegisters.map(register => register.id)
			const schemas = this.selectedSchemas.map(schema => schema.id)

			const catalogiItem = new Catalogi({
				...this.catalogi,
				organization: this.selectedOrganization?.id,
				registers,
				schemas,
				filters: {},
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
		if (navigationStore.modal === 'catalog' && !this.hasUpdated) {
			this.hasUpdated = true

			if (this.isEdit) {
				const activeCatalog = objectStore.getActiveObject('catalog')

				this.catalogi = {
					...activeCatalog,
					filters: Array.isArray(activeCatalog.filters) ? {} : activeCatalog.filters || {},
				}

				// Find and set the selected organization
				const org = objectStore.getCollection('organization').results.find(
					org => org.id.toString() === activeCatalog.organization.toString(),
				)

				this.selectedOrganization = org ? { id: org.id, label: org.title } : null

				// Map existing registers and schemas to the format expected by NcSelect
				this.selectedRegisters = activeCatalog.registers.map(id => ({
					id,
					label: objectStore.availableRegisters.find(r => r.id === id)?.title || id,
				}))

				this.selectedSchemas = activeCatalog.schemas.map(id => ({
					id,
					label: objectStore.availableSchemas.find(s => s.id === id)?.title || id,
				}))
			}
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
				registers: [],
				schemas: [],
				filters: {},
			}
			this.selectedOrganization = null
			this.selectedRegisters = []
			this.selectedSchemas = []
			// Reset the object store state
			objectStore.setState('catalog', { success: null, error: null })
		},
		saveCatalog() {
			// Map selected objects to their IDs for saving
			const registers = this.selectedRegisters.map(register => register.id)
			const schemas = this.selectedSchemas.map(schema => schema.id)

			const catalogiItem = new Catalogi({
				...this.catalogi,
				organization: this.selectedOrganization?.id,
				registers,
				schemas,
				filters: {},
			})

			if (this.isEdit) {
				objectStore.updateObject('catalog', catalogiItem.id, catalogiItem)
					.then(() => {
						// Wait for the user to read the feedback then close the model
						const self = this
						setTimeout(function() {
							self.closeModal()
						}, 2000)
					})
			} else {
				objectStore.createObject('catalog', catalogiItem)
					.then(() => {
						// Wait for the user to read the feedback then close the model
						const self = this
						setTimeout(function() {
							self.closeModal()
						}, 2000)
					})
			}
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

.catalog-submit-button {
    margin-block-start: 1rem;
}

.loading-status {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
    margin: 1rem 0;
    color: var(--color-text-lighter);
}
</style>

<style scoped>
.form-group {
	display: flex;
	flex-direction: column;
}
</style>
