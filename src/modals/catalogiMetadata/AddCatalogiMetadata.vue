<script setup>
import { catalogiStore, navigationStore } from '../../store/store.js'
</script>

<template>
	<NcModal v-if="navigationStore.modal === 'addCatalogiMetadata'"
		ref="modalRef"
		label-id="addCatalogModal"
		@close="closeModal">
		<div class="modal__content">
			<h2>Publicatie type toevoegen aan Catalogus</h2>
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
				<NcSelect v-bind="metaData"
					v-model="metaData.value"
					input-label="Publicatie type"
					:loading="metaDataLoading"
					required />
			</div>
			<NcButton v-if="success === null"
				:disabled="!metaData?.value?.source || loading"
				type="primary"
				@click="addCatalogMetadata">
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

export default {
	name: 'AddCatalogiMetadata',
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
			},
			metaData: {},
			metaDataLoading: false,
			loading: false,
			success: null,
			error: false,
			errorCode: '',
		}
	},
	mounted() {
		// catalogiStore.catalogiItem can be false, so only assign catalogiStore.catalogiItem to catalogiItem if its NOT false
		catalogiStore.catalogiItem && (this.catalogiItem = catalogiStore.catalogiItem)
	},
	updated() {
		if (navigationStore.modal === 'addCatalogiMetadata' && this.hasUpdated) {
			if (this.catalogiItem.id === catalogiStore.catalogiItem.id) return
			this.hasUpdated = false
		}
		if (navigationStore.modal === 'addCatalogiMetadata' && !this.hasUpdated) {
			catalogiStore.catalogiItem && (this.catalogiItem = catalogiStore.catalogiItem)
			this.fetchData(catalogiStore.catalogiItem.id)
			this.fetchMetaData(catalogiStore.catalogiItem?.metadata || [])
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
		fetchMetaData(metadataList) {
			this.metaDataLoading = true
			fetch('/index.php/apps/opencatalogi/api/metadata', {
				method: 'GET',
			})
				.then((response) => {
					response.json().then((data) => {
						const metadataListAsString = metadataList.map(String)
						const filteredData = data.results.filter((meta) => !metadataListAsString.includes(meta?.id.toString()))

						this.metaData = {
							options: filteredData.map((metaData) => ({
								source: metaData.source,
								label: metaData.title,
							})),
						}
					})
					this.metaDataLoading = false
				})
				.catch((err) => {
					console.error(err)
					this.metaDataLoading = false
				})
		},
		addCatalogMetadata() {
			this.loading = true
			this.error = false

			this.catalogiItem.metadata.push(this.metaData.value.source)
			if (!this.metaData?.value?.source) {
				this.error = 'Publicatie type heeft geen bron, kan niet gekoppeld worden'
				this.metaDataLoading = false

				return
			}

			fetch(
				`/index.php/apps/opencatalogi/api/catalogi/${this.catalogiItem.id}`,
				{
					method: 'PUT',
					headers: {
						'Content-Type': 'application/json',
					},
					body: JSON.stringify({
						...this.catalogiItem,
						metadata: this.catalogiItem.metadata,
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
						self.closeModal()
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

.zaakDetailsContainer {
    margin-block-start: var(--OC-margin-20);
    margin-inline-start: var(--OC-margin-20);
    margin-inline-end: var(--OC-margin-20);
}

.success {
    color: green;
}
</style>
