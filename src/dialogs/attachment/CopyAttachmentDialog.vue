<script setup>
import { store } from '../../store.js'
</script>

<template>
	<NcDialog
		v-if="store.dialog === 'copyAttachment'"
		name="Bijlage kopieren"
		:can-close="false">
		<p v-if="!succes">
			Wil je <b>{{ store.attachmentItem.name ?? store.attachmentItem.title }}</b> kopieren?
		</p>
		<NcNoteCard v-if="succes" type="success">
			<p>Bijlage succesvol gekopierd</p>
		</NcNoteCard>
		<NcNoteCard v-if="error" type="error">
			<p>{{ error }}</p>
		</NcNoteCard>
		<template #actions>
			<NcButton
				:disabled="loading"
				icon=""
				@click="store.setDialog(false)">
				<template #icon>
					<Cancel :size="20" />
				</template>
				{{ succes ? 'Sluiten' : 'Annuleer' }}
			</NcButton>
			<NcButton
				v-if="!succes"
				:disabled="loading"
				type="primary"
				@click="CopyAttachment()">
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
	name: 'CopyAttachmentDialog',
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
		CopyAttachment() {
			this.loading = true
			store.attachmentItem.title = 'KOPIE: ' + store.attachmentItem.title
			store.attachmentItem.status = 'concept'
			delete store.attachmentItem.id
			delete store.attachmentItem._id
			fetch(
				'/index.php/apps/opencatalogi/api/attachments',
				{
					method: 'POST',
					headers: {
						'Content-Type': 'application/json',
					},
					body: JSON.stringify(store.attachmentItem),
				},
			)
				.then((response) => {
					this.loading = false
					this.succes = true
					// Lets refresh the attachment list
					response.json().then((data) => {
						store.setAttachmentItem(data)
					})
					if (store.publicationItem?.id) {
						store.getPublicationAttachments(store.publicationItem.id)
						// @todo update the publication item
					}
					// Wait for the user to read the feedback then close the model
					const self = this
					setTimeout(function() {
						self.succes = false
						store.setAttachmentItem(false)
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
