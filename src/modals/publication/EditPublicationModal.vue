<script setup>
import { navigationStore, publicationStore } from '../../store/store.js'
</script>
<template>
	<NcDialog v-if="navigationStore.modal === 'editPublication'"
		name="Bewerk Publicatie"
		size="normal"
		:can-close="false">
		<div v-if="success !== null || error">
			<NcNoteCard v-if="success" type="success">
				<p>Publicatie succesvol bewerkt</p>
			</NcNoteCard>
			<NcNoteCard v-if="!success" type="error">
				<p>Er is iets fout gegaan bij het bewerken van Publicatie</p>
			</NcNoteCard>
			<NcNoteCard v-if="error" type="error">
				<p>{{ error }}</p>
			</NcNoteCard>
		</div>
		<div v-if="success === null" class="wrapper">
			<NcTextField :disabled="loading"
				label="Titel *"
				required
				:value.sync="publicationItem.title" />
			<NcTextField :disabled="loading"
				label="Samenvatting *"
				required
				:value.sync="publicationItem.summary" />
			<NcTextArea :disabled="loading"
				label="Beschrijving"
				:value.sync="publicationItem.description" />
			<NcTextField :disabled="loading"
				label="Kenmerk"
				:value.sync="publicationItem.reference" />
			<NcTextField :disabled="loading"
				label="Categorie"
				:value.sync="publicationItem.category" />
			<NcTextField :disabled="loading"
				label="Portaal"
				:value.sync="publicationItem.portal" />
			<p>Publicatie datum</p>
			<NcDateTimePicker v-model="publicationItem.published"
				:disabled="loading"
				label="Publicatie datum" />
			<NcCheckboxRadioSwitch :disabled="loading"
				label="Featured"
				:checked.sync="publicationItem.featured">
				Featured
			</NcCheckboxRadioSwitch>
			<NcTextField :disabled="loading"
				label="Image"
				:value.sync="publicationItem.image" />
			<b>Juridisch</b>
			<NcTextField :disabled="loading"
				label="Licentie"
				:value.sync="publicationItem.license" />
		</div>
		<template #actions>
			<NcButton
				:disabled="loading"
				icon=""
				@click="navigationStore.setModal(false)">
				<template #icon>
					<Cancel :size="20" />
				</template>
				{{ succes ? 'Sluiten' : 'Annuleer' }}
			</NcButton>
			<NcButton v-if="success === null"
				:disabled="!publicationItem.title || !publicationItem.summary"
				type="primary"
				@click="updatePublication()">
				<template #icon>
					<NcLoadingIcon v-if="loading" :size="20" />
					<ContentSaveOutline v-if="!loading" :size="20" />
				</template>
				Opslaan
			</NcButton>
		</template>
	</NcDialog>
</template>

<script>
import {
	NcButton,
	NcCheckboxRadioSwitch,
	NcDateTimePicker,
	NcLoadingIcon,
	NcDialog,
	NcNoteCard,
	NcTextArea,
	NcTextField,
} from '@nextcloud/vue'
import ContentSaveOutline from 'vue-material-design-icons/ContentSaveOutline.vue'
import Cancel from 'vue-material-design-icons/Cancel.vue'

export default {
	name: 'EditPublicationModal',
	components: {
		NcDialog,
		NcTextField,
		NcTextArea,
		NcCheckboxRadioSwitch,
		NcDateTimePicker,
		NcButton,
		NcLoadingIcon,
		NcNoteCard,
		// Icons
		ContentSaveOutline,
		Cancel,
	},
	data() {
		return {
			publicationItem: {
				id: '',
				title: '',
				summary: '',
				description: '',
				reference: '',
				image: '',
				category: '',
				portal: '',
				featured: false,
				published: '',
				license: '',
				catalogi: '',
				metaData: '',
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
			catalogiLoading: false,
			metaDataLoading: false,
			hasUpdated: false,
		}
	},
	mounted() {
		// publicationStore.publicationItem can be false, so only assign publicationStore.publicationItem to publicationItem if its NOT false
		publicationStore.publicationItem && (this.publicationItem = publicationStore.publicationItem)
	},
	updated() {
		if (navigationStore.modal === 'editPublication' && this.hasUpdated) {
			if (this.publicationItem.id === publicationStore.publicationItem.id) return
			this.hasUpdated = false
		}
		if (navigationStore.modal === 'editPublication' && !this.hasUpdated) {
			publicationStore.publicationItem && (this.publicationItem = publicationStore.publicationItem)
			this.fetchData(publicationStore.publicationItem.id)
			this.hasUpdated = true
		}
	},
	methods: {
		fetchData(id) {
			this.loading = true
			fetch(
				`/index.php/apps/opencatalogi/api/publications/${id}`,
				{
					method: 'GET',
				},
			)
				.then((response) => {
					response.json().then((data) => {
						publicationStore.setPublicationItem(data)
						this.publicationItem = publicationStore.publicationItem
					})
					this.loading = false
				})
				.catch((err) => {
					console.error(err)
					this.loading = false
				})
		},
		updatePublication() {
			this.loading = true
			fetch(
				`/index.php/apps/opencatalogi/api/publications/${publicationStore.publicationItem.id}`,
				{
					method: 'PUT',
					headers: {
						'Content-Type': 'application/json',
					},
					body: JSON.stringify({
						...this.publicationItem,
						id: this.publicationItem.id.toString(),
					}),
				},
			)
				.then((response) => {
					this.loading = false
					this.success = response.ok
					// Lets refresh the catalogiList
					publicationStore.refreshPublicationList()
					response.json().then((data) => {
						publicationStore.setPublicationItem(data)
					})
					navigationStore.setSelected('publication')

					const self = this
					setTimeout(() => {
						self.success = null
						navigationStore.setModal(false)
					}, 2500)
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

.EPM-horizontal {
	display: flex;
	gap: 4px;
	align-items: flex-end;
	flex-wrap: wrap;
}

.wrapper {
	display: flex;
	gap: 4px;
	align-items: flex-end;
	flex-wrap: wrap;
}
</style>
