<script setup>
import { store } from '../../store/store.js'
</script>

<template>
	<NcDialog
		v-if="store.dialog === 'deleteMetaDataProperty'"
		name="Metadata eigenschap verwijderen"
		:can-close="false">
		<p v-if="!succes">
			Wil je <b>{{ store.metadataDataKey }}</b> definitief verwijderen? Deze actie kan niet ongedaan worden gemaakt.
		</p>
		<NcNoteCard v-if="succes" type="success">
			<p>Metadata eigenschap succesvol verwijderd</p>
		</NcNoteCard>
		<NcNoteCard v-if="error" type="error">
			<p>{{ error }}</p>
		</NcNoteCard>
		<template #actions>
			<NcButton :disabled="loading" icon="" @click="store.setDialog(false)">
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
				@click="DeleteProperty()">
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
	name: 'DeleteMetaDataPropertiesDialog',
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
		DeleteProperty() {
			delete store.metaDataItem.properties[store.metadataDataKey]

			this.loading = true
			fetch(
				`/index.php/apps/opencatalogi/api/metadata/${store.metaDataItem.id}`,
				{
					method: 'PUT',
					headers: {
						'Content-Type': 'application/json',
					},
					body: JSON.stringify(store.metaDataItem),
				},
			)
				.then((response) => {
					this.loading = false
					this.succes = true
					// Lets refresh the catalogiList
					store.refreshMetaDataList()
					response.json().then((data) => {
						store.setMetaDataItem(data)
					})
					store.setSelected('metaData')
					// Wait for the user to read the feedback then close the model
					const self = this
					setTimeout(function() {
						self.succes = false
						store.setDialog(false)
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
