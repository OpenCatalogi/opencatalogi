<script setup>
import { store } from '../../store.js'
</script>

<template>
	<NcDialog
		v-if="store.dialog === 'publishAttachment'"
		name="Bijlage publiseren"
		:can-close="false">
		<p v-if="!succes">
			Wil je <b>{{ store.attachmentItem.name ?? store.attachmentItem.title }}</b> publiseren?
		</p>
		<NcNoteCard v-if="succes" type="success">
			<p>Bijlage succesvol gepubliseerd</p>
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
	name: 'PublishAttachmentDialog',
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
			succes: false,
			error: false,
		}
	},
	methods: {
		CopyAttachment() {
			this.loading = true
			store.attachmentItem.status = 'published'
			fetch(
				`/index.php/apps/opencatalogi/api/attachments/${store.attachmentItem.id}`,
				{
					method: 'PUT',
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
