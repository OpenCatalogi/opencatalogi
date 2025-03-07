<script setup>
import { navigationStore, publicationTypeStore } from '../../store/store.js'
</script>

<template>
	<NcModal v-if="navigationStore.modal === 'addPublicationType'"
		ref="modalRef"
		label-id="addPublicationTypeModal"
		@close="closeModal">
		<div class="modal__content">
			<h2>Publicatietype toevoegen</h2>
			<div v-if="success !== null || error">
				<NcNoteCard v-if="success" type="success">
					<p>Publicatietype succesvol toegevoegd</p>
				</NcNoteCard>
				<NcNoteCard v-if="!success" type="error">
					<p>Er is iets fout gegaan bij het toevoegen van publicatietype</p>
				</NcNoteCard>
				<NcNoteCard v-if="error" type="error">
					<p>{{ error }}</p>
				</NcNoteCard>
			</div>
			<div v-if="success === null" class="form-group">
				<NcTextField
					label="Titel"
					:value.sync="publicationType.title"
					:error="!!inputValidation.fieldErrors?.['title']"
					:helper-text="inputValidation.fieldErrors?.['title']?.[0]" />
				<NcTextField :disabled="loading"
					label="Samenvatting*"
					:value.sync="publicationType.summary"
					:error="!!inputValidation.fieldErrors?.['summary']"
					:helper-text="inputValidation.fieldErrors?.['summary']?.[0]" />
				<NcTextArea
					label="Beschrijving"
					:disabled="loading"
					:value.sync="publicationType.description"
					:error="!!inputValidation.fieldErrors?.['description']"
					:helper-text="inputValidation.fieldErrors?.['description']?.[0]" />
			</div>
			<NcButton v-if="success === null"
				v-tooltip="inputValidation.errorMessages?.[0]"
				:disabled="!inputValidation.success || loading"
				type="primary"
				@click="addPublicationType">
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
import { NcButton, NcModal, NcTextField, NcTextArea, NcLoadingIcon, NcNoteCard } from '@nextcloud/vue'
import Plus from 'vue-material-design-icons/Plus.vue'

import { PublicationType } from '../../entities/index.js'

export default {
	name: 'AddPublicationTypeModal',
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
			publicationType: {
				title: '',
				description: '',
				summary: '',
				required: '',
			},
			loading: false,
			success: null,
			error: false,
		}
	},
	computed: {
		inputValidation() {
			const publicationTypeItem = new PublicationType({
				...this.publicationType,
			})

			const result = publicationTypeItem.validate()

			return {
				success: result.success,
				errorMessages: result?.error?.issues.map((issue) => `${issue.path.join('.')}: ${issue.message}`) || [],
				fieldErrors: result?.error?.formErrors?.fieldErrors || {},
			}
		},
	},
	methods: {
		closeModal() {
			this.success = null
			this.publicationType = {
				title: '',
				description: '',
				summary: '',
				required: '',
			}
			navigationStore.setModal(false)
		},
		addPublicationType() {
			this.loading = true

			const publicationTypeItem = new PublicationType({
				...this.publicationType,
			})

			publicationTypeStore.addPublicationType(publicationTypeItem)
				.then(({ response }) => {
					// Set the form
					this.loading = false
					this.success = response.ok

					navigationStore.setSelected('publicationType')
					// Update the list
					const self = this
					setTimeout(function() {
						self.closeModal()
					}, 2000)
				})
				.catch((err) => {
					this.publicationTypeLoading = false
					this.error = err
					console.error(err)
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
</style>
