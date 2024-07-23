<script setup>
import { UIStore, publicationStore } from '../../store/store.js'
</script>

<template>
	<NcDialog
		v-if="UIStore.dialog === 'archivePublication'"
		name="Publicatie archiveren"
		:can-close="false">
		<p v-if="!succes">
			Wil je <b>{{ publicationStore.publicationItem.name ?? publicationStore.publicationItem.title }}</b> archiveren? Dit betekend dat de publicatie wordt de gepubliseerd en niet langer vindbaar is. Bij de eerste volgende gelegendheid wordt de publicatie <b>automatisch</b> over gebracht naar het digitaal archief.
		</p>
		<NcNoteCard v-if="succes" type="success">
			<p>Publicatie succesvol gearchiveerd</p>
		</NcNoteCard>
		<NcNoteCard v-if="error" type="error">
			<p>{{ error }}</p>
		</NcNoteCard>
		<template #actions>
			<NcButton :disabled="loading" icon="" @click="UIStore.setDialog(false)">
				<template #icon>
					<Cancel :size="20" />
				</template>
				{{ succes ? 'Sluiten' : 'Annuleer' }}
			</NcButton>
			<NcButton
				v-if="!succes"
				:disabled="loading"
				icon="Delete"
				type="primary"
				@click="ArchivePublication()">
				<template #icon>
					<NcLoadingIcon v-if="loading" :size="20" />
					<ArchivePlusOutline v-if="!loading" :size="20" />
				</template>
				Archiveren
			</NcButton>
		</template>
	</NcDialog>
</template>

<script>
import { NcButton, NcDialog, NcNoteCard, NcLoadingIcon } from '@nextcloud/vue'

import Cancel from 'vue-material-design-icons/Cancel.vue'
import ArchivePlusOutline from 'vue-material-design-icons/ArchivePlusOutline.vue'

export default {
	name: 'ArchivePublicationDialog',
	components: {
		NcDialog,
		NcButton,
		NcNoteCard,
		NcLoadingIcon,
		// Icons
		Cancel,
		ArchivePlusOutline,
	},
	data() {
		return {

			loading: false,
			succes: false,
			error: false,
		}
	},
	methods: {
		ArchivePublication() {
			this.loading = true
			publicationStore.publicationItem.status = 'archived'
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
					publicationStore.getConceptPublications()
					// Wait for the user to read the feedback then close the model
					const self = this
					setTimeout(function() {
						self.succes = false
						publicationStore.setPublicationItem(false)
						UIStore.setDialog(false)
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
