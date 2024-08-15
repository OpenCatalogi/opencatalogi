<script setup>
import { navigationStore, publicationStore } from '../../store/store.js'
</script>
<template>
	<NcModal v-if="navigationStore.modal === 'editPublicationData'"
		ref="modalRef"
		label-id="editPublicationPropertyModal"
		@close="navigationStore.setModal(false)">
		<div class="modal__content">
			<h2>Bewerk publicatie eigenschappen</h2>
			<div v-if="success !== null || error">
				<NcNoteCard v-if="success" type="success">
					<p>Publicatie eigenschap succesvol bewerkt</p>
				</NcNoteCard>
				<NcNoteCard v-if="!success" type="error">
					<p>Er is iets fout gegaan bij het bewerken van Publicatie eigenschap</p>
				</NcNoteCard>
				<NcNoteCard v-if="error" type="error">
					<p>{{ error }}</p>
				</NcNoteCard>
			</div>
			<div v-if="success === null" class="form-group">
				<NcTextField :disabled="loading"
					:label="publicationStore.publicationDataKey"
					:value.sync="publicationStore.publicationItem.data[publicationStore.publicationDataKey]" />
			</div>

			<span class="flex-horizontal">
				<NcButton v-if="success === null"
					:disabled="!publicationStore.publicationItem.data[publicationStore.publicationDataKey] || loading"
					type="primary"
					@click="updatePublication(publicationStore.publicationItem.id)">
					<template #icon>
						<span>
							<NcLoadingIcon v-if="loading" :size="20" />
							<ContentSaveOutline v-if="!loading" :size="20" />
						</span>
					</template>
					Opslaan
				</NcButton>
				<NcButton
					@click="navigationStore.setModal(false)">
					{{ success ? 'Sluiten' : 'Annuleer' }}
				</NcButton>
			</span>
		</div>
	</NcModal>
</template>

<script>
import {
	NcButton,
	NcModal,
	NcTextField,
	NcLoadingIcon,
	NcNoteCard,
} from '@nextcloud/vue'
import ContentSaveOutline from 'vue-material-design-icons/ContentSaveOutline.vue'

export default {
	name: 'EditPublicationDataModal',
	components: {
		NcModal,
		NcTextField,
		NcButton,
		NcLoadingIcon,
		NcNoteCard,
		// icons
		ContentSaveOutline,
	},
	data() {
		return {
			publication: {
				title: '',
				description: '',
				catalogi: '',
				metaData: '',
				data: '',
				id: '',
			},
			catalogi: {
				value: [],
				options: [],
			},
			metaData: {
				value: [],
				options: [],
			},
			loading: false,
			success: null,
			error: false,
		}
	},
	updated() {
		if (navigationStore.modal === 'editPublicationDataModal' && !this.hasUpdated) {
			this.fetchCatalogi()
			this.fetchMetaData()
			this.fetchData(publicationStore.publicationItem.id)
			this.hasUpdated = true
		}
	},
	methods: {
		fetchData(id) {
			this.publicationLoading = true
			fetch(
				`/index.php/apps/opencatalogi/api/publications/${id}`,
				{
					method: 'GET',
				},
			)
				.then((response) => {
					response.json().then((data) => {
						this.publication = data
						// this.publication.data = JSON.stringify(data.data)
						this.catalogi.value = [data.catalogi?.id]
						this.metaData.value = [data.metaData?.id]
					})
					this.publicationLoading = false
				})
				.catch((err) => {
					console.error(err)
					this.publicationLoading = false
				})
		},
		fetchCatalogi() {
			this.catalogiLoading = true
			fetch('/index.php/apps/opencatalogi/api/catalogi', {
				method: 'GET',
			})
				.then((response) => {
					response.json().then((data) => {

						this.catalogi = {
							value: this.catalogi.value,
							inputLabel: 'Catalogi',
							options: Object.entries(data.results).map((catalog) => ({
								id: catalog[1].id,
								label: catalog[1].name,
							})),

						}
					})
					this.catalogiLoading = false
				})
				.catch((err) => {
					console.error(err)
					this.catalogiLoading = false
				})
		},
		fetchMetaData() {
			this.metaDataLoading = true
			fetch('/index.php/apps/opencatalogi/api/metadata', {
				method: 'GET',
			})
				.then((response) => {
					response.json().then((data) => {

						this.metaData = {
							inputLabel: 'MetaData',
							options: Object.entries(data.results).map((metaData) => ({
								id: metaData[1].id,
								label: metaData[1].name,
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
		updatePublication(id) {
			this.loading = true
			fetch(
				`/index.php/apps/opencatalogi/api/publications/${id}`,
				{
					method: 'PUT',
					headers: {
						'Content-Type': 'application/json',
					},
					body: JSON.stringify({
						...publicationStore.publicationItem,
						id: publicationStore.publicationItem.id,
						catalogi: publicationStore.publicationItem.catalogi.id,
						metaData: publicationStore.publicationItem.metaData.id,
					}),
				},
			)
				.then((response) => {
					this.loading = false
					this.success = response.ok

					publicationStore.refreshPublicationList()
					response.json().then((data) => {
						publicationStore.setPublicationItem(data)
					})

					const self = this
					setTimeout(() => {
						self.success = null
						navigationStore.setModal(false)
					}, 2000)
				})
				.catch((err) => {
					this.loading = false
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

.flex-horizontal {
    display: flex;
    gap: 4px;
}
</style>
