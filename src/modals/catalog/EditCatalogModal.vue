<script setup>
import { catalogiStore, navigationStore } from '../../store/store.js'
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
					:value.sync="catalogiStore.catalogiItem.title"
					required />
				<NcTextField :disabled="loading"
					label="Samenvatting"
					maxlength="255"
					:value.sync="catalogiStore.catalogiItem.summary" />
				<NcTextField :disabled="loading"
					label="Beschrijving"
					maxlength="255"
					:value.sync="catalogiStore.catalogiItem.description" />
				<NcCheckboxRadioSwitch :disabled="loading"
					label="Publiek vindbaar"
					:checked.sync="catalogiStore.catalogiItem.listed">
					Publiek vindbaar
				</NcCheckboxRadioSwitch>
				<NcSelect v-bind="organisations"
					v-model="organisations.value"
					input-label="Organisatie"
					:loading="organisationsLoading" />
			</div>
			<NcButton v-if="success === null"
				:disabled="loading"
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
				organisation: '',
			},
			loading: false,
			success: null,
			error: false,
			organisations: {},
			organisationsLoading: false,
			hasUpdated: false,
		}
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
			this.fetchOrganisations()
			this.hasUpdated = true
		}
	},
	methods: {
		closeModal() {
			navigationStore.modal = false
		},
		fetchData(id) {
			this.loading = true
			fetch(
				`/index.php/apps/opencatalogi/api/catalogi/${id}`,
				{
					method: 'GET',
				},
			)
				.then((response) => {
					response.json().then((data) => {
						catalogiStore.setCatalogiItem(data)
						this.catalogiItem = catalogiStore.catalogiItem
					})
					this.loading = false
				})
				.catch((err) => {
					console.error(err)
					this.loading = false
				})
		},
		fetchOrganisations() {
			this.organisationsLoading = true
			fetch('/index.php/apps/opencatalogi/api/organisations', {
				method: 'GET',
			})
				.then((response) => {
					response.json().then((data) => {
						const selectedOrganisation = data.results.filter((org) => org?.id === catalogiStore.catalogiItem?.organisation?.id) || null

						this.organisations = {
							options: data.results.map((organisation) => ({
								id: organisation.id,
								label: organisation.title,
							})),
							value: selectedOrganisation[0]
								? {
									id: selectedOrganisation?.id,
									label: selectedOrganisation?.title,
								}
								: null,
						}
					})
					this.organisationsLoading = false
				})
				.catch((err) => {
					console.error(err)
					this.organisationsLoading = false
				})
		},
		editCatalog() {
			this.loading = true
			this.error = false
			fetch(
				`/index.php/apps/opencatalogi/api/catalogi/${catalogiStore.catalogiItem.id}`,
				{
					method: 'PUT',
					headers: {
						'Content-Type': 'application/json',
					},
					body: JSON.stringify({
						...catalogiStore.catalogiItem,
						organisation: this.organisations?.value?.id,
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
