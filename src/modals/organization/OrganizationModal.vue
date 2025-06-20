<script setup>
import { navigationStore, objectStore } from '../../store/store.js'
</script>

<template>
	<NcModal ref="modalRef"
		:label-id="isEdit ? 'editOrganizationModal' : 'addOrganizationModal'"
		@close="closeModal()">
		<div class="modal__content">
			<h2>Organisatie {{ isEdit ? 'bewerken' : 'toevoegen' }}</h2>

			<div v-if="objectStore.getState('organization').success !== null || objectStore.getState('organization').error">
				<NcNoteCard v-if="objectStore.getState('organization').success" type="success">
					<p>Organisatie succesvol {{ isEdit ? 'bewerkt' : 'toegevoegd' }}</p>
				</NcNoteCard>
				<NcNoteCard v-if="!objectStore.getState('organization').success" type="error">
					<p>Er is iets fout gegaan bij het {{ isEdit ? 'bewerken' : 'toevoegen' }} van Organisatie</p>
				</NcNoteCard>
				<NcNoteCard v-if="objectStore.getState('organization').error" type="error">
					<p>{{ objectStore.getState('organization').error }}</p>
				</NcNoteCard>
			</div>
			<div v-if="objectStore.getState('organization').success === null" class="formContainer">
				<NcTextField
					:disabled="objectStore.isLoading('organization')"
					label="Name"
					:value.sync="organization.name"
					:error="!!inputValidation.fieldErrors?.['name']"
					:helper-text="inputValidation.fieldErrors?.['name']?.[0]" />
				<NcTextField
					:disabled="objectStore.isLoading('organization')"
					label="Samenvatting"
					:value.sync="organization.summary"
					:error="!!inputValidation.fieldErrors?.['summary']"
					:helper-text="inputValidation.fieldErrors?.['summary']?.[0]" />
				<NcTextArea
					:disabled="objectStore.isLoading('organization')"
					label="Beschrijving"
					:value.sync="organization.description"
					:error="!!inputValidation.fieldErrors?.['description']"
					:helper-text="inputValidation.fieldErrors?.['description']?.[0]"
					resize="vertical" />
				<NcTextField
					:disabled="objectStore.isLoading('organization')"
					label="OIN (organisatie-identificatienummer)"
					:value.sync="organization.oin"
					:error="!!inputValidation.fieldErrors?.['oin']"
					:helper-text="inputValidation.fieldErrors?.['oin']?.[0]" />
				<NcTextField
					:disabled="objectStore.isLoading('organization')"
					label="TOOI"
					:value.sync="organization.tooi"
					:error="!!inputValidation.fieldErrors?.['tooi']"
					:helper-text="inputValidation.fieldErrors?.['tooi']?.[0]" />
				<NcTextField
					:disabled="objectStore.isLoading('organization')"
					label="RSIN"
					:value.sync="organization.rsin"
					:error="!!inputValidation.fieldErrors?.['rsin']"
					:helper-text="inputValidation.fieldErrors?.['rsin']?.[0]" />
				<NcTextField
					:disabled="objectStore.isLoading('organization')"
					label="PKI"
					:value.sync="organization.pki"
					:error="!!inputValidation.fieldErrors?.['pki']"
					:helper-text="inputValidation.fieldErrors?.['pki']?.[0]" />
				<NcTextField
					:disabled="objectStore.isLoading('organization')"
					label="Afbeelding (url)"
					:value.sync="organization.image"
					:error="!!inputValidation.fieldErrors?.['image']"
					:helper-text="inputValidation.fieldErrors?.['image']?.[0]" />
			</div>
			<NcButton v-if="objectStore.getState('organization').success === null"
				v-tooltip="inputValidation.errorMessages?.[0]"
				:disabled="!inputValidation.success || objectStore.isLoading('organization')"
				type="primary"
				@click="saveOrganization()">
				<template #icon>
					<NcLoadingIcon v-if="objectStore.isLoading('organization')" :size="20" />
					<Plus v-if="!objectStore.isLoading('organization')" :size="20" />
				</template>
				{{ isEdit ? 'Bewerken' : 'Toevoegen' }}
			</NcButton>
		</div>
	</NcModal>
</template>

<script>
import {
	NcButton,
	NcLoadingIcon,
	NcModal,
	NcNoteCard,
	NcTextArea,
	NcTextField,
} from '@nextcloud/vue'
import Plus from 'vue-material-design-icons/Plus.vue'
import { Organization } from '../../entities/index.js'
import _ from 'lodash'
export default {
	name: 'OrganizationModal',
	components: {
		NcModal,
		NcTextField,
		NcTextArea,
		NcButton,
		NcLoadingIcon,
		NcNoteCard,
		// Icons
		Plus,
	},
	data() {
		return {
			isEdit: !!objectStore.getActiveObject('organization')?.id,
			organization: {
				name: '',
				summary: '',
				description: '',
				oin: '',
				tooi: '',
				rsin: '',
				pki: '',
				image: '',
			},
			errorCode: '',
			closeTimeout: null,
		}
	},
	computed: {
		inputValidation() {
			const item = new Organization({
				...this.organization,
			})

			const result = item.validate()

			return {
				success: result.success,
				errorMessages: result?.error?.issues.map((issue) => `${issue.path.join('.')}: ${issue.message}`) || [],
				fieldErrors: result?.error?.formErrors?.fieldErrors || {},
			}
		},
	},
	mounted() {
		if (this.isEdit) {
			this.organization = {
				...this.organization,
				..._.cloneDeep(objectStore.getActiveObject('organization')),
			}
		}
	},
	methods: {
		closeModal() {
			if (this.closeTimeout) {
				clearTimeout(this.closeTimeout)
				this.closeTimeout = null
			}
			navigationStore.setModal(false)
			this.organization = {
				name: '',
				summary: '',
				description: '',
				oin: '',
				tooi: '',
				rsin: '',
				pki: '',
				image: '',
			}
			objectStore.setState('organization', { success: null, error: null })
		},
		saveOrganization() {
			objectStore.setLoading('organization', true)

			const organizationItem = new Organization({
				...this.organization,
			})

			const operation = this.isEdit
				? objectStore.updateObject('organization', organizationItem.id, organizationItem)
				: objectStore.createObject('organization', organizationItem)

			operation
				.then(() => {
					objectStore.setLoading('organization', false)
					this.success = objectStore.getState('organization').success

					navigationStore.setSelected('organizations')
					// Wait for the user to read the feedback then close the model
					this.closeTimeout = setTimeout(() => {
						this.closeModal()
					}, 2000)
				})
				.catch((err) => {
					objectStore.setState('organization', { error: err })
					objectStore.setLoading('organization', false)
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

.formContainer > * {
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

<style scoped>
.formContainer {
    display: flex;
    flex-direction: column;
}
</style>
