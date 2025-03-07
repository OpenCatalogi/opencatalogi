<script setup>
import { navigationStore, publicationStore } from '../../store/store.js'
</script>

<template>
	<NcDialog
		v-if="navigationStore.dialog === 'downloadPublication'"
		name="Publicatie downloaden"
		:can-close="false">
		<div v-if="success !== null || error">
			<NcNoteCard v-if="success" type="success">
				<p>Publicatie succesvol gedownload</p>
			</NcNoteCard>
			<NcNoteCard v-if="!success" type="error">
				<p>Er is iets fout gegaan bij het downloaden van de publicatie</p>
			</NcNoteCard>
			<NcNoteCard v-if="error" type="error">
				<p>{{ error }}</p>
			</NcNoteCard>
		</div>
		<p v-if="success === null">
			Hoe wilt u <b>{{ publicationStore.publicationItem?.title }}</b> downloaden?
		</p>
		<div class="downloadButtonGroup">
			<NcButton
				v-if="success === null"
				:disabled="zipLoading || pdfLoading"
				icon="Delete"
				type="primary"
				@click="downloadPublication('zip')">
				<template #icon>
					<NcLoadingIcon v-if="zipLoading" :size="20" />
					<FolderZipOutline v-if="!zipLoading" :size="20" />
				</template>
				Download als ZIP
			</NcButton>
			<NcButton
				v-if="success === null"
				:disabled="zipLoading || pdfLoading"
				icon="Delete"
				type="primary"
				@click="downloadPublication('pdf')">
				<template #icon>
					<NcLoadingIcon v-if="pdfLoading" :size="20" />
					<FilePdfBox v-if="!pdfLoading" :size="20" />
				</template>
				Download als PDF
			</NcButton>
		</div>
		<template #actions>
			<NcButton :disabled="zipLoading || pdfLoading" icon="" @click="navigationStore.setDialog(false)">
				<template #icon>
					<Cancel :size="20" />
				</template>
				{{ success !== null ? 'Sluiten' : 'Annuleer' }}
			</NcButton>
		</template>
	</NcDialog>
</template>

<script>
import { NcButton, NcDialog, NcNoteCard, NcLoadingIcon } from '@nextcloud/vue'

import Cancel from 'vue-material-design-icons/Cancel.vue'
import FolderZipOutline from 'vue-material-design-icons/FolderZipOutline.vue'
import FilePdfBox from 'vue-material-design-icons/FilePdfBox.vue'

export default {
	name: 'DownloadPublicationDialog',
	components: {
		NcDialog,
		NcButton,
		NcNoteCard,
		NcLoadingIcon,
	},
	data() {
		return {
			success: null,
			error: false,
			pdfLoading: false,
			zipLoading: false,
		}
	},
	methods: {
		downloadPublication(type) {
			this.error = false

			if (type === 'pdf') {
				this.pdfLoading = true
			} else if (type === 'zip') {
				this.zipLoading = true
			}

			publicationStore.downloadPublication(
				publicationStore.publicationItem.id,
				publicationStore.publicationItem.title,
				type,
			)
				.then(({ response, download }) => {
					download()
					this.success = response.ok

					if (type === 'pdf') {
						this.pdfLoading = false
					} else if (type === 'zip') {
						this.zipLoading = false
					}

					const self = this
					setTimeout(function() {
						self.success = null
						navigationStore.setDialog(false)
					}, 2000)
				})
				.catch((err) => {
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

.downloadButtonGroup{
	display: flex;
	gap: 5px;
	justify-content: center;
}

.success {
    color: green;
}
</style>
