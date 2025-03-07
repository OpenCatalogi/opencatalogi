<script setup>
import { navigationStore, publicationStore } from '../../store/store.js'
</script>

<template>
	<NcDialog
		v-if="navigationStore.dialog === 'publishPublication'"
		name="Publicatie publiceren"
		:can-close="false">
		<div v-if="success !== null || error">
			<NcNoteCard v-if="success" type="success">
				<p>Publicatie succesvol gepubliceerd</p>
			</NcNoteCard>
			<NcNoteCard v-if="!success" type="error">
				<p>Er is iets fout gegaan bij het publiceren van Publicatie</p>
			</NcNoteCard>
			<NcNoteCard v-if="error" type="error">
				<p>{{ error }}</p>
			</NcNoteCard>
		</div>
		<p v-if="success === null">
			Wil je <b>{{ publicationStore.publicationItem.name ?? publicationStore.publicationItem.title }}</b> publiceren? Deze actie betekend dat de publicatie (en gepubliceerde bijlagen) worden opgenomen in de zoekindex en publiek toegankelijk zijn.
		</p>
		<template #actions>
			<NcButton :disabled="loading" icon="" @click="closeDialog()">
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
				@click="PublishPublication()">
				<template #icon>
					<NcLoadingIcon v-if="loading" :size="20" />
					<Publish v-if="!loading" :size="20" />
				</template>
				Publiceren
			</NcButton>
		</template>
	</NcDialog>
</template>

<script>
import { NcButton, NcDialog, NcNoteCard, NcLoadingIcon } from '@nextcloud/vue'

import Cancel from 'vue-material-design-icons/Cancel.vue'
import Publish from 'vue-material-design-icons/Publish.vue'
import { Publication } from '../../entities/index.js'

export default {
	name: 'PublishPublicationDialog',
	components: {
		NcDialog,
		NcButton,
		NcNoteCard,
		NcLoadingIcon,
		// Icons
		Cancel,
		Publish,
	},
	data() {
		return {
			loading: false,
			success: null,
			error: false,
		}
	},
	methods: {
		PublishPublication() {
			this.loading = true

			const publicationClone = { ...publicationStore.publicationItem }

			publicationClone.status = 'Published'

			const publicationItem = new Publication({
				...publicationClone,
				published: new Date().toISOString(),
				catalog: publicationClone.catalog.id ?? publicationClone.catalog,
				publicationType: publicationClone.publicationType,
			})

			publicationStore.editPublication(publicationItem)
				.then(({ response }) => {
					this.loading = false
					this.success = response.ok

					// Wait for the user to read the feedback then close the model
					setTimeout(function() {
						this.closeDialog()
					}, 2000)
				})
				.catch((err) => {
					this.error = err
					this.loading = false
				})
		},
		closeDialog() {
			this.success = null
			this.error = null
			navigationStore.setDialog(false)
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
