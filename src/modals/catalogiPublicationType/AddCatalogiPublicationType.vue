<script setup>
import { catalogiStore, navigationStore, publicationTypeStore } from '../../store/store.js'
</script>

<template>
	<NcModal v-if="navigationStore.modal === 'addCatalogiPublicationType'"
		ref="modalRef"
		label-id="addCatalogiPublicationType"
		@close="closeModal">
		<div class="modal__content">
			<h2>Publicatietype toevoegen aan {{ catalogiItem.title }}</h2>
			<div v-if="success !== null || error">
				<NcNoteCard v-if="success" type="success">
					<p>Publicatietype succesvol toegevoegd</p>
				</NcNoteCard>
				<NcNoteCard v-if="!success" type="error">
					<p>Er is iets fout gegaan bij het toevoegen van een publicatietype</p>
				</NcNoteCard>
				<NcNoteCard v-if="error" type="error">
					<p>{{ error }}</p>
				</NcNoteCard>
			</div>
			<div v-if="success === null" class="form-group">
				<NcSelect v-bind="publicationTypes"
					v-model="publicationTypes.value"
					input-label="Publicatietype"
					:loading="publicationTypeLoading"
					required />
			</div>
			<NcButton v-if="success === null"
				:disabled="!publicationTypes?.value || loading"
				type="primary"
				@click="addCatalogPublicationType">
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
import { NcButton, NcModal, NcLoadingIcon, NcNoteCard, NcSelect } from '@nextcloud/vue'
import Plus from 'vue-material-design-icons/Plus.vue'

import { Catalogi } from '../../entities/index.js'

export default {
	name: 'AddCatalogiPublicationType',
	components: {
		NcModal,
		NcButton,
		NcLoadingIcon,
		NcNoteCard,
		NcSelect,
		// Icons
		Plus,
	},
	data() {
		return {
			catalogiItem: {
				title: '',
				summary: '',
				description: '',
				listed: false,
				publicationTypes: [],
			},
			publicationTypes: {},
			publicationTypeLoading: false,
			loading: false,
			success: null,
			error: false,
			errorCode: '',
			hasUpdated: false,
		}
	},
	mounted() {
		// catalogiStore.catalogiItem can be false, so only assign catalogiStore.catalogiItem to catalogiItem if its NOT false

		catalogiStore.catalogiItem && (this.catalogiItem = catalogiStore.catalogiItem)
	},
	updated() {
		if (navigationStore.modal === 'addCatalogiPublicationType' && this.hasUpdated) {
			if (this.catalogiItem.id === catalogiStore.catalogiItem.id) return
			this.hasUpdated = false
		}
		if (navigationStore.modal === 'addCatalogiPublicationType' && !this.hasUpdated) {
			catalogiStore.catalogiItem && (this.catalogiItem = catalogiStore.catalogiItem)
			this.fetchData(catalogiStore.catalogiItem.id)
			this.fetchPublicationType(catalogiStore.catalogiItem?.publicationTypes || [])
			this.hasUpdated = true
		}
	},
	methods: {
		closeModal() {
			navigationStore.setModal(false)
			this.catalogiItem = {
				title: '',
				summary: '',
				description: '',
				listed: false,
				publicationTypes: [],
			}
		},
		fetchData(id) {
			this.loading = true

			catalogiStore.getOneCatalogi(id)
				.then(({ response, data }) => {
					this.catalogiItem = catalogiStore.catalogiItem

					this.loading = false
				})
				.catch((err) => {
					console.error(err)
					this.loading = false
				})
		},
		fetchPublicationType(publicationTypeList) {
			this.publicationTypeLoading = true

			publicationTypeStore.getAllPublicationTypes()
				.then(({ response, data }) => {
					const filteredData = data.filter((publicationType) =>
						!publicationTypeList.includes(publicationType?.id),
					)

					this.publicationTypes = {
						options: filteredData.map((publicationType) => ({
							source: publicationType.source,
							id: publicationType.id,
							label: publicationType.title,
						})),
					}

					this.publicationTypeLoading = false
				})
				.catch((err) => {
					console.error(err)
					this.publicationTypeLoading = false
				})
		},
		addCatalogPublicationType() {
			this.loading = true
			this.error = false

			this.catalogiItem.publicationTypes.push(this.publicationTypes.value.id)

			const newCatalogiItem = new Catalogi({
				...this.catalogiItem,
				publicationTypes: this.catalogiItem.publicationTypes,
			})

			catalogiStore.editCatalogi(newCatalogiItem)
				.then(({ response }) => {
					this.loading = false
					this.success = response.ok

					// Wait for the user to read the feedback then close the model
					const self = this
					setTimeout(function() {
						self.success = null
						self.closeModal()
					}, 2000)

					this.hasUpdated = false
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
</style>
