<script setup>
import { UIStore, publicationStore } from '../../store/store.js'
</script>
<template>
	<NcModal
		v-if="UIStore.modal === 'editPublication'"
		ref="modalRef"
		@close="UIStore.setModal(false)">
		<div class="modal__content">
			<h2>Edit publication</h2>
			<NcNoteCard v-if="succes" type="success">
				<p>Publicatie succesvol bewerkt</p>
			</NcNoteCard>
			<NcNoteCard v-if="error" type="error">
				<p>{{ error }}</p>
			</NcNoteCard>
			<div v-if="!succes" class="form-group">
				<NcTextField :disabled="loading"
					label="Naam"
					:value.sync="publicationStore.publicationItem.title" />
				<NcTextArea :disabled="loading" label="Beschrijving" :value.sync="publicationStore.publicationItem.description" />
				<NcTextField :disabled="loading"
					label="Categorie"
					:value.sync="publicationStore.publicationItem.category" />
				<NcTextField :disabled="loading"
					label="Publicatie"
					:value.sync="publicationStore.publicationItem.publication" />
				<NcTextField :disabled="loading"
					label="Portaal"
					:value.sync="publicationStore.publicationItem.portal" />
				<NcTextField :disabled="loading"
					label="Status"
					:value.sync="publicationStore.publicationItem.status" />
				<NcTextField :disabled="loading"
					label="Gepubliceerd"
					:value.sync="publicationStore.publicationItem.published" />
				<p>Featured</p>
				<NcCheckboxRadioSwitch :disabled="loading"
					label="Featured"
					:value.sync="publicationStore.publicationItem.featured" />
				<NcTextField :disabled="loading"
					label="Image"
					:value.sync="publicationStore.publicationItem.image" />
				<NcTextField :disabled="loading"
					label="Modified"
					:value.sync="publicationStore.publicationItem.modified" />
				<b>Juridisch</b>
				<NcTextField :disabled="loading"
					label="Licentie"
					:value.sync="publicationStore.publicationItem.license" />
				<b>Toegang</b>
				<NcSelectTags
					:value.sync="publicationStore.publicationItem.userGroups"
					input-label="Gebruikers groepen"
					:multiple="true" />
			</div>
			<NcButton
				v-if="!succes"
				:disabled="!publicationStore.publicationItem.title"
				type="primary"
				@click="updatePublication()">
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
	NcModal,
	NcTextField,
	NcTextArea,
	NcSelectTags,
	NcLoadingIcon,
	NcCheckboxRadioSwitch,
	NcNoteCard,
} from '@nextcloud/vue'
import ContentSaveOutline from 'vue-material-design-icons/ContentSaveOutline.vue'

export default {
	name: 'EditPublicationModal',
	components: {
		NcModal,
		NcTextField,
		NcTextArea,
		NcSelectTags,
		NcCheckboxRadioSwitch,
		NcButton,
		NcLoadingIcon,
		NcNoteCard,
		// Icons
		ContentSaveOutline,
	},
	data() {
		return {

			catalogi: {
				value: [],
				options: [],
			},
			metaData: {
				value: [],
				options: [],
			},
			loading: false,
			succes: false,
			error: false,
			catalogiLoading: false,
			metaDataLoading: false,
		}
	},
	mounted() {
		this.publication = publicationStore.publicationItem
	},
	updated() {
		if (UIStore.modal === 'publicationEdit' && this.hasUpdated) {
			if (this.publication.id === publicationStore.publicationItem.id) return
			this.hasUpdated = false
		}
		if (UIStore.modal === 'publicationEdit' && !this.hasUpdated) {
			this.fetchCatalogi()
			this.fetchMetaData()
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
						this.setSetPublictionsetPublicationItem(data)
					})
					this.loading = false
				})
				.catch((err) => {
					console.error(err)
					this.loading = false
				})
		},
		fetchCatalogi() {
			this.catalogiLoading = true
			fetch('/index.php/apps/opencatalogi/api/catalogi', {
				method: 'GET',
			})
				.then((response) => {
					response.json().then((data) => {

						const selectedCatalogi = Object.entries(data.results).find((catalogi) => catalogi[1]._id === this.publication.catalogi)

						this.catalogi = {
							inputLabel: 'Catalogi',
							options: Object.entries(data.results).map((catalog) => ({
								id: catalog[1]._id,
								label: catalog[1].name,
							})),
							value: {
								id: selectedCatalogi[1]._id ?? '',
								label: selectedCatalogi[1].name ?? '',
							},

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
						const selectedMetaData = Object.entries(data.results).find((metadata) => metadata[1]._id === this.publication.metaData)

						this.metaData = {
							inputLabel: 'MetaData',
							options: Object.entries(data.results).map((metaData) => ({
								id: metaData[1].id ?? metaData[1]._id,
								label: metaData[1].title ?? metaData[1].name,
							})),
							value: {
								id: selectedMetaData[1]._id ?? '',
								label: selectedMetaData[1].name ?? selectedMetaData[1].title ?? '',
							},
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
				`/index.php/apps/opencatalogi/api/publications/${publicationStore.publicationItem.id}`,
				{
					method: 'PUT',
					headers: {
						'Content-Type': 'application/json',
					},
					body: JSON.stringify(publicationStore.publicationItem),
				},
			)
				.then((response) => {
					this.loading = false
					this.succes = true
					// Lets refresh the catalogiList
					publicationStore.refreshPublicationList()
					response.json().then((data) => {
						publicationStore.setPublicationItem(data)
					})
					UIStore.setSelected('publication')
					setTimeout(() => UIStore.setModal(false), 2500)
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
