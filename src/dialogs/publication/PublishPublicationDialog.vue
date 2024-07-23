<script setup>
import { useUIStore, usePublicationStore } from '../../store/store.js'
</script>

<template>
	<NcDialog
		v-if="UIStore.dialog === 'publishPublication'"
		name="Publicatie publiseren"
		:can-close="false">
		<p v-if="!succes">
			Wil je <b>{{ publicationStore.publicationItem.name ?? publicationStore.publicationItem.title }}</b> publiseren? Deze actie betekend dat de publicatie (en gepubliseerde bijlagen) worden opgenomen in de zoekindex en publiek toegankenlijk zijn.
		</p>
		<NcNoteCard v-if="succes" type="success">
			<p>Publicatie succesvol gepubliseerd</p>
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
				@click="PublishPublication()">
				<template #icon>
					<NcLoadingIcon v-if="loading" :size="20" />
					<Publish v-if="!loading" :size="20" />
				</template>
				Publiseren
			</NcButton>
		</template>
	</NcDialog>
</template>

<script>
import { NcButton, NcDialog, NcNoteCard, NcLoadingIcon } from '@nextcloud/vue'

import Cancel from 'vue-material-design-icons/Cancel.vue'
import Publish from 'vue-material-design-icons/Publish.vue'

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
			UIStore: useUIStore(),
			publicationStore: usePublicationStore(),
			loading: false,
			succes: false,
			error: false,
		}
	},
	methods: {
		PublishPublication() {
			this.loading = true
			this.publicationStore.publicationItem.status = 'published'
			fetch(
				`/index.php/apps/opencatalogi/api/publications/${this.publicationStore.publicationItem.id}`,
				{
					method: 'PUT',
					headers: {
						'Content-Type': 'application/json',
					},
					body: JSON.stringify(this.publicationStore.publicationItem),
				},
			)
				.then((response) => {
					this.loading = false
					this.succes = true
					// Lets refresh the catalogiList
					this.publicationStore.refreshPublicationList()
					this.publicationStore.getConceptPublications()
					// Wait for the user to read the feedback then close the model
					const self = this
					setTimeout(function() {
						self.succes = false
						this.publicationStore.setPublicationItem(false)
						this.UIStore.setDialog(false)
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
