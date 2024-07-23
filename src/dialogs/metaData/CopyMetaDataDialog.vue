<script setup>
import { UIStore, metadataStore } from '../../store/store.js'
</script>

<template>
	<NcDialog
		v-if="UIStore.dialog === 'copyMetaData'"
		name="Metadata kopieren"
		:can-close="false">
		<p v-if="!succes">
			Wil je <b>{{ metadataStore.metaDataItem.title ?? metadataStore.metaDataItem.name }}</b> kopieren?
		</p>
		<NcNoteCard v-if="succes" type="success">
			<p>Metadata succesvol gekopierd</p>
		</NcNoteCard>
		<NcNoteCard v-if="error" type="error">
			<p>{{ error }}</p>
		</NcNoteCard>
		<template #actions>
			<NcButton :disabled="loading" icon="" @click="UIStore.setDialog(false)">
				<template #icon>
					<Cancel :size="20" />
				</template>
				{{ succes ? 'Sluiten' : 'Annuleer' }}
			</NcButton>
			<NcButton
				v-if="!succes"
				:disabled="loading"
				type="primary"
				@click="CopyMetadata()">
				<template #icon>
					<NcLoadingIcon v-if="loading" :size="20" />
					<ContentCopy v-if="!loading" :size="20" />
				</template>
				Kopieren
			</NcButton>
		</template>
	</NcDialog>
</template>

<script>
import { NcButton, NcDialog, NcNoteCard, NcLoadingIcon } from '@nextcloud/vue'

import Cancel from 'vue-material-design-icons/Cancel.vue'
import ContentCopy from 'vue-material-design-icons/ContentCopy.vue'

export default {
	name: 'CopyMetaDataDialog',
	components: {
		NcDialog,
		NcButton,
		NcNoteCard,
		NcLoadingIcon,
		// Icons
		Cancel,
		ContentCopy,
	},
	data() {
		return {

			loading: false,
			succes: false,
			error: false,
		}
	},
	methods: {
		CopyMetadata() {
			this.loading = true
			metadataStore.metaDataItem.title = 'KOPIE: ' + metadataStore.metaDataItem.title
			if (Object.keys(metadataStore.metaDataItem.properties).length === 0) {
				delete metadataStore.metaDataItem.properties
			}
			delete metadataStore.metaDataItem.id
			delete metadataStore.metaDataItem._id
			fetch(
				'/index.php/apps/opencatalogi/api/metadata',
				{
					method: 'POST',
					headers: {
						'Content-Type': 'application/json',
					},
					body: JSON.stringify(metadataStore.metaDataItem),
				},
			)
				.then((response) => {
					this.loading = false
					this.succes = true
					// Lets refresh the catalogiList
					metadataStore.refreshMetaDataList()
					response.json().then((data) => {
						metadataStore.setMetaDataItem(data)
					})
					UIStore.setSelected('metaData')
					// Wait for the user to read the feedback then close the model
					const self = this
					setTimeout(function() {
						self.succes = false
						UIStore.setDialog(false)
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
