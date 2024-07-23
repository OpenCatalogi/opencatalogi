<script setup>
import { store } from '../../store/store.js'
</script>

<template>
	<NcModal v-if="store.modal === 'AddAttachment'" ref="modalRef" @close="store.setModal(false)">
		<div class="modal__content">
			<h2>Bijlage toevoegen</h2>
			<NcNoteCard v-if="succes" type="success">
				<p>Bijlage succesvol toegevoegd</p>
			</NcNoteCard>
			<NcNoteCard v-if="error" type="error">
				<p>{{ error }}</p>
			</NcNoteCard>
			<div v-if="!succes" class="form-group">
				<NcTextField :disabled="loading"
					label="Titel"
					maxlength="255"
					:value.sync="store.attachmentItem.title"
					required />
				<NcTextField :disabled="loading"
					label="Samenvatting"
					maxlength="255"
					:value.sync="store.attachmentItem.summary" />
				<NcTextArea :disabled="loading"
					label="Beschrijving"
					maxlength="255"
					:value.sync="store.attachmentItem.description" />
				<NcTextField :disabled="loading"
					label="Toegangs url"
					maxlength="255"
					:value.sync="store.attachmentItem.accessURL" />
				<NcTextField :disabled="loading"
					label="Download URL"
					maxlength="255"
					:value.sync="store.attachmentItem.downloadURL" />
			</div>
			<NcButton
				v-if="!succes"
				:disabled="!store.attachmentItem.title || loading"
				type="primary"
				@click="addAttachment()">
				<template #icon>
					<NcLoadingIcon v-if="loading" :size="20" />
					<Plus v-if="!loading" :size="20" />
				</template>
				Toevoegen
			</NcButton>
		</div>
	</NcModal>
</template>

<script>
import { NcButton, NcModal, NcTextField, NcTextArea, NcLoadingIcon, NcNoteCard } from '@nextcloud/vue'
import Plus from 'vue-material-design-icons/Plus.vue'

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
			succes: false,
			error: false,
		}
	},
	mounted() {
		store.setAttachmentItem([])
	},
	methods: {
		closeModal() {
			store.modal = false
		},
		addAttachment() {
			this.loading = true
			this.errorMessage = false
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
					if (store.publicationItem?.id) {
						store.getPublicationAttachments(store.publicationItem.id)
						// @todo update the publication item
					}
					// store.refreshCatalogiList()
					response.json().then((data) => {
						store.setAttachmentItem(data)
					})
					// Wait for the user to read the feedback then close the model
					const self = this
					setTimeout(function() {
						self.succes = false
						store.setModal(false)
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
