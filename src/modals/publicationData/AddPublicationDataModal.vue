<script setup>
import { navigationStore, publicationStore } from '../../store/store.js'
</script>
<template>
	<NcModal v-if="navigationStore.modal === 'addPublicationData'"
		ref="modalRef"
		label-id="addPublicationPropertyModal"
		@close="navigationStore.setModal(false)">
		<div class="modal__content">
			<h2>Publicatie eigenschap toevoegen</h2>
			<div v-if="success !== null || error">
				<NcNoteCard v-if="success" type="success">
					<p>Publicatie eigenschap succesvol toegevoegd</p>
				</NcNoteCard>
				<NcNoteCard v-if="!success" type="error">
					<p>Er is iets fout gegaan bij het toevoegen van Publicatie eigenschap</p>
				</NcNoteCard>
				<NcNoteCard v-if="error" type="error">
					<p>{{ error }}</p>
				</NcNoteCard>
			</div>
			<div v-if="success === null" class="form-group">
				<NcSelect v-bind="mapMetadataEigenschappen"
					v-model="eigenschappen.value"
					required />-

				<NcTextField :disabled="loading"
					label="Data"
					:value.sync="data"
					:loading="loading" />
			</div>

			<span class="flex-horizontal">
				<NcButton v-if="success === null"
					:disabled="loading || !eigenschappen.value?.id || !data"
					type="primary"
					@click="AddPublicatieEigenschap()">
					<template #icon>
						<span>
							<NcLoadingIcon v-if="loading" :size="20" />
							<Plus v-if="!loading" :size="20" />
						</span>
					</template>
					Toevoegen
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
	NcNoteCard,
	NcLoadingIcon,
	NcSelect,
} from '@nextcloud/vue'

// icons
import Plus from 'vue-material-design-icons/Plus.vue'

export default {
	name: 'AddPublicationDataModal',
	components: {
		NcModal,
		NcTextField,
		NcSelect,
		NcButton,
		NcNoteCard,
		NcLoadingIcon,
	},
	data() {
		return {
			eigenschappen: {},
			data: '',
			loading: false,
			success: null,
			error: false,
		}
	},
	computed: {
		mapMetadataEigenschappen() {
			return {
				inputLabel: 'Publicatie type eigenschap',
				options: Object.values(publicationStore.publicationItem?.metaData?.properties)
					.filter((prop) => !Object.keys(publicationStore.publicationItem?.data).includes(prop.title))
					.map((prop) => ({
						id: prop.title,
						label: prop.title,
					})),
			}
		},
	},
	methods: {
		AddPublicatieEigenschap() {
			this.loading = true

			const bodyData = publicationStore.publicationItem
			bodyData.data[this.eigenschappen.value?.label] = this.data
			delete bodyData.publicationDate

			fetch(
				`/index.php/apps/opencatalogi/api/publications/${publicationStore.publicationItem.id}`,
				{
					method: 'PUT',
					headers: {
						'Content-Type': 'application/json',
					},
					body: JSON.stringify({
						...bodyData,
						catalogi: bodyData.catalogi.id,
						metaData: bodyData.metaData.id,
					}),
				},
			)
				.then((response) => {
					this.loading = false
					this.success = response.ok

					// Lets refresh the publicationList
					publicationStore.refreshPublicationList()
					response.json().then((data) => {
						publicationStore.setPublicationItem(data)
					})

					// Wait for the user to read the feedback then close the model
					const self = this
					setTimeout(function() {
						self.success = null
						navigationStore.setModal(false)
					}, 2000)

					// reset modal form
					this.eigenschappen = {}
					this.data = ''
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

.form-group .group {
    margin-block-end: 2rem;
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
