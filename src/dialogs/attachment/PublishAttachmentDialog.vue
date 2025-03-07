<script setup>
import { publicationStore, navigationStore } from '../../store/store.js'
</script>

<template>
	<NcDialog
		v-if="navigationStore.dialog === 'publishAttachment'"
		name="Bijlage publiceren"
		:can-close="false">
		<p v-if="!succes">
			Wil je <b>{{ publicationStore.attachmentItem?.name ?? publicationStore.attachmentItem?.title }}</b> publiceren?
		</p>
		<NcNoteCard v-if="succes" type="success">
			<p>Bijlage succesvol gepubliceerd</p>
		</NcNoteCard>
		<NcNoteCard v-if="error" type="error">
			<p>{{ error }}</p>
		</NcNoteCard>
		<template #actions>
			<NcButton
				:disabled="loading"
				icon=""
				@click="navigationStore.setDialog(false)">
				<template #icon>
					<Cancel :size="20" />
				</template>
				{{ succes ? 'Sluiten' : 'Annuleer' }}
			</NcButton>
			<NcButton
				v-if="!succes"
				:disabled="loading"
				type="primary"
				@click="PublishAttachment()">
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

import { Attachment } from '../../entities/index.js'

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
		PublishAttachment() {
			this.loading = true

			const attachmentClone = { ...publicationStore.attachmentItem }

			const now = new Date().toISOString()
			attachmentClone.published = now

			const attachmentItem = new Attachment(attachmentClone)

			publicationStore.editAttachment(attachmentItem)
				.then(({ response }) => {
					this.loading = false
					this.succes = response.ok

					if (publicationStore.publicationItem) {
						publicationStore.getPublicationAttachments(publicationStore.publicationItem?.id)
					}

					// Wait for the user to read the feedback then close the model
					const self = this
					setTimeout(function() {
						self.succes = false
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
