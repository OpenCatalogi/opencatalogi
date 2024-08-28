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
				label="Titel*"
				required
				:value.sync="publicationItem.title" />
			<NcTextField :disabled="loading"
				label="Samenvatting*"
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
				Uitgelicht
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
				@click="navigationStore.setModal(false)">
				<template #icon>
					<Cancel :size="20" />
				</template>
				{{ success ? 'Sluiten' : 'Annuleer' }}
			</NcButton>
			<NcButton
				@click="openLink('https://conduction.gitbook.io/opencatalogi-nextcloud/gebruikers/publicaties', '_blank')">
				<template #icon>
					<Help :size="20" />
				</template>
				Help
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
import Help from 'vue-material-design-icons/Help.vue'

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
		Help,
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
						id: this.publicationItem.id,
						catalogi: this.publicationItem.catalogi.id,
						metaData: this.publicationItem.metaData.id,
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
		openLink(url, type = '') {
			window.open(url, type)
		},
	},
}
</script>

<style>
.dialog__content {
  padding-top: 12px;
  padding-bottom: 12px;
}

</style>
