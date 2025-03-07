<script setup>
import { navigationStore, publicationStore } from '../../store/store.js'
</script>

<template>
	<NcDialog
		v-if="navigationStore.dialog === 'archivePublication'"
		name="Publicatie archiveren"
		:can-close="false">
		<div v-if="success !== null || error">
			<NcNoteCard v-if="success" type="success">
				<p>Publicatie succesvol gearchiveerd</p>
			</NcNoteCard>
			<NcNoteCard v-if="!success" type="error">
				<p>Er is iets fout gegaan bij het archiveren van Publicatie</p>
			</NcNoteCard>
			<NcNoteCard v-if="error" type="error">
				<p>{{ error }}</p>
			</NcNoteCard>
		</div>
		<p v-if="success === null">
			Wil je <b>{{ publicationStore.publicationItem?.title }}</b> archiveren? Dit betekend dat de publicatie wordt de gepubliceerd en niet langer vindbaar is. Bij de eerste volgende gelegenheid wordt de publicatie <b>automatisch</b> over gebracht naar het digitaal archief.
		</p>
		<template #actions>
			<NcButton :disabled="loading" icon="" @click="navigationStore.setDialog(false)">
				<template #icon>
					<Cancel :size="20" />
				</template>
				{{ success !== null ? 'Sluiten' : 'Annuleer' }}
			</NcButton>
			<NcButton
				v-if="success === null"
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
import { Publication } from '../../entities/index.js'

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
			success: null,
			error: false,
		}
	},
	methods: {
		ArchivePublication() {
			this.loading = true
			publicationStore.publicationItem.status = 'Archived'

			const catalogiItem = new Publication({
				...publicationStore.publicationItem,
				catalog: publicationStore.publicationItem.catalog.id ?? publicationStore.publicationItem.catalog,
				publicationType: publicationStore.publicationItem.publicationType,
			})

			publicationStore.editPublication(catalogiItem)
				.then(({ response }) => {
					this.loading = false
					this.success = response.ok

					// Wait for the user to read the feedback then close the model
					const self = this
					setTimeout(function() {
						self.success = null
						navigationStore.setDialog(false)
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
