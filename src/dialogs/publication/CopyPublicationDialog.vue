<script setup>
import { store } from '../../store/store.js'
</script>

<template>
	<NcDialog
		v-if="store.dialog === 'copyPublication'"
		name="Publicatie kopieren"
		:can-close="false">
		<p v-if="!succes">
			Wil je <b>{{ store.publicationItem.name ?? store.publicationItem.title }}</b> kopieren?
		</p>
		<NcNoteCard v-if="succes" type="success">
			<p>Publicatie succesvol gekopierd</p>
		</NcNoteCard>
		<NcNoteCard v-if="error" type="error">
			<p>{{ error }}</p>
		</NcNoteCard>
		<template #actions>
			<NcButton :disabled="loading" icon="" @click="store.setDialog(false)">
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
			loading: false,
			succes: false,
			error: false,
		}
	},
	methods: {
		CopyPublication() {
			this.loading = true
			store.publicationItem.title = 'KOPIE: ' + store.publicationItem.title
			if (Object.keys(store.publicationItem.data).length === 0) {
				delete store.publicationItem.data
			}
			delete store.publicationItem.id
			delete store.publicationItem._id
			store.publicationItem.status = 'concept'
			fetch(
				'/index.php/apps/opencatalogi/api/publications',
				{
					method: 'POST',
					headers: {
						'Content-Type': 'application/json',
					},
					body: JSON.stringify(store.publicationItem),
				},
			)
				.then((response) => {
					this.loading = false
					this.succes = true
					// Lets refresh the catalogiList
					store.refreshPublicationList()
					response.json().then((data) => {
						store.setPublicationItem(data)
					})
					store.setSelected('publication')
					// Wait for the user to read the feedback then close the model
					const self = this
					setTimeout(function() {
						self.succes = false
						store.setPublicationItem(false)
						store.setDialog(false)
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
