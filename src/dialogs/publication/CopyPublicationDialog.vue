<script setup>
import { useUIStore, usePublicationStore } from '../../store/store.js'
</script>

<template>
	<NcDialog
		v-if="UIStore.dialog === 'copyPublication'"
		name="Publicatie kopieren"
		:can-close="false">
		<p v-if="!succes">
			Wil je <b>{{ publicationStore.publicationItem.name ?? publicationStore.publicationItem.title }}</b> kopieren?
		</p>
		<NcNoteCard v-if="succes" type="success">
			<p>Publicatie succesvol gekopierd</p>
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
				type="primary"
				@click="CopyPublication()">
				<template #icon>
					<NcLoadingIcon v-if="loading" :size="20" />
					<ContentCopy v-if="!loading" :size="20" />
				</template>
				Kopieren
			</NcButton>
		</template>
	</NcDialog>
</template>

<script>
import { NcButton, NcDialog, NcNoteCard, NcLoadingIcon } from '@nextcloud/vue'

import Cancel from 'vue-material-design-icons/Cancel.vue'
import ContentCopy from 'vue-material-design-icons/ContentCopy.vue'

export default {
	name: 'CopyPublicationDialog',
	components: {
		NcDialog,
		NcButton,
		NcNoteCard,
		NcLoadingIcon,
		// Icons
		Cancel,
		ContentCopy,
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
		CopyPublication() {
			this.loading = true
			this.publicationStore.publicationItem.title = 'KOPIE: ' + this.publicationStore.publicationItem.title
			if (Object.keys(this.publicationStore.publicationItem.data).length === 0) {
				delete this.publicationStore.publicationItem.data
			}
			delete this.publicationStore.publicationItem.id
			delete this.publicationStore.publicationItem._id
			this.publicationStore.publicationItem.status = 'concept'
			fetch(
				'/index.php/apps/opencatalogi/api/publications',
				{
					method: 'POST',
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
					response.json().then((data) => {
						this.publicationStore.setPublicationItem(data)
					})
					this.UIStore.setSelected('publication')
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
