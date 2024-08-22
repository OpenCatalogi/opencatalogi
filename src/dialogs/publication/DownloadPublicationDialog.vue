<script setup>
import { navigationStore, publicationStore } from '../../store/store.js'
</script>

<template>
	<NcDialog
		v-if="navigationStore.dialog === 'downloadPublication'"
		name="Publicatie downloaden"
		:can-close="false">
		<p v-if="!succes">
			Hoe wilt u <b>{{ publicationStore.publicationItem.name ?? publicationStore.publicationItem.title }}</b> downloaden?
		</p>

		<div class="downloadButtonGroup">
			<NcButton
				v-if="!succes"
				:disabled="zipLoading || pdfLoading || true"
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
				v-if="!succes"
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
		<NcNoteCard v-if="succes" type="success">
			<p>Publicatie succesvol gearchiveerd</p>
		</NcNoteCard>
		<NcNoteCard v-if="error" type="error">
			<p>{{ error }}</p>
		</NcNoteCard>
		<template #actions>
			<NcButton :disabled="zipLoading || pdfLoading" icon="" @click="navigationStore.setDialog(false)">
				<template #icon>
					<Cancel :size="20" />
				</template>
				{{ succes ? 'Sluiten' : 'Annuleer' }}
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

			zipLoading: false,
			pdfLoading: false,
			succes: false,
			error: false,
		}
	},
	methods: {
		downloadPublication(type) {
			this.error = false

			if (type === 'pdf') { this.pdfLoading = true }
			if (type === 'zip') { this.zipLoading = true }

			fetch(
				`/index.php/apps/opencatalogi/api/publications/${publicationStore.publicationItem.id}/download`,
				{
					method: 'GET',
					headers: {
						Accept: `application/${type}`,
					},
				},
			)

				.then(res => res.blob())
				.then(blob => {
					const url = window.URL.createObjectURL(new Blob([blob]))
					const link = document.createElement('a')
					link.href = url

					link.setAttribute('download', `${publicationStore.publicationItem.title}.${type.toLowerCase()}`)
					document.body.appendChild(link)
					link.click()

					this.succes = true
					this.pdfLoading = false
					this.zipLoading = false
					const self = this

					setTimeout(function() {
						self.succes = false
						navigationStore.setDialog(false)
					}, 2000)
				})
				.catch((err) => {
					this.error = err
					this.pdfLoading = false
					this.zipLoading = false
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
