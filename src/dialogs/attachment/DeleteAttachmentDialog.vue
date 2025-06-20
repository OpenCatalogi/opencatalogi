<script setup>
import { navigationStore, objectStore, catalogStore } from '../../store/store.js'
</script>

<template>
	<NcDialog name="Bijlage verwijderen"
		:can-close="false">
		<p v-if="!succes">
			Wil je <b>{{ objectStore.getActiveObject('publicationAttachment')?.title }}</b> definitief verwijderen? Deze actie kan niet ongedaan worden gemaakt.
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
			loading: false,
			succes: false,
			error: false,
		}
	},
	methods: {
		DeleteAttachment() {
			this.loading = true

			fetch(`/index.php/apps/openregister/api/objects/${objectStore.getActiveObject('publication')['@self'].register}/${objectStore.getActiveObject('publication')['@self'].schema}/${objectStore.getActiveObject('publication').id}/files/${objectStore.getActiveObject('publicationAttachment').title}`, {
				method: 'DELETE',
			})
				.then((response) => {
					this.loading = false
					this.succes = response.status === 200

					catalogStore.getPublicationAttachments(objectStore.getActiveObject('publication').id, { page: objectStore.currentPage, limit: objectStore.limit })

					setTimeout(() => {
						navigationStore.setDialog(false)
					}, 2000)
				}).finally(() => {
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
