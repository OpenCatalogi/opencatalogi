<script setup>
import { navigationStore, publicationTypeStore } from '../../store/store.js'
</script>

<template>
	<NcModal v-if="navigationStore.modal === 'editPublicationType'"
		ref="modalRef"
		label-id="editPublicationTypeModal"
		@close="navigationStore.setModal(false)">
		<div class="modal__content">
			<h2>Publicatietype bewerken</h2>
			<div v-if="success !== null || error">
				<NcNoteCard v-if="success" type="success">
					<p>Publicatietype succesvol bewerkt</p>
				</NcNoteCard>
				<NcNoteCard v-if="!success" type="error">
					<p>Er is iets fout gegaan bij het bewerken van de publicatietype</p>
				</NcNoteCard>
				<NcNoteCard v-if="error" type="error">
					<p>{{ error }}</p>
				</NcNoteCard>
			</div>
			<div v-if="success == null" class="form-group">
				<NcTextField
					label="Titel"
					:disabled="loading"
					:value.sync="publicationType.title"
					:error="!!inputValidation.fieldErrors?.['title']"
					:helper-text="inputValidation.fieldErrors?.['title']?.[0]" />
				<NcTextField
					label="Samenvatting*"
					:disabled="loading"
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
			<NcButton v-if="success == null"
				v-tooltip="inputValidation.errorMessages?.[0]"
				:disabled="!inputValidation.success || loading"
				type="primary"
				@click="editPublicationType">
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
import { NcButton, NcModal, NcTextField, NcTextArea, NcLoadingIcon, NcNoteCard } from '@nextcloud/vue'
import ContentSaveOutline from 'vue-material-design-icons/ContentSaveOutline.vue'

import { PublicationType } from '../../entities/index.js'

export default {
	name: 'EditPublicationTypeModal',
	components: {
		NcModal,
		NcTextField,
		NcTextArea,
		NcButton,
		NcLoadingIcon,
		NcNoteCard,
		// Icons
		ContentSaveOutline,
	},
	data() {
		return {
			publicationType: {
				title: '',
				summary: '',
				description: '',
			},
			loading: false,
			success: null,
			error: false,
			hasUpdated: false,
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
	mounted() {
		// publicationTypeStore.publicationTypeItem can be false, so only assign publicationTypeStore.publicationTypeItem to publicationType if its NOT false
		publicationTypeStore.publicationTypeItem && (this.publicationType = publicationTypeStore.publicationTypeItem)
	},
	updated() {
		if (navigationStore.modal === 'editPublicationType' && this.hasUpdated) {
			if (this.publicationType.id === publicationTypeStore.publicationTypeItem.id) return
			this.hasUpdated = false
		}
		if (navigationStore.modal === 'editPublicationType' && !this.hasUpdated) {
			publicationTypeStore.publicationTypeItem && (this.publicationType = publicationTypeStore.publicationTypeItem)
			this.fetchData(publicationTypeStore.publicationTypeItem.id)
			this.hasUpdated = true
		}
	},
	methods: {
		fetchData(id) {
			this.loading = true

			publicationTypeStore.getOnePublicationType(id)
				.then(() => {
					this.loading = false
				})
				.catch((err) => {
					this.error = err
					this.loading = false
				})
		},
		editPublicationType() {
			this.loading = true

			const publicationTypeItem = new PublicationType({
				...this.publicationType,
			})

			publicationTypeStore.editPublicationType(publicationTypeItem)
				.then(({ response }) => {
					this.loading = false
					this.success = response.ok

					navigationStore.setSelected('publicationType')
					// Wait for the user to read the feedback then close the model
					const self = this
					setTimeout(function() {
						self.success = null
						navigationStore.setModal(false)
					}, 2000)
				}).catch((err) => {
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

.zaakDetailsContainer {
    margin-block-start: var(--OC-margin-20);
    margin-inline-start: var(--OC-margin-20);
    margin-inline-end: var(--OC-margin-20);
}

.success {
    color: green;
}
</style>
