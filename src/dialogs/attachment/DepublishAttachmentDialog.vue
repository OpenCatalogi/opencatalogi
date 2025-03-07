<script setup>
import { publicationStore, navigationStore } from '../../store/store.js'
</script>

<template>
	<NcDialog
		v-if="navigationStore.dialog === 'depublishAttachment'"
		name="Bijlage depubliceren"
		:can-close="false">
		<p v-if="!succes">
			Wil je <b>{{ publicationStore.attachmentItem?.name ?? publicationStore.attachmentItem?.title }}</b> depubliceren?
		</p>
		<NcNoteCard v-if="succes" type="success">
			<p>Bijlage succesvol gedepubliceerd</p>
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
				@click="depublishAttachment()">
				<template #icon>
					<NcLoadingIcon v-if="loading" :size="20" />
					<PublishOff v-if="!loading" :size="20" />
				</template>
				Depubliceren
			</NcButton>
		</template>
	</NcDialog>
</template>

<script>
import { NcButton, NcDialog, NcNoteCard, NcLoadingIcon } from '@nextcloud/vue'

import Cancel from 'vue-material-design-icons/Cancel.vue'
import PublishOff from 'vue-material-design-icons/PublishOff.vue'

import { Attachment } from '../../entities/index.js'

export default {
	name: 'DepublishAttachmentDialog',
	components: {
		NcDialog,
		NcButton,
		NcNoteCard,
		NcLoadingIcon,
		// Icons
		Cancel,
		PublishOff,
	},
	data() {
		return {
			loading: false,
			succes: false,
			error: false,
		}
	},
	methods: {
		depublishAttachment() {
			this.loading = true

			const attachmentClone = { ...publicationStore.attachmentItem }

			attachmentClone.published = null

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
