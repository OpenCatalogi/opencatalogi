<script setup>
import { navigationStore, organizationStore } from '../../store/store.js'
</script>
<template>
	<NcModal ref="modalRef"
		label-id="addOrganizationModal"
		@close="navigationStore.setModal(false)">
		<div class="modal__content">
			<h2>Organisatie {{ IS_EDIT ? 'bewerken' : 'toevoegen' }}</h2>

			<div v-if="success !== null || error">
				<NcNoteCard v-if="success" type="success">
					<p>Organisatie succesvol {{ IS_EDIT ? 'bewerkt' : 'toegevoegd' }}</p>
				</NcNoteCard>
				<NcNoteCard v-if="!success" type="error">
					<p>Er is iets fout gegaan bij het {{ IS_EDIT ? 'bewerken' : 'toevoegen' }} van Organisatie</p>
				</NcNoteCard>
				<NcNoteCard v-if="error" type="error">
					<p>{{ error }}</p>
				</NcNoteCard>
			</div>
			<div class="formContainer">
				<div v-if="success === null" class="form-group">
					<NcTextField
						:disabled="loading"
						label="Titel"
						:value.sync="organization.title"
						:error="!!inputValidation.fieldErrors?.['title']"
						:helper-text="inputValidation.fieldErrors?.['title']?.[0]" />
					<NcTextField
						:disabled="loading"
						label="Samenvatting"
						:value.sync="organization.summary"
						:error="!!inputValidation.fieldErrors?.['summary']"
						:helper-text="inputValidation.fieldErrors?.['summary']?.[0]" />
					<NcTextArea
						:disabled="loading"
						label="Beschrijving"
						:value.sync="organization.description"
						:error="!!inputValidation.fieldErrors?.['description']"
						:helper-text="inputValidation.fieldErrors?.['description']?.[0]" />
					<NcTextField
						:disabled="loading"
						label="OIN (organisatie-identificatienummer)"
						:value.sync="organization.oin"
						:error="!!inputValidation.fieldErrors?.['oin']"
						:helper-text="inputValidation.fieldErrors?.['oin']?.[0]" />
					<NcTextField
						:disabled="loading"
						label="TOOI"
						:value.sync="organization.tooi"
						:error="!!inputValidation.fieldErrors?.['tooi']"
						:helper-text="inputValidation.fieldErrors?.['tooi']?.[0]" />
					<NcTextField
						:disabled="loading"
						label="RSIN"
						:value.sync="organization.rsin"
						:error="!!inputValidation.fieldErrors?.['rsin']"
						:helper-text="inputValidation.fieldErrors?.['rsin']?.[0]" />
					<NcTextField
						:disabled="loading"
						label="PKI"
						:value.sync="organization.pki"
						:error="!!inputValidation.fieldErrors?.['pki']"
						:helper-text="inputValidation.fieldErrors?.['pki']?.[0]" />
					<NcTextField
						:disabled="loading"
						label="Afbeelding (url)"
						:value.sync="organization.image"
						:error="!!inputValidation.fieldErrors?.['image']"
						:helper-text="inputValidation.fieldErrors?.['image']?.[0]" />
				</div>
			</div>
			<NcButton v-if="success === null"
				v-tooltip="inputValidation.errorMessages?.[0]"
				:disabled="!inputValidation.success || loading"
				type="primary"
				@click="saveOrganization()">
				<template #icon>
					<NcLoadingIcon v-if="loading" :size="20" />
					<Plus v-if="!loading" :size="20" />
				</template>
				{{ IS_EDIT ? 'Bewerken' : 'Toevoegen' }}
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
	name: 'OrganizationFormModal',
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
			IS_EDIT: !!organizationStore.organizationItem?.id,
			organization: {
				title: '',
				summary: '',
				description: '',
				oin: '',
				tooi: '',
				rsin: '',
				pki: '',
				image: '',
			},
			errorCode: '',
			organizationLoading: false,
			loading: false,
			success: null,
			error: false,
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
		if (this.IS_EDIT) {
			this.organization = {
				...this.organization,
				..._.cloneDeep(organizationStore.organizationItem),
			}
		}
	},
	methods: {
		isJsonString(str) {
			try {
				JSON.parse(str)
			} catch (e) {
				return false
			}
			return true
		},
		saveOrganization() {
			this.loading = true
			this.error = false

			const organizationItem = new Organization({
				...this.organization,
			})

			organizationStore.saveOrganization(organizationItem)
				.then(({ response }) => {
					this.loading = false
					this.success = response.ok

					navigationStore.setSelected('organizations')
					// Wait for the user to read the feedback then close the model
					setTimeout(function() {
						navigationStore.setModal(false)
					}, 2000)
				})
				.catch((err) => {
					this.error = err
					this.loading = false
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
