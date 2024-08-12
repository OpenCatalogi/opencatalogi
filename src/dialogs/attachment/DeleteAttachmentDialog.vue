<script setup>
import { publicationStore, navigationStore } from '../../store/store.js'
</script>

<template>
	<NcDialog
		v-if="navigationStore.dialog === 'deleteAttachment'"
		name="Bijlage verwijderen"
		:can-close="false">
		<p v-if="!succes">
			Wil je <b>{{ publicationStore.attachmentItem.name ?? publicationStore.attachmentItem.title }}</b> definitief verwijderen? Deze actie kan niet ongedaan worden gemaakt.
		</p>
		<NcNoteCard v-if="succes" type="success">
			<p>Bijlage succesvol verwijderd</p>
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
				icon="Delete"
				type="error"
				@click="DeleteAttachment()">
				<template #icon>
					<NcLoadingIcon v-if="loading" :size="20" />
					<Delete v-if="!loading" :size="20" />
				</template>
				Verwijderen
			</NcButton>
		</template>
	</NcDialog>
</template>

<script>
import { NcButton, NcDialog, NcNoteCard, NcLoadingIcon } from '@nextcloud/vue'

import Cancel from 'vue-material-design-icons/Cancel.vue'
import Delete from 'vue-material-design-icons/Delete.vue'

export default {
	name: 'DeleteAttachmentDialog',
	components: {
		NcDialog,
		NcButton,
		NcNoteCard,
		NcLoadingIcon,
		// Icons
		Cancel,
		Delete,
	},
	data() {
		return {
			filterdAttachments: [],
			loading: false,
			succes: false,
			error: false,
		}
	},
	methods: {
		DeleteAttachment() {
			this.loading = true
			fetch(
				`/index.php/apps/opencatalogi/api/attachments/${publicationStore.attachmentItem.id}`,
				{
					method: 'DELETE',
					headers: {
						'Content-Type': 'application/json',
					},
				},
			)
				.then((response) => {
					this.loading = false
					this.succes = true
					// Lets refresh the attachment list
					if (publicationStore.publicationItem?.id) {
						publicationStore.getPublicationAttachments(publicationStore.publicationItem.id)
						// @todo update the publication item
					}

					this.filterdAttachments = publicationStore.publicationItem.attachments.filter((attachment) => { return parseInt(attachment) !== parseInt(publicationStore.attachmentItem.id) })

					fetch(
						`/index.php/apps/opencatalogi/api/publications/${publicationStore.publicationItem.id}`,
						{
							method: 'PUT',
							headers: {
								'Content-Type': 'application/json',
							},
							body: JSON.stringify({
								...publicationStore.publicationItem,
								attachments: [...this.filterdAttachments],
							}),
						},
					)
						.then((response) => {
							this.loading = false

							// Lets refresh the publicationList
							publicationStore.refreshPublicationList()
							response.json().then((data) => {
								publicationStore.setPublicationItem(data)
							})
						})
						.catch((err) => {
							this.error = err
							this.loading = false
						})

					// Wait for the user to read the feedback then close the model
					const self = this
					setTimeout(function() {
						self.succes = false
						publicationStore.setAttachmentItem(false)
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
