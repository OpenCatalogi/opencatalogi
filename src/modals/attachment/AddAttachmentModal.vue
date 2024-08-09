<script setup>
import { navigationStore, publicationStore } from '../../store/store.js'
</script>

<template>
	<NcModal v-if="navigationStore.modal === 'AddAttachment'"
		ref="modalRef"
		label-id="AddAttachmentModal"
		@close="navigationStore.setModal(false)">
		<div class="modal__content">
			<h2>Bijlage toevoegen</h2>
			<div v-if="success !== null || error">
				<NcNoteCard v-if="success" type="success">
					<p>Bijlage succesvol toegevoegd</p>
				</NcNoteCard>
				<NcNoteCard v-if="!success" type="error">
					<p>Er is iets fout gegaan bij het toevoegen van bijlage</p>
				</NcNoteCard>
				<NcNoteCard v-if="error" type="error">
					<p>{{ error }}</p>
				</NcNoteCard>
			</div>
			<div v-if="success === null" class="form-group">
				<NcTextField :disabled="(files && true) || loading"
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
					label="Toegangs URL"
					maxlength="255"
					:value.sync="publicationStore.attachmentItem.accessUrl" />
				<NcTextField :disabled="(files && true) || loading"
					label="Download URL"
					maxlength="255"
					:value.sync="publicationStore.attachmentItem.downloadUrl" />
				<div class="addFileButtonGroup">
					<NcButton v-if="success === null && !files"
						:disabled="checkIfDisabled() || loading"
						type="primary"
						@click="openFileUpload()">
						<template #icon>
							<Plus :size="20" />
						</template>
						Bestand toevoegen
					</NcButton>

					<NcButton v-if="success === null && files"
						:disabled="checkIfDisabled() || loading"
						type="primary"
						@click="reset()">
						<template #icon>
							<Minus :size="20" />
						</template>
						<span v-for="file of files" :key="file.name">{{ file.name }}</span>
					</NcButton>
				</div>
				<NcButton v-if="success === null"
					:disabled="loading"
					type="primary"
					@click="addAttachment()">
					<template #icon>
						<NcLoadingIcon v-if="loading" :size="20" />
						<Plus v-if="!loading" :size="20" />
					</template>
					Toevoegen
				</NcButton>
			</div>
		</div>
	</NcModal>
</template>

<script>
import { NcButton, NcLoadingIcon, NcModal, NcNoteCard, NcTextArea, NcTextField } from '@nextcloud/vue'
import { useFileDialog } from '@vueuse/core'

import Minus from 'vue-material-design-icons/Minus.vue'
import Plus from 'vue-material-design-icons/Plus.vue'

import axios from 'axios'

const { files, open: openFileUpload, reset } = useFileDialog()

export default {
	name: 'AddAttachmentModal',
	components: {
		NcModal,
		NcTextField,
		NcTextArea,
		NcButton,
		NcLoadingIcon,
		NcNoteCard,
		// Icons
		Plus,
	},
	data() {
		return {
			loading: false,
			success: null,
			error: false,
		}
	},
	mounted() {
		publicationStore.setAttachmentItem([])
	},
	methods: {
		closeModal() {
			navigationStore.modal = false
		},

		checkIfDisabled() {
			if (publicationStore.attachmentItem.downloadUrl || publicationStore.attachmentItem.title) return true
			return false
		},
		addAttachment() {
			this.loading = true
			this.errorMessage = false

			axios.post('/index.php/apps/opencatalogi/api/attachments', {
				...(publicationStore.attachmentItem),
				_file: files.value ? files.value[0] : '',
			}, {
				headers: {
					'Content-Type': 'multipart/form-data',
					// These headers are used to pass along some publication info to use as name for a Folder,
					// to store (attachments/) files in for that specific publication,
					'Publication-Id': publicationStore.publicationItem.id,
					'Publication-Title': publicationStore.publicationItem.title,
				},
			}).then((response) => {

				this.success = true
				reset()
				// Lets refresh the attachment list
				if (publicationStore.publicationItem?.id) {
					publicationStore.getPublicationAttachments(publicationStore.publicationItem.id)
					// @todo update the publication item
				}

				fetch(
					`/index.php/apps/opencatalogi/api/publications/${publicationStore.publicationItem.id}`,
					{
						method: 'PUT',
						headers: {
							'Content-Type': 'application/json',
						},
						body: JSON.stringify({
							...publicationStore.publicationItem,
							attachments: [...publicationStore.publicationItem.attachments, response.data.id],
						}),
					},
				)
					.then(() => {
						this.loading = false
					})
					.catch((err) => {
						this.error = err
						this.loading = false
					})
				// store.refreshCatalogiList()

				publicationStore.setAttachmentItem(response)

				// Wait for the user to read the feedback then close the model
				const self = this
				setTimeout(function() {
					self.success = null
					navigationStore.setModal(false)
				}, 2000)
			})
				.catch((err) => {
					this.error = err.response?.data?.error ?? err
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

.addFileButtonGroup{
	margin-block-end: var(--OC-margin-20);
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
