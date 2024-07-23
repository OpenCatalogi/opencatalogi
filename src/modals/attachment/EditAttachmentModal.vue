<script setup>
import { UIStore, publicationStore } from '../../store/store.js'
</script>

<template>
	<NcModal v-if="UIStore.modal === 'EditAttachment'" ref="modalRef" @close="UIStore.setModal(false)">
		<div class="modal__content">
			<h2>Bijlage bewerken</h2>
			<NcNoteCard v-if="succes" type="success">
				<p>Bijlage succesvol bewerkt</p>
			</NcNoteCard>
			<NcNoteCard v-if="error" type="error">
				<p>{{ error }}</p>
			</NcNoteCard>
			<div v-if="!succes" class="form-group">
				<NcTextField :disabled="loading"
					label="Titel"
					maxlength="255"
					:value.sync="publicationStore.attachmentItem.title"
					required />
				<NcTextField :disabled="loading"
					label="Samenvatting"
					maxlength="255"
					:value.sync="publicationStore.attachmentItem.summary" />
				<NcTextArea :disabled="loading"
					label="Beschrijving"
					maxlength="255"
					:value.sync="publicationStore.attachmentItem.description" />
				<NcTextField :disabled="loading"
					label="Toegangs url"
					maxlength="255"
					:value.sync="publicationStore.attachmentItem.accessURL" />
				<NcTextField :disabled="loading"
					label="Download URL"
					maxlength="255"
					:value.sync="publicationStore.attachmentItem.downloadURL" />
			</div>
			<NcButton
				v-if="!succes"
				:disabled="!publicationStore.attachmentItem.title || loading"
				type="primary"
				@click="editAttachment()">
				<template #icon>
					<NcLoadingIcon v-if="loading" :size="20" />
					<ContentSaveOutline v-if="!loading" :size="20" />
				</template>
				Opslaan
			</NcButton>
		</div>
	</NcModal>
</template>

<script>
import { NcButton, NcModal, NcTextField, NcTextArea, NcNoteCard, NcLoadingIcon } from '@nextcloud/vue'
import ContentSaveOutline from 'vue-material-design-icons/ContentSaveOutline.vue'

export default {
	name: 'EditAttachmentModal',
	components: {
		NcModal,
		NcTextField,
		NcTextArea,
		NcButton,
		NcNoteCard,
		NcLoadingIcon,
		// Icons
		ContentSaveOutline,
	},
	data() {
		return {

			loading: false,
			succes: false,
			error: false,
		}
	},
	methods: {
		editAttachment() {
			this.loading = true
			this.error = false
			fetch(
				`/index.php/apps/opencatalogi/api/attachments/${publicationStore.attachmentItem.id}`,
				{
					method: 'PUT',
					headers: {
						'Content-Type': 'application/json',
					},
					body: JSON.stringify(publicationStore.attachmentItem),
				},
			)
				.then((response) => {
					this.loading = false
					this.succes = true
					// Lets refresh the catalogiList
					if (publicationStore.publicationItem?.id) {
						publicationStore.getPublicationAttachments(publicationStore.publicationItem.id)
						// @todo update the publication item
					}
					response.json().then((data) => {
						publicationStore.setAttachmentItem(data)
					})
					// Wait for the user to read the feedback then close the model
					const self = this
					setTimeout(function() {
						self.succes = false
						UIStore.setModal(false)
					}, 2000)
				})
				.catch((err) => {
					this.loading = false
					this.error = err
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
