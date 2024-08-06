<script setup>
import { navigationStore, organisationStore } from '../../store/store.js'
</script>
<template>
	<NcModal
		v-if="navigationStore.modal === 'organisationAdd'"
		ref="modalRef"
		label-id="addOrganisationModal"
		@close="navigationStore.setModal(false)">
		<div class="modal__content">
			<h2>Organisatie toevoegen</h2>
			<div v-if="success !== null || error">
				<NcNoteCard v-if="success" type="success">
					<p>Organisatie succesvol toegevoegd</p>
				</NcNoteCard>
				<NcNoteCard v-if="!success" type="error">
					<p>Er is iets fout gegaan bij het toevoegen van Organisatie</p>
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
						:value.sync="organisation.title" />
					<NcTextField
						:disabled="loading"
						label="Samenvatting"
						:value.sync="organisation.summary" />
					<NcTextArea
						:disabled="loading"
						label="Beschrijving"
						:value.sync="organisation.description" />
					<NcTextField
						:disabled="loading"
						label="OIN (organisatie-identificatienummer)"
						:value.sync="organisation.oin" />
					<NcTextField
						:disabled="loading"
						label="TOOI"
						:value.sync="organisation.tooi" />
					<NcTextField
						:disabled="loading"
						label="RSIN"
						:value.sync="organisation.rsin" />
					<NcTextField
						:disabled="loading"
						label="PKI"
						:value.sync="organisation.pki" />
				</div>
			</div>
			<NcButton
				v-if="success === null"
				:disabled="!organisation.title || loading"
				type="primary"
				@click="addOrganisation()">
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
	NcLoadingIcon,
	NcModal,
	NcNoteCard,
	NcTextArea,
	NcTextField,
} from '@nextcloud/vue'
import ContentSaveOutline from 'vue-material-design-icons/ContentSaveOutline.vue'

export default {
	name: 'AddOrganisationModal',
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
			organisation: {
				title: '',
				summary: '',
				description: '',
				oin: '',
				tooi: '',
				rsin: '',
				pki: '',
			},

			errorCode: '',
			organisationLoading: false,
			hasUpdated: false,
			loading: false,
			success: null,
			error: false,
		}
	},
	updated() {
		if (navigationStore.modal === 'organisationAdd' && !this.hasUpdated) {
			this.hasUpdated = true
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
		addOrganisation() {
			this.loading = true
			this.error = false
			fetch('/index.php/apps/opencatalogi/api/organisations', {
				method: 'POST',
				headers: {
					'Content-Type': 'application/json',
				},
				body: JSON.stringify({
					...this.organisation,
				}),
			})
				.then((response) => {
					this.loading = false
					this.success = response.ok
					// Lets refresh the organisationList
					organisationStore.refreshOrganisationList()
					response.json().then((data) => {
						organisationStore.setOrganisationList(data)
					})
					navigationStore.setSelected('organisations')
					// Wait for the user to read the feedback then close the model
					const self = this
					setTimeout(function() {
						self.success = null
						navigationStore.setModal(false)
						self.organisation = {
							title: '',
							summary: '',
							description: '',
							oin: '',
							tooi: '',
							rsin: '',
							pki: '',
						}
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
