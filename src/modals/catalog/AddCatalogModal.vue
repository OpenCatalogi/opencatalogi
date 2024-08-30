<script setup>
import { catalogiStore, navigationStore } from '../../store/store.js'
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
					:value.sync="catalogi.title" />
				<NcTextField :disabled="loading"
					label="Samenvatting"
					maxlength="255"
					:value.sync="catalogi.summary" />
				<NcTextField :disabled="loading"
					label="Beschrijving"
					maxlength="255"
					:value.sync="catalogi.description" />
				<NcCheckboxRadioSwitch :disabled="loading"
					label="Publiek vindbaar"
					:checked.sync="catalogi.listed">
					Publiek vindbaar
				</NcCheckboxRadioSwitch>
				<NcSelect v-bind="organisations"
					v-model="organisations.value"
					input-label="Organisatie"
					:loading="organisationsLoading"
					:disabled="loading" />
			</div>
			<NcButton v-if="success === null"
				:disabled="!catalogi.title || loading"
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
			organisations: {},
			organisationsLoading: false,
			hasUpdated: false,
		}
	},
	updated() {
		if (navigationStore.modal === 'addCatalog' && !this.hasUpdated) {
			this.fetchOrganisations()
			this.hasUpdated = true
		}
	},
	methods: {
		closeModal() {
			navigationStore.setModal(false)
			this.catalogi = {
				title: '',
				summary: '',
				description: '',
				listed: false,
			}
		},
		fetchOrganisations() {
			this.organisationsLoading = true
			fetch('/index.php/apps/opencatalogi/api/organisations', {
				method: 'GET',
			})
				.then((response) => {
					response.json().then((data) => {
						this.organisations = {
							options: data.results.map((organisation) => ({
								id: organisation.id,
								label: organisation.title,
							})),
						}
					})
					this.organisationsLoading = false
				})
				.catch((err) => {
					console.error(err)
					this.organisationsLoading = false
				})
		},
		addCatalog() {
			this.loading = true
			this.error = false
			fetch(
				'/index.php/apps/opencatalogi/api/catalogi',
				{
					method: 'POST',
					headers: {
						'Content-Type': 'application/json',
					},
					body: JSON.stringify({
						...this.catalogi,
						organisation: this.organisations.value?.id,
					}),
				},
			)
				.then((response) => {
					this.loading = false
					this.success = response.ok
					// Lets refresh the catalogiList
					catalogiStore.refreshCatalogiList()
					response.json().then((data) => {
						catalogiStore.setCatalogiItem(data)
					})
					// Wait for the user to read the feedback then close the model
					const self = this
					setTimeout(function() {
						self.success = null
						self.hasUpdated = false
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
